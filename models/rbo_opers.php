<?php
require_once "models/rbobject.php";
require_once "models/rbo_cust.php";
require_once "models/rbohelper.php";
class RbOpers extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->is_multiple = false;
    $this->setTableName ("rbo_opers"); 
    
    $this->flds ["operId"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["oper_type"] = array ("type" => "string" );
    $this->flds ["oper_date"] = array ("type" => "date" );
    $this->flds ["custId"] = array ("type" => "numeric" );
    $this->flds ["oper_firm"] = array ("type" => "string" );
    $this->flds ["productId"] = array ("type" => "numeric" );
    $this->flds ["product_code"] = array ("type" => "string" );
    $this->flds ["product_name"] = array ("type" => "string" );
    $this->flds ["product_price"] = array ("type" => "numeric" );
    $this->flds ["product_cnt"] = array ("type" => "numeric" );
    $this->flds ["oper_sum"] = array ("type" => "numeric" );
    $this->flds ["oper_manager"] = array ("type" => "string" );
    $this->flds ["oper_rem"] = array ("type" => "string" );

    $this->flds ["created_by"] = array ("type" => "string" );
    $this->flds ["created_on"] = array ("type" => "datetime" );
    $this->flds ["modified_by"] = array ("type" => "string" );
    $this->flds ["modified_on"] = array ("type" => "datetime" );
    
    $this->getInputBuffer ();
    if (! isset ($keyValue)) $this->keyValue = $this->buffer->operId;
  }
  
  // =================================================================
  public function readObject() {
    parent::readObject ();
    $custId = $this->buffer->custId;

    $cust = new RbOCust ($custId);
    $cust->readObject ();
    $cust->buffer->cust_data = json_decode ($cust->buffer->cust_data);
    $this->buffer->oper_cust = $cust->buffer;
    
    $this->response = json_encode ($this->buffer, JSON_UNESCAPED_UNICODE);
  }
  
  // =================================================================
  public function updateObject() {
    $response = true;
    $custId = $this->buffer->custId;
    $oper_cust = $this->buffer->oper_cust;
    $oper_cust ['cust_data'] = json_encode ($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
    $productId = $this->buffer->productId;
    
    $response = $response && RbOCust::updateOrCreateCustomer ($custId, $oper_cust);
    $this->buffer->custId = $custId; 
    
    $response = $response && RbOProducts::updateOrCreateProduct ($productId, $this->buffer);
    $this->buffer->productId = $productId; 

    $this->buffer->modified_by = JFactory::getUser ()->username;
    $this->buffer->modified_on = RbOHelper::getCurrentTimeForDb ();
    
    parent::updateObject ();
    
    $this->response = $this->response && $response;
  }
  
  // =================================================================
  public function createObject() {
    $response = true;
    $custId = $this->buffer->custId;
    $oper_cust = $this->buffer->oper_cust;
    $oper_cust ['cust_data'] = json_encode ($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
    $productId = $this->buffer->productId;
    
    $response = $response && RbOCust::updateOrCreateCustomer ($custId, $oper_cust);
    $this->buffer->custId = $custId; 
    
    $response = $response && RbOProducts::updateOrCreateProduct ($productId, $this->buffer);
    $this->buffer->productId = $productId; 
    
    $this->buffer->created_by = JFactory::getUser ()->username;
    $this->buffer->created_on = RbOHelper::getCurrentTimeForDb ();
    
    parent::createObject ();
    
    $this->response = $this->response && $response;
  }
  
  // =================================================================
  public function getOperList() {
    $db = JFactory::getDBO ();
    
    $input = JFactory::getApplication ()->input;
    $iDisplayStart = $input->getInt ('iDisplayStart');
    $iDisplayLength = $input->getInt ('iDisplayLength');
    $sEcho = $input->getString ('sEcho');
    $sSearch = $input->getString ('sSearch');
    $sWhere = array ();
    /*
     * $sWhere [] = $db->quoteName ('doc_type') . "='" . $doc_type . "'";
     * if (isset ($sSearch) && $sSearch != "") $sWhere [] = $db->quoteName ('rc.cust_name') . " LIKE '%" .
     * $sSearch . "%'";
     */
    
    $query = $db->getQuery (true);
    
    $query->clear ();
    $where = $this->getFieldsForSelectClause ('so');
    $where[] = "rc.cust_name cust_name";
    $query->select ($where);
    $query->from ($db->quoteName ($this->table_name, 'so'));
    // $query->where ($sWhere);
    $query->order ($db->quoteName ('so.oper_date') . " DESC");
    $query->leftJoin (
        $db->quoteName ('rbo_cust', 'rc') . ' ON (' . $db->quoteName ('so.custId') . ' = ' .
             $db->quoteName ('rc.custId') . ')');
    
    if (isset ($_POST ['iDisplayStart']) && $_POST ['iDisplayLength'] != '-1') {
      $db->setQuery ($query, intval ($iDisplayStart), intval ($iDisplayLength));
    } else {
      $db->setQuery ($query);
    }
    
    $data_rows_assoc_list = $db->loadAssocList ();
    $iTotalDisplayRecords = $db->getAffectedRows ();
    
    foreach ( $data_rows_assoc_list as &$v ) {
      $v ['doc_date'] = JFactory::getDate ($v ['doc_date'])->format ('d.m.y'); // https://php.net/manual/en/function.date.php
    }
    
    $res = new stdClass ();
    $res->sEcho = $sEcho;
    $res->iTotalRecords = $iTotalDisplayRecords;
    $res->iTotalDisplayRecords = $iTotalDisplayRecords + 100;
    $res->aaData = $data_rows_assoc_list;
    $this->response = json_encode ($res);
  }
}



