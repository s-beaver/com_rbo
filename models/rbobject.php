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
   "modified_by" => "string","modified_on" => "datetime" 
 ); // ассоциативный массив системных полей таблицы
 public $buffer; /*
                  * Значения для загрузки в таблицу. Ассоциативный массив, или массив ассоциативных массивов
                  * Результат загрузки значений из таблицы. Ассоциативный массив, или массив ассоциативных массивов
                  */
 
 // =================================================================
 public function __construct() {
  $this->user_name = JFactory::getUser ()->username;
  $this->oJson = new Services_JSON ();
  $this->sysflds ["modified_by"] = $this->user_name;
 }
 
 // =================================================================
 public function getInputBuffer() {
  $input = JFactory::getApplication ()->input;
  $inp_str = $input->getString ($this->table_name); // Читаем то, что записано в буфере. Формат буфера - одно поле, совпадающее с именем таблицв в БД
  $buffMixed = $this->oJson->decode ($inp_str);
  $this->buffer = ( object ) $buffMixed;
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
 public function udateObject() {
 }
 
 // =================================================================
 public function createObject() {
 }
 
 // =================================================================
 public function deleteObject() {
 }
}
