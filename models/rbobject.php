<?php
//jimport ('etc.json_lib');
class RbObject {
  public $is_multiple = false; // читать одну запись или несколько по идентификатору
  public $response = ""; // подготовленный ответ для отправки в ответ на ajax запрос
  public $table_name = ""; // наименование таблицы БД в которой хранится объект, по совместительству - название переменной для передачи данных через input буфер
  public $flds = array (); // ассоциативный массив, где ключ - название поле таблицы БД, а значение - тип поля из списка: numeric, string, datetime
                           // public $flds = array (); // ассоциативный массив системных полей таблицы
  public $buffer; // Значения для загрузки в таблицу. Ассоциативный массив, или массив ассоциативных массивов. Результат загрузки значений из таблицы. Ассоциативный массив, или массив ассоциативных массивов
  public $keyValue = 0; // если $is_multiple, то отсюда берется значение ключа для связи с родительской таблицей. Название ключа выбирается из flds
  public $insertid;
  
  // =================================================================
  public function __construct($keyValue) {
    $this->keyValue = $keyValue;
  }
  
  // =================================================================
  public function getInputBuffer() { // должно приходить в виде массива
    $input = JFactory::getApplication ()->input;
    $inp_str = $input->get ($this->table_name, null, null); // Читаем то, что записано в буфере. Формат буфера - одно поле, совпадающее с именем таблицы в БД
    $this->buffer = ( object ) $inp_str; // не всегда ведь?
  }
  
  // =================================================================
  public function getResponse() {
    return $this->response;
  }
  
  // =================================================================
  public function getFieldsForSelectClause() {
    $flds_names = array ();
    foreach ( $this->flds as $fldname => $fldvalue ) {
      if ($fldvalue ["formula"]) {
        $fldname = "(" . $fldvalue ["formula"] . ") as " . $fldname;
      }
      switch ($fldvalue ["type"]) {
        case "date" :
          {
            $fldname = "DATE_FORMAT($fldname,'%d.%m.%Y') as $fldname"; // http://dev.mysql.com/doc/refman/5.6/en/date-and-time-functions.html#function_date-format
            break;
          }
        case "datetime" :
          {
            $fldname = "DATE_FORMAT($fldname,'%d.%m.%Y %H:%i') as $fldname"; // http://dev.mysql.com/doc/refman/5.6/en/date-and-time-functions.html#function_date-format
            break;
          }
      }
      $flds_names [] = ( string ) $fldname;
    }
    return $flds_names;
  }
  
  // =================================================================
  public function getWhereClause() {
    foreach ( $this->flds as $fldname => $fldvalue ) {
      if ($fldvalue ["is_key"]) return "$fldname=" . $this->keyValue;
    }
    return; // ПРОБА!!!!!!!!!!!!!!!!!!
    
    if ($this->is_multiple) {
      foreach ( $this->flds as $fldname => $fldvalue ) {
        if ($fldvalue ["is_key"]) return "$fldname=" . $this->keyValue;
      }
    } else {
      $buffer = ( object ) $this->buffer;
      $buffProp = get_object_vars ($buffer);
      foreach ( $buffProp as $key => $value ) {
        if ($this->flds [$key] ["is_key"]) return "$key=$value";
      }
    }
    JLog::add (get_class ($this) . "->getWhereClause() " . print_r ($buffer, true), JLog::ERROR, 
        'com_rbo');
    throw new Exception ('Не найдено ключевое поле');
  }
  
  // =================================================================
  public function getKeyField() { // deprecated
    $buffProp = get_object_vars ($this->buffer);
    foreach ( $buffProp as $key => $value ) {
      if ($this->flds [$key] ["is_key"]) return $key;
    }
    JLog::add (get_class ($this) . "->getKeyField() " . print_r ($this->buffer, true), JLog::ERROR, 
        'com_rbo');
    throw new Exception ('Не найдено ключевое поле');
  }
  
  // =================================================================
  public function getSetForUpdateClause($db, $buffer) { // заменить на перебор flds вместо buffer (см. ниже)
    $buffer = ( object ) $buffer;
    
    $setFlds = get_object_vars ($buffer);
    $setAr = array ();
    foreach ( $setFlds as $key => $value ) {
      if (! isset ($this->flds [$key])) continue;
      if (is_array ($value) || is_object ($value)) continue;
      if ($this->flds [$key] ["read_only"]) continue;
      switch ($this->flds [$key] ["type"]) {
        case "string" :
          {
            $setAr [] = $db->quoteName ($key) . "=" . $db->quote ($value);
            break;
          }
        case "date" :
          {
            $setAr [] = $db->quoteName ($key) . "=STR_TO_DATE('$value','%d.%m.%Y')";
            break;
          }
        case "datetime" :
          {
            $setAr [] = $db->quoteName ($key) . "=STR_TO_DATE('$value','%d.%m.%Y %H:%i:00')";
            break;
          }
        default :
          {
            $setAr [] = $db->quoteName ($key) . "=$value";
          }
      }
    }
    return $setAr;
  }
  
