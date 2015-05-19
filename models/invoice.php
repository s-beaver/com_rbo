<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/invproducts.php";
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

    $this->flds ["doc_cust"] = array ("type" => "string" );
    
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
    parent::readObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", array ("docId" => $docId ));
    $prod = new RbOInvProducts ($docId);
    $prod->readObject ();
    $this->buffer->doc_products = $prod->buffer;
    
    $cfg = new RboConfig ();
    $this->buffer->doc_firm_details = $cfg->firms [$this->buffer->doc_firm];
    $this->buffer->doc_manager_details = $cfg->managers [$this->buffer->doc_manager];
    $this->response = $this->oJson->encode ($this->buffer);
  }
  
  // =================================================================
  public function updateObject() {
    $docId = $this->buffer->docId;
    $doc_products = $this->buffer->doc_products;
    
    $this->buffer->modified_by = JFactory::getUser ()->username;
    $this->buffer->modified_on = RbOHelper::getCurrentTimeForDb ();
    
    foreach ( $doc_products as &$p ) {
      $p ["docId"] = $docId;
    }
    parent::updateObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbOInvProducts ($docId);
    $prod->deleteObject ();
    $prod->createObject ();
    $this->response = $this->response && $prod->response;
  }
  
  // =================================================================
  public function createObject() {
    $doc_products = $this->buffer->doc_products;
    
    $this->buffer->created_by = JFactory::getUser ()->username;
    $this->buffer->created_on = RbOHelper::getCurrentTimeForDb ();
    
    parent::createObject ();
    
    $docId = $this->buffer->docId;
    foreach ( $doc_products as &$p ) {
      $p ["docId"] = $docId;
    }
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbOInvProducts ($docId);
    $prod->createObject ();
    $this->response = $this->response && $prod->response;
  }
  
  // =================================================================
  public function deleteObject() {
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