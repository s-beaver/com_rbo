<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/invproducts.php";
class RbOInvoice extends RbObject {
 
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->table_name = "rbo_invoices";
  $this->flds ["invId"] = "numeric";
  $this->flds ["inv_num"] = "string";
  $this->flds ["inv_date"] = "datetime";
  $this->flds ["inv_sum"] = "numeric";
  $this->flds ["inv_status"] = "string";
  $this->flds ["inv_rem"] = "string";
  $this->flds ["inv_firm"] = "string";
  $this->flds ["inv_manager"] = "string";
  
  $this->getInputBuffer (); // Получаем из input значения полей для массива flds
 }
 
 // =================================================================
 public function readObject() {
  parent::readObject();
  
  $input = JFactory::getApplication ()->input;
  $input->set ("rbo_invoices_products", "{inv_num:'" . $this->buffer->inv_num . "'}");
  $prod = new RbOInvProducts ();
  $prod->readObject ();
  $this->buffer->inv_products = $prod->buffer;
  $this->response = $this->oJson->encode ($this->buffer);
 }
 
 // =================================================================
 public function updateObject() {
  $db = JFactory::getDBO ();
  $q = "UPDATE rbo_invoices SET inv_num=" . $this->inv_num . ", inv_date=STR_TO_DATE('" .
     $this->inv_date . "','%d.%m.%Y'), inv_cust='" . $this->inv_cust . "', inv_sum='" .
     $this->inv_sum . "', inv_status='" . $this->inv_status . "', inv_rem='" . $this->inv_rem . "' " .
     "inv_firm='" . $this->inv_firm . "', inv_manager='" . $this->inv_text . "', modified_by='" .
     $this->modified_by . "', modified_on='" . $this->modified_on . "' " .
     "FROM rbo_invoices WHERE invId=" . $this->invId;
  
  $res ["result"] = $q;
  $this->response = $this->oJson->encode ($res);
  return;
  
  $db->setQuery ($q);
  $db->query ();
  
  $res ["result"] = "success";
  $this->response = $this->oJson->encode ($res);
 }
}


/*$q = "SELECT product_code, product_name, product_cnt, product_price, product_cnt*product_price " .
  "FROM rbo_invoices_products WHERE inv_num=" . $this->buffer->inv_num;
$this->db->setQuery ($q);
$this->buffer->inv_products = $this->db->loadRowList ();
$this->response = $this->oJson->encode ($this->buffer);
*/


