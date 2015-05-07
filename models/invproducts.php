<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOInvProducts extends RbObject {
 
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->is_multiple = true;
  $this->table_name = "rbo_invoices_products";
  $this->flds ["productId"] = array("type"=>"numeric");
  $this->flds ["product_code"] = array("type"=>"string");
  $this->flds ["product_name"] = array("type"=>"string");
  $this->flds ["product_cnt"] = array("type"=>"numeric");
  $this->flds ["product_price"] = array("type"=>"numeric");
  $this->flds ["product_sum"] = array("type"=>"numeric","read_only"=>true, "formula"=>"product_cnt*product_price");
  $this->flds ["invId"] = array("type"=>"string","is_key"=>true);
  
  $this->getInputBuffer (); 
 }
}



