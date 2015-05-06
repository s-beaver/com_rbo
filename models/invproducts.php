<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOInvProducts extends RbObject {
 
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->is_multiple = true;
  $this->table_name = "rbo_invoices_products";
  $this->flds ["product_code"] = array("type"=>"string");
  $this->flds ["product_name"] = array("type"=>"string");
  $this->flds ["product_cnt"] = array("type"=>"numeric");
  $this->flds ["product_price"] = array("type"=>"numeric");
  $this->flds ["product_cnt*product_price"] = array("type"=>"numeric","read_only"=>true); // или добавить поле sum или поле только для чтения
  $this->flds ["invId"] = array("type"=>"string","is_key"=>true);
  
  $this->getInputBuffer (); 
 }
}



