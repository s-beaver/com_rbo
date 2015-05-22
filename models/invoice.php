<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/invproducts.php";
include_once "models/rbocust.php";
include_once "models/rbohelper.php";
include_once "configuration.php";
class RbOInvoice extends RbObject {
  
  // =================================================================
  public function __construct($parentKeyValue) {
    parent::__construct ($parentKeyValue);
    
    $this->table_name = "rbo_docs";
    $this->flds ["docId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["doc_num"] = array ("type" => "string" );
    $this->flds ["doc_date"] = array ("type" => "date" );
    $this->flds ["doc_type"] = array ("type" => "string" );
    $this->flds ["doc_status"] = array ("type" => "string" );
    $this->flds ["doc_base"] = array ("type" => "numeric" );
    
    $this->flds ["custId"] = array ("type" => "numeric" );
    
    $this->flds ["doc_sum"] = array ("type" => "numeric" );
    $this->flds ["doc_manager"] = array ("type" => "string" );
    $this->flds ["doc_firm"] = array ("type" => "string" );
    $this->flds ["doc_rem"] = array ("type" => "string" );
    
    $this->flds ["created_by"] = array ("type" => "string" );
    $this->flds ["created_on"] = array ("type" => "datetime" );
    $this->flds ["modified_by"] = array ("type" => "string" );
    $this->flds ["modified_on"] = array ("type" => "datetime" );
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  public function readObject() {
    $docId = $this->buffer->docId;
    $this->parentKeyValue = $docId;
    parent::readObject ();
    $custId = $this->buffer->custId;
    
    $prod = new RbOInvProducts ($docId);
    $prod->readObject ();
    $this->buffer->doc_products = $prod->buffer;
    
    $cust = new RbOCust ($custId);
    $cust->readObject ();
    $cust->buffer->cust_data = json_decode ($cust->buffer->cust_data);
    $this->buffer->doc_cust = $cust->buffer;
    
    $cfg = new RboConfig ();
    $this->buffer->doc_firm_details = $cfg->firms [$this->buffer->doc_firm];
    $this->buffer->doc_manager_details = $cfg->managers [$this->buffer->doc_manager];
    $this->response = $this->oJson->encode ($this->buffer);
  }
  
  // =================================================================
  public function updateObject() {
    $response = true;
    $docId = $this->buffer->docId;
    $custId = $this->buffer->custId;
    $this->parentKeyValue = $docId;
    $doc_products = $this->buffer->doc_products;
    $doc_cust = $this->buffer->doc_cust;
    $doc_cust ['cust_data'] = json_encode ($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
    
    $this->buffer->modified_by = JFactory::getUser ()->username;
    $this->buffer->modified_on = RbOHelper::getCurrentTimeForDb ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_cust", $doc_cust);
    $cust = new RbOCust ($custId);
    if ($custId > 0) {
      $cust->updateObject ();
      $response = $response && $cust->response;
    } elseif ($custId == - 1) {
      $custId = 0;
      $this->buffer->custId = 0;
    } else {
      $cust->createObject ();
      $this->buffer->custId = $cust->insertid;
    }
    
    foreach ( $doc_products as &$p ) {
      $p ["docId"] = $docId;
    }
    parent::updateObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbOInvProducts ($docId);
    $prod->deleteObject ();
    $prod->createObject ();
    $response = $response && $prod->response;
    
    $this->response = $this->response && $response;
  }
  
  // =================================================================
  public function createObject() {
    $response = true;
    $custId = $this->buffer->custId;
    $doc_products = $this->buffer->doc_products;
    $doc_cust = $this->buffer->doc_cust;
    $doc_cust ['cust_data'] = json_encode ($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
    
    $this->buffer->created_by = JFactory::getUser ()->username;
    $this->buffer->created_on = RbOHelper::getCurrentTimeForDb ();
    $this->buffer->doc_type = "счет";
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_cust", $doc_cust);
    $cust = new RbOCust ($custId);
    if ($custId > 0) {
      $cust->updateObject ();
      $response = $response && $cust->response;
    } elseif ($custId == - 1) {
      $custId = 0;
      $this->buffer->custId = 0;
    } else {
      $cust->createObject ();
      $this->buffer->custId = $cust->insertid;
    }
    
    parent::createObject ();
    
    $docId = $this->insertid;
    foreach ( $doc_products as &$p ) {
      $p ["docId"] = $docId;
    }
    
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbOInvProducts ($docId);
    $prod->createObject ();
    $response = $response && $prod->response;
    
    $this->response = $this->response && $response;
  }
  
  // =================================================================
  public function deleteObject() {
    $docId = $this->buffer->docId;
    $this->parentKeyValue = $docId;
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    try {
      $query->update ($db->quoteName ($this->table_name));
      $query->set ("doc_status='удален'");
      $query->where ($this->getWhereClause ());
      $db->setQuery ($query);
      $result = $db->execute ();
    } catch ( Exception $e ) {
      JLog::add (
          get_class ($this) . ":" . $e->getMessage () . " buffer=" . print_r ($this->buffer, true), 
          JLog::ERROR, 'com_rbo');
    }
    $this->response = $result;
  }
  
  // =================================================================
  public function printInv() {
  }
}