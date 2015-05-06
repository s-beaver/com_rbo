<?php
jimport ('etc.json_lib');
class RbObject {
  public $oJson; // нужно выносить из класса
  public $is_multiple = false; // читать одну запись или несколько по идентификатору
  public $response = ""; // подготовленный ответ для отправки в ответ на ajax запрос
  public $table_name = ""; // наименование таблицы БД в которой хранится объект, по совместительству - название переменной для передачи данных через input буфер
  public $flds = array (); // ассоциативный массив, где ключ - название поле таблицы БД, а значение - тип поля из списка: numeric, string, datetime
  public $sysflds = array (); // ассоциативный массив системных полей таблицы
  public $buffer; // Значения для загрузки в таблицу. Ассоциативный массив, или массив ассоциативных массивов. Результат загрузки значений из таблицы. Ассоциативный массив, или массив ассоциативных массивов
                  
  // =================================================================
  public function __construct() {
    $this->oJson = new Services_JSON ();
    $this->sysflds ["created_by"] = array ("type" => "string" );
    $this->sysflds ["created_on"] = array ("type" => "date" );
    $this->sysflds ["modified_by"] = array ("type" => "string" );
    $this->sysflds ["modified_on"] = array ("type" => "date" );
  }
  
  // =================================================================
  public function getInputBuffer() { // должно приходить в виде массива
    $input = JFactory::getApplication ()->input;
    $inp_str = $input->get ($this->table_name, null, null); // Читаем то, что записано в буфере. Формат буфера - одно поле, совпадающее с именем таблицы в БД
    $this->buffer = ( object ) $inp_str;
  }
  
  // =================================================================
  public function getResponse() {
    return $this->response;
  }
  
  // =================================================================
  public function getFieldsForSelectClause() {
    $flds_names = array ();
    foreach ( $this->flds as $fldname => $fldvalue ) {
      if ($fldvalue ["is_key"]) continue; // исключаем ключевое поле
      switch ($fldvalue ["type"]) {
        case "date" :
          {
            $fldname = "DATE_FORMAT($fldname,'%d.%m.%Y') as $fldname";//http://dev.mysql.com/doc/refman/5.6/en/date-and-time-functions.html#function_date-format
            break;
          }
        case "datetime" :
          {
            $fldname = "DATE_FORMAT($fldname,'%d.%m.%Y %H %i') as $fldname";//http://dev.mysql.com/doc/refman/5.6/en/date-and-time-functions.html#function_date-format
            break;
          }
      }
      $flds_names [] = ( string ) $fldname;
    }
    foreach ( $this->sysflds as $fldname => $fldvalue ) {
      $flds_names [] = ( string ) $fldname;
    }
    return $flds_names;
  }
  
  // =================================================================
  public function getWhereClause() {
    $buffProp = get_object_vars ($this->buffer);
    foreach ( $buffProp as $key => $value ) {
      if ($this->flds [$key] ["is_key"]) return "$key=$value";
    }
    JLog::add (get_class ($this) . "->getWhereClause() " . print_r ($this->buffer, true), 
        JLog::ERROR, 'com_rbo');
    throw new Exception ('Не найдено ключевое поле');
  }
  
  // =================================================================
  public function getKeyField() {
    $buffProp = get_object_vars ($this->buffer);
    foreach ( $buffProp as $key => $value ) {
      if ($this->flds [$key] ["is_key"]) return $key;
    }
    JLog::add (get_class ($this) . "->getKeyField() " . print_r ($this->buffer, true), JLog::ERROR, 
        'com_rbo');
    throw new Exception ('Не найдено ключевое поле');
  }
  
  // =================================================================
  public function prepareSetForUpdate($buffer) {
    $currentTime = new JDate ('now');
    $buffer ["modified_by"] = JFactory::getUser ()->username;
    $buffer ["modified_on"] = $currentTime->toFormat ('%d.%m.%Y');
    
    $setFlds = get_object_vars ($buffer);
    foreach ( $setFlds as $key => &$value ) {
      switch ($this->flds [$key] ["type"]) {
        case "string" :
          {
            $value = "'" . $value . "'"; // Надо пользоваться методами Joomla $db->quoteName(
            break;
          }
        case "date" :
          {
            $value = "STR_TO_DATE('$value','%d.%m.%Y')";
            break;
          }
        case "datetime" :
          {
            $value = "STR_TO_DATE('$value','%d.%m.%Y')"; // добавить время
            break;
          }
      }
    }
    return $setFlds;
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
        $this->buffer = $db->loadRowList (); // возвращает не ассоциативный массив - не согласовано с "единичным" запросом
      } else {
        $this->buffer = $db->loadObject (); // возвращает объект
      }
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
    // JLog::add (get_class ($this) . "->readObject " . print_r ($this->buffer, true), JLog::DEBUG, 'com_rbo');
    
    // постобработка или при запросе дату форматировать
  }
  
  // =================================================================
  public function updateObject() {
    $db = JFactory::getDBO ();
    
    $keyFld = $this->getKeyField ();
    try {
      if ($this->is_multiple) {
        $result = true;
        foreach ( $this->buffer as $key => $value ) {
          $result = $result && $db->updateObject ($this->table_name, 
              $this->prepareSetForUpdate ($value), $keyFld);
        }
        if (! $result) throw new Exception ('Ошибка при обновлении записи в БД');
      } else {
        $result = $db->updateObject ($this->table_name, $this->prepareSetForUpdate ($this->buffer), 
            $keyFld);
      }
    } catch ( Exception $e ) {
      JLog::add (
          get_class ($this) . ":" . $e->getMessage () . " buffer=" . print_r ($this->buffer, true), 
          JLog::ERROR, 'com_rbo');
    }
  }
  
  // =================================================================
  public function createObject() {
  }
  
  // =================================================================
  public function deleteObject() {
  }
}
