<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/invproducts.php";
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
    $this->buffer->inv_products = $prod->buffer; // получился объект, у которого свойство inv_products - не ассоциативный массив. Не согласовано. Наверное надо переходить на объект. См. datatables.net
    $this->response = $this->oJson->encode ($this->buffer);
  }
  
  // =================================================================
  public function updateObject() {
    $invId = $this->buffer->invId;
    $inv_products = $this->buffer->inv_products;
    foreach ( $inv_products as &$p ) {
      $p["invId"] = $invId;
    }
    parent::updateObject ();

    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_invoices_products", $inv_products);
    $prod = new RbOInvProducts ($invId);
    $prod->deleteObject();    
    $prod->createObject ();
    $this->response = $this->response && $prod->response;
  }
}