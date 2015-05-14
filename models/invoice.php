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
    
    $this->table_name = "rbo_invoices";
    $this->flds ["invId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["inv_num"] = array ("type" => "string" );
    $this->flds ["inv_date"] = array ("type" => "date" );
    $this->flds ["inv_sum"] = array ("type" => "numeric" );
    $this->flds ["inv_status"] = array ("type" => "string" );
    $this->flds ["inv_manager"] = array ("type" => "string" );
    $this->flds ["inv_cust"] = array ("type" => "string" );
    $this->flds ["inv_firm"] = array ("type" => "string" );
    $this->flds ["inv_rem"] = array ("type" => "string" );
    
    $this->flds ["created_by"] = array ("type" => "string" );
    $this->flds ["created_on"] = array ("type" => "datetime" );
    $this->flds ["modified_by"] = array ("type" => "string" );
    $this->flds ["modified_on"] = array ("type" => "datetime" );
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  public function readObject() {
    $invId = $this->buffer->invId;
    parent::readObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_invoices_products", array ("invId" => $invId ));
    $prod = new RbOInvProducts ($invId);
    $prod->readObject ();
    $this->buffer->inv_products = $prod->buffer;

    $cfg = new RboConfig();
    $this->buffer->inv_firm_details = $cfg->firms[$this->buffer->inv_firm];
    $this->buffer->inv_manager_details = $cfg->managers[$this->buffer->inv_manager];
    $this->response = $this->oJson->encode ($this->buffer);
  }
  
  // =================================================================
  public function updateObject() {
    $invId = $this->buffer->invId;
    $inv_products = $this->buffer->inv_products;
    
    $this->buffer->modified_by = JFactory::getUser ()->username;
    $this->buffer->modified_on = RbOHelper::getCurrentTimeForDb();
    
    foreach ( $inv_products as &$p ) {
      $p ["invId"] = $invId;
    }
    parent::updateObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_invoices_products", $inv_products);
    $prod = new RbOInvProducts ($invId);
    $prod->deleteObject ();
    $prod->createObject ();
    $this->response = $this->response && $prod->response;
  }
  
  // =================================================================
  public function createObject() {
    $inv_products = $this->buffer->inv_products;
    
    $this->buffer->created_by = JFactory::getUser ()->username;
    $this->buffer->created_on = RbOHelper::getCurrentTimeForDb();
    
    parent::createObject ();
    
    $invId = $this->buffer->invId;  
    foreach ( $inv_products as &$p ) {
      $p ["invId"] = $invId;
    }
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_invoices_products", $inv_products);
    $prod = new RbOInvProducts ($invId);
    $prod->createObject ();
    $this->response = $this->response && $prod->response;
  }
  
  // =================================================================
  public function deleteObject() {
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    try {
      $query->update ($db->quoteName ($this->table_name));
      $query->set ("inv_status='удален'");
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