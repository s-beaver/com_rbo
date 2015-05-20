<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOCust extends RbObject {
 
 // =================================================================
 public function __construct($parentKeyValue) {
  parent::__construct ($parentKeyValue);
  
  $this->table_name = "rbo_cust";
  $this->flds ["custId"] = array("type"=>"numeric","is_key"=>true);
  $this->flds ["cust_name"] = array("type"=>"string");
  $this->flds ["cust_fullname"] = array("type"=>"string");
  $this->flds ["cust_email"] = array("type"=>"string");
  $this->flds ["cust_data"] = array("type"=>"string");
  $this->flds ["cust_phone"] = array("type"=>"string");
  
  $this->flds ["created_by"] = array ("type" => "string" );
  $this->flds ["created_on"] = array ("type" => "datetime" );
  $this->flds ["modified_by"] = array ("type" => "string" );
  $this->flds ["modified_on"] = array ("type" => "datetime" );
    
  $this->getInputBuffer (); 
 }
}



