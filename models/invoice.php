<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/invproducts.php";
class RbOInvoice extends RbObject {
                   
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->input_parameter_name = "rbo_invoice";
  $this->flds ["invId"] = "";
  $this->flds ["inv_num"] = "";
  $this->flds ["inv_date"] = "";
  $this->flds ["inv_sum"] = "";
  $this->flds ["inv_status"] = "";
  $this->flds ["inv_rem"] = "";
  $this->flds ["inv_firm"] = "";
  $this->flds ["inv_manager"] = "";
  
  $this->getInputFlds (); // Получаем из input значения полей для массива flds
  $this->fillFldNamesAsArray (); // заполним массив названий полей flds_names
 }
 
 // =================================================================
 public function readObject() {
  $query = $this->db->getQuery (true);
  
  $query->select ($this->flds_names);
  $query->from ('rbo_invoices');
  $query->where ('invId=' . $this->buffer->invId);
  
  try {
   $this->db->setQuery ($query);
   $this->buffer = $this->db->loadObject (); 
  } catch ( Exception $e ) {
   JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
  }
  JLog::add (get_class ($this) . "->readObject inv=" . print_r ($this->buffer, true), JLog::DEBUG, 
    'com_rbo');

  $this->input->set('rbo_invoice_products', '{inv_num:'.$this->buffer->inv_num.'}');
  $prod = new RbOInvProducts();
  $prod->readObject();
  $this->buffer->inv_products = $prod->buffer;
  $this->response = $this->oJson->encode ($this->buffer);
  
  return;
  
  $q = "SELECT product_code, product_name, product_cnt, product_price, product_cnt*product_price " .
     "FROM rbo_invoices_products WHERE inv_num=" . $this->buffer->inv_num;
  $this->db->setQuery ($q);
  $this->buffer->inv_products = $this->db->loadRowList ();
  
  // $["inv_cust"]=array();
  // $["inv_products"]=array();
  
  // объединить в один элемент все и сохранить в ответ запроса
  
  $this->response = $this->oJson->encode ($this->buffer);
 }
 
 // =================================================================
 public function udateObject() {
  $q = "UPDATE rbo_invoices SET inv_num=" . $this->inv_num . ", inv_date=STR_TO_DATE('" .
     $this->inv_date . "','%d.%m.%Y'), inv_cust='" . $this->inv_cust . "', inv_sum='" .
     $this->inv_sum . "', inv_status='" . $this->inv_status . "', inv_rem='" . $this->inv_rem . "' " .
     "inv_firm='" . $this->inv_firm . "', inv_manager='" . $this->inv_text . "', modified_by='" .
     $this->modified_by . "', modified_on='" . $this->modified_on . "' " .
     "FROM rbo_invoices WHERE invId=" . $this->invId;
  
  $res ["result"] = $q;
  $this->response = $this->oJson->encode ($res);
  return;
  
  $this->db->setQuery ($q);
  $this->db->query ();
  
  $res ["result"] = "success";
  $this->response = $this->oJson->encode ($res);
 }
}



