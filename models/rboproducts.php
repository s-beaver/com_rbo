<?php
// jimport ('etc.json_lib');
include_once "models/rbobject.php";
class RbOProducts extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->is_multiple = true;
    $this->table_name = "rbo_docs_products";
    $this->flds ["docId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["productId"] = array ("type" => "numeric" );
    $this->flds ["product_code"] = array ("type" => "string" );
    $this->flds ["product_name"] = array ("type" => "string" );
    $this->flds ["product_cnt"] = array ("type" => "numeric" );
    $this->flds ["product_price"] = array ("type" => "numeric" );
    $this->flds ["product_sum"] = array ("type" => "numeric","read_only" => true,
        "formula" => "product_cnt*product_price" );
    $this->flds ["product_uom"] = array ("type" => "string","read_only" => true,"formula" => "'шт.'" );
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  static function getProductListBySubstr() {
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    /*
     * $query->select ("count(*)");
     * $query->from ("SS_products");
     * $query->where ("name LIKE '%" . $searchSubstr . "%'");
     * $db->setQuery ($query);
     * $count = $db->loadResult ();
     */
    
    $query->clear ();
    $query->select ("productID, name, price, product_code, list_price");
    $query->from ("SS_products");
    $query->where ("name LIKE '%" . $searchSubstr . "%'");
    
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



