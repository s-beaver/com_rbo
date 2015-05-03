<?php
jimport ('etc.json_lib');
class RbObject {
 public $db;
 public $response = "";
 public $oJson;
 public $user_name = "";
 public $input;
 public $input_parameter_name = "";
 public $flds = array ();
 public $sysflds = array ("created_by" => "","created_on" => "","modified_by" => "",
   "modified_on" => "" 
 );
 public $flds_names = array ();
 public $buffer; /*
                  * Значения для загрузки в таблицу. Ассоциативный массив, или массив ассоциативных массивов
                  * Результат загрузки значений из таблицы. Ассоциативный массив, или массив ассоциативных массивов
                  */
 
 // =================================================================
 public function __construct() {
  $this->db = JFactory::getDBO ();
  $this->user_name = JFactory::getUser ()->username;
  $this->oJson = new Services_JSON ();
  $this->sysflds ["modified_by"] = $this->user_name;
  $this->input = JFactory::getApplication ()->input;
 }
 
 // =================================================================
 public function getInputFlds() {
  $inp_str = $this->input->getString ($this->input_parameter_name);
  $test = $this->oJson->decode ($inp_str);
  $this->buffer = (object)$test;
 }
 
 // =================================================================
 public function fillFldNamesAsArray() {
  foreach ( $this->flds as $fldname => $fldvalue ) {
   $this->flds_names [] = ( string ) $fldname;
  }
   foreach ( $this->sysflds as $fldname => $fldvalue ) {
   $this->flds_names [] = ( string ) $fldname;
  }
 }
 
 // =================================================================
 public function getResponse() {
  return $this->response;
 }
 
 // =================================================================
 public function readObject() {
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
