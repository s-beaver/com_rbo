<?php
jimport ('etc.json_lib');
class RbObject {
 public $oJson; // нужно выносить из класса
 public $is_multiple = false; // читать одну запись или несколько по идентификатору
 public $response = ""; // подготовленный ответ для отправки в ответ на ajax запрос
 /* public $table_row_id_fld_name = "";//название поля таблицы БД являющееся ключевым */
 public $table_name = ""; // наименование таблицы БД в которой хранится объект, по совместительству - название переменной для передачи данных через input буфер
 public $flds = array (); // ассоциативный массив, где ключ - название поле таблицы БД, а значение - тип поля из списка: numeric, string, datetime
 public $sysflds = array ("created_by" => "string","created_on" => "datetime",
   "modified_by" => "string","modified_on" => "datetime" ); // ассоциативный массив системных полей таблицы
 public $buffer; // Значения для загрузки в таблицу. Ассоциативный массив, или массив ассоциативных массивов. Результат загрузки значений из таблицы. Ассоциативный массив, или массив ассоциативных массивов
                 
 // =================================================================
 public function __construct() {
  $this->user_name = JFactory::getUser ()->username;
  $this->oJson = new Services_JSON ();
  $this->sysflds ["modified_by"] = $this->user_name;
 }
 
 // =================================================================
 public function getInputBuffer() {//должно приходить в виде массива
  $input = JFactory::getApplication ()->input;
  $inp_str = $input->get ($this->table_name, null, null); // Читаем то, что записано в буфере. Формат буфера - одно поле, совпадающее с именем таблицв в БД
  $this->buffer = ( object ) $inp_str;
 }
 
 // =================================================================
 public function fillFldNamesAsArray() {
  $flds_names = array ();
  foreach ( $this->flds as $fldname => $fldvalue ) {
   $flds_names [] = ( string ) $fldname;
  }
  foreach ( $this->sysflds as $fldname => $fldvalue ) {
   $flds_names [] = ( string ) $fldname;
  }
  return $flds_names;
 }
 
 // =================================================================
 public function createConditions() {
  /* Переберем все свойства объекта buffer, и сформируем строку для where */
  $buffProp = get_object_vars ($this->buffer);
  $condAr = array ();
  foreach ( $buffProp as $key => $value ) {
   $condAr [] = "$key=$value";
  }
  return $condAr;
 }
 
 // =================================================================
 public function getKeyField() {
  $buffProp = get_object_vars ($this->buffer);
  foreach ( $buffProp as $key => $value ) {
   if (strpos($value,"KEY_FIELD")>=0)  return $key; 
  }
  JLog::add (get_class ($this) . "->getKeyField() " . print_r ($this->buffer, true), JLog::ERROR, 'com_rbo');
  throw new Exception('Не найдено ключевое поле');
 }
 
 // =================================================================
 public function getResponse() {
  return $this->response;
 }
 
 // =================================================================
 public function readObject() {
  $db = JFactory::getDBO ();
  $query = $db->getQuery (true);
  
  $query->select ($this->fillFldNamesAsArray ());
  $query->from ($this->table_name);
  $query->where ($this->createConditions ());
  
  try {
   $db->setQuery ($query);
   if ($this->is_multiple) {
    $this->buffer = $db->loadRowList ();
   } else {
    $this->buffer = $db->loadObject ();
   }
  } catch ( Exception $e ) {
   JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
  }
  JLog::add (get_class ($this) . "->readObject " . print_r ($this->buffer, true), JLog::DEBUG, 
    'com_rbo');
 }
 
 // =================================================================
 public function updateObject() {
  //$query->queryBatch - транзакции
  $db = JFactory::getDBO ();
  $result = $db->updateObject($this->table_name, $this->buffer, $this->getKeyField());
  
/*  // Fields to update.
  $fields = array(
    $db->quoteName('profile_value') . ' = ' . $db->quote('Updating custom message for user 1001.'),
    $db->quoteName('ordering') . ' = 2'
  );
  
  // Conditions for which records should be updated.
  $conditions = array(
    $db->quoteName('user_id') . ' = 42',
    $db->quoteName('profile_key') . ' = ' . $db->quote('custom.message')
  );
  
  $query = $db->getQuery (true);
  $query->update ($this->table_name);
  $query->set ($fields);
  $query->where ($conditions);
  $db->setQuery($query);
  $result = $db->execute();*/

  
 }
 
 // =================================================================
 public function createObject() {
 }
 
 // =================================================================
 public function deleteObject() {
 }
}