  // =================================================================
  public function getArraysForInsert($db, $buffer) {
    $buffer = ( object ) $buffer;
    $flds_names = array ();
    $flds_vals = array ();
    foreach ( $this->flds as $fldname => $fldvalue ) {
      if ($fldvalue ["read_only"]) continue;
      if (! isset ($buffer->{$fldname})) continue;
      $flds_names [] = $db->quoteName ($fldname);
      switch ($fldvalue ["type"]) {
        case "string" :
          {
            $flds_vals [] = $db->quote ($buffer->{$fldname});
            break;
          }
        case "date" :
          {
            $flds_vals [] = "STR_TO_DATE('" . $buffer->{$fldname} . "','%d.%m.%Y')";
            break;
          }
        case "datetime" :
          {
            $flds_vals [] = "STR_TO_DATE('" . $buffer->{$fldname} . "','%d.%m.%Y %H:%i:00')";
            break;
          }
        case "numeric" :
          {
            if ($buffer->{$fldname} == "") $buffer->{$fldname} = 0;
            $flds_vals [] = $buffer->{$fldname};
            break;
          }
        default :
          {
            $flds_vals [] = $buffer->{$fldname};
          }
      }
    }
    return array ($flds_names,implode (",", $flds_vals) );
  }
  
  // =================================================================
  public function readObject() {
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $query->select ($this->getFieldsForSelectClause ());
    $query->from ($this->table_name);
    $query->where ($this->getWhereClause ());
    
    try {
      $db->setQuery ($query);
      if ($this->is_multiple) {
        $this->buffer = $db->loadObjectList (); // возвращает массив объектов
      } else {
        $this->buffer = $db->loadObject (); // возвращает объект
      }
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
  
  // =================================================================
  public function updateObject() {
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    try {
      if ($this->is_multiple) {
        $result = true;
        foreach ( $this->buffer as $key => $value ) {
          $query->clear ();
          $query->update ($db->quoteName ($this->table_name));
          $query->set ($this->getSetForUpdateClause ($db, $value));
          $query->where ($this->getWhereClause ());
          $db->setQuery ($query);
          $result = $result && $db->execute ();
        }
        if (! $result) throw new Exception ('Ошибка при обновлении записи в БД');
      } else {
        $query->update ($db->quoteName ($this->table_name));
        $query->set ($this->getSetForUpdateClause ($db, $this->buffer));
        $query->where ($this->getWhereClause ());
        $db->setQuery ($query);
        $result = $db->execute ();
      }
    } catch ( Exception $e ) {
      JLog::add (
          get_class ($this) . ":" . $e->getMessage () . " buffer=" . print_r ($this->buffer, true), 
          JLog::ERROR, 'com_rbo');
    }
    $this->response = $result;
  }
  
  // =================================================================
  public function createObject() {
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    try {
      if ($this->is_multiple) {
        $result = true;
        $this->insertid = array ();
        foreach ( $this->buffer as $key => $value ) {
          $query->clear ();
          $ins = $this->getArraysForInsert ($db, $value);
          $query->insert ($db->quoteName ($this->table_name));
          $query->columns ($ins [0]);
          $query->values ($ins [1]);
          $db->setQuery ($query);
          $result = $result && $db->execute ();
          $this->insertid [] = $db->insertid ();
        }
        if (! $result) throw new Exception ('Ошибка при создании записи в БД');
      } else {
        $ins = $this->getArraysForInsert ($db, $this->buffer);
        $query->insert ($db->quoteName ($this->table_name));
        $query->columns ($ins [0]);
        $query->values ($ins [1]);
        $db->setQuery ($query);
        $result = $db->execute ();
        $this->insertid = $db->insertid ();
      }
    } catch ( Exception $e ) {
      JLog::add (
          get_class ($this) . ":" . $e->getMessage () . " buffer=" . print_r ($this->buffer, true), 
          JLog::ERROR, 'com_rbo');
    }
    $this->response = $result;
  }
  
  // =================================================================
  public function deleteObject() {
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    $query->delete ($db->quoteName ($this->table_name));
    $query->where ($this->getWhereClause ());
    $db->setQuery ($query);
    $result = $db->execute ();
  }
}
