<?php
include_once "models/rbobject.php";
class RbOCust extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->setTableName ("rbo_cust");
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
  static function updateOrCreateCustomer(& $custId, $doc_cust) {
    $doc_cust = ( object ) $doc_cust;
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_cust", $doc_cust);
    $cust = new RbOCust ($custId);
    if ($custId > 0) {
      if (! isset ($doc_cust) || ! isset ($doc_cust->cust_name) || $doc_cust->cust_name == '') return false;
      if ($cust->buffer->_cust_data_changed) {
        $cust->updateObject ();
      } else {
        $cust->response = true;
      }
    } elseif ($custId == - 1) {
      $custId = 0;
      $cust->response = true;
    } else {
      if (! isset ($doc_cust) || ! isset ($doc_cust->cust_name) || $doc_cust->cust_name != '') return true;
      $cust->createObject ();
      $custId = $cust->insertid;
    }
    return $cust->response;
  }
  
  // =================================================================
  public function getCustListBySubstr() {
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $query->clear ();
    $query->select ("custId, cust_name, cust_fullname, cust_email, cust_data, cust_phone");
    $query->from ("rbo_cust");
    $query->where ("cust_name LIKE '%" . $searchSubstr . "%'", "OR");
    // $query->where ("cust_data LIKE '%" . $searchSubstr . "%'", "OR");пока не хотим искать по данным
    
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
    $res->count = $db->getAffectedRows ();
    $res->result = $buffer;
    echo json_encode ($res);
  }
}



