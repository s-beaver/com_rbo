<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOInvProducts extends RbObject {
 
 // =================================================================
 public function __construct() {
  parent::__construct ();
  
  $this->is_multiple = true;
  $this->table_name = "rbo_invoices_products";
  $this->flds ["product_code"] = "string";
  $this->flds ["product_name"] = "string";
  $this->flds ["product_cnt"] = "numeric";
  $this->flds ["product_price"] = "numeric";
  $this->flds ["product_cnt*product_price"] = "numeric,READ_ONLY"; // или добавить поле sum или поле только для чтения
  $this->flds ["inv_num"] = "string,KEY_FIELD";
  
  $this->getInputBuffer (); // Получаем из input значения полей для массива flds
 }
}



