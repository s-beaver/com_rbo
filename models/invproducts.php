<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOInvProducts extends RbObject {
 
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->input_parameter_name = "rbo_invoice_products";
  $this->flds ["product_code"] = "";
  $this->flds ["product_name"] = "";
  $this->flds ["product_cnt"] = "";
  $this->flds ["product_price"] = "";
  $this->flds ["product_cnt*product_price"] = ""; 
  $this->flds ["inv_num"] = "";
  
  $this->getInputFlds (); // Получаем из input значения полей для массива flds
  $this->fillFldNamesAsArray (); // заполним массив названий полей flds_names
 }
 
 // =================================================================
 public function readObject() {
  $query = $this->db->getQuery (true);
  
  $query->select ($this->flds_names);
  $query->from ('rbo_invoices_products');
  $query->where ('inv_num=' . $this->buffer->inv_num);
  
  try {
   $this->db->setQuery ($query);
   /*$this->buffer = $this->db->loadAssocList ();
   $this->buffer = $this->db->loadObjectList ();*/
   $this->buffer = $this->db->loadRowList ();
  } catch ( Exception $e ) {
   JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
  }
  JLog::add (get_class ($this) . "->readObject prods=" . print_r ($this->buffer, true), JLog::DEBUG, 
    'com_rbo');
  
  $this->response = $this->oJson->encode ($this->buffer);
 }
}



