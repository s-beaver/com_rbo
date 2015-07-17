<?php
include_once "models/rbobject.php";
class RbOProducts extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->is_multiple = true;
    $this->setTableName("rbo_docs_products");
    $this->flds ["docId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["productId"] = array ("type" => "numeric" );
    $this->flds ["product_code"] = array ("type" => "string" );
    $this->flds ["product_name"] = array ("type" => "string" );
    $this->flds ["product_cnt"] = array ("type" => "numeric" );
    $this->flds ["product_price"] = array ("type" => "numeric" );
    $this->flds ["product_sum"] = array ("type" => "numeric","read_only" => true,
        "formula" => "product_cnt*product_price" );
    $this->flds ["product_uom"] = array ("type" => "string","read_only" => true,
        "formula" => "'шт.'" );
    
    $this->getInputBuffer ();
  }
  
}