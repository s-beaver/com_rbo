<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOCust extends RbObject {
  
  // =================================================================
  public function __construct($parentKeyValue) {
    parent::__construct ($parentKeyValue);
    
    $this->table_name = "rbo_cust";
    $this->flds ["custId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["cust_name"] = array ("type" => "string" );
    $this->flds ["cust_fullname"] = array ("type" => "string" );
    $this->flds ["cust_email"] = array ("type" => "string" );
    $this->flds ["cust_data"] = array ("type" => "string" );
    $this->flds ["cust_phone"] = array ("type" => "string" );
    
    $this->flds ["created_by"] = array ("type" => "string" );
    $this->flds ["created_on"] = array ("type" => "datetime" );
    $this->flds ["modified_by"] = array ("type" => "string" );
    $this->flds ["modified_on"] = array ("type" => "datetime" );
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  public function getCustListBySubstr() { // перенести в rbocust.php
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $query->select ("count(*)");
    $query->from ("rbo_cust");
    $query->where ("cust_name LIKE '%" . $searchSubstr . "%'", "OR");
    $query->where ("cust_data LIKE '%" . $searchSubstr . "%'", "OR");
    $db->setQuery ($query);
    $count = $db->loadResult ();
    
    $query->clear ();
    $query->select ("custId, cust_name, cust_fullname, cust_email, cust_data, cust_phone");
    $query->from ("rbo_cust");
    $query->where ("cust_name LIKE '%" . $searchSubstr . "%'", "OR");
    $query->where ("cust_data LIKE '%" . $searchSubstr . "%'", "OR");
    
    try {
      $db->setQuery ($query, 0, 30);
      $buffer = $db->loadObjectList ();
      $res = new stdClass ();
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
    foreach ( $buffer as &$v ) {
      $v->cust_data = json_decode ($v->cust_data);
    }
    $res->count = $count;
    $res->result = $buffer;
    echo json_encode ($res);
  }
}



