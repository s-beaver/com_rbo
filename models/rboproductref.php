<?php
include_once "models/rbobject.php";
class RbOProductRef extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->is_multiple = false;
    $this->setTableName("SS_products");
    $this->flds ["productId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["categoryID"] = array ("type" => "numeric" );
    $this->flds ["product_code"] = array ("type" => "string" );
    $this->flds ["name"] = array ("type" => "string" );
    $this->flds ["price"] = array ("type" => "numeric" );
    $this->flds ["list_price"] = array ("type" => "numeric" );
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  static function getProductListBySubstr() {
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $searchAr = split (" ", $searchSubstr);
    
    $query->clear ();
    $query->select ("productID, name, price, product_code, list_price");
    $query->from ("SS_products");
    foreach ( $searchAr as $v ) {
      $query->where ("LOWER(name) LIKE '%" . strtolower($v) . "%'");
    }
    
    try {
      $db->setQuery ($query, 0, 30);
      $buffer = $db->loadObjectList ();
      $count = $db->getAffectedRows ();
      $res = new stdClass ();
      $res->count = $count;
      $res->result = $buffer;
      echo json_encode ($res);
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
}

