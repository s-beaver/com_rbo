<?php
jimport ('etc.json_lib');
include_once "models/rbobject.php";
include_once "models/rboproducts.php";
include_once "models/rbocust.php";
include_once "../library/rbohelper.php";
include_once "configuration.php";
class RbODocument extends RbObject {
  public $readBaseDocument = true;
  
  // =================================================================
  public function __construct($keyValue, $readBaseDocument) {
    parent::__construct ($keyValue);
    
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
    
    if (! isset ($keyValue)) $this->keyValue = $this->buffer->docId;
    if (isset ($readBaseDocument)) $this->readBaseDocument = $readBaseDocument;
    else $this->readBaseDocument = true;
  }
  
  // =================================================================
  public function readObject() {
    parent::readObject ();
    $custId = $this->buffer->custId;
    $doc_base = $this->buffer->doc_base;
    
    if ($this->readBaseDocument) {
      if (!isset($doc_base)) $doc_base = 0;//иначе объект возъмет из буфера, а там ключ самого себя
      $doc_base_doc = new RbODocument ($doc_base, false);
      $doc_base_doc->readObject ();
      $this->buffer->doc_base_doc = $doc_base_doc->buffer;
    }
    
    $prod = new RbOProducts ($this->keyValue);
    $prod->readObject ();
    $this->buffer->doc_products = $prod->buffer;
    
    $cust = new RbOCust ($custId);
    $cust->readObject ();
    $cust->buffer->cust_data = json_decode ($cust->buffer->cust_data);
    $this->buffer->doc_cust = $cust->buffer;
    
    $cfg = new RboConfig ();
    $this->buffer->doc_firm_details = $cfg->firms [$this->buffer->doc_firm];
    $this->buffer->doc_manager_details = $cfg->managers [$this->buffer->doc_manager];
    $this->response = json_encode ($this->buffer, JSON_UNESCAPED_UNICODE);
    // $this->response = $this->oJson->encode ($this->buffer);
  }
  
  // =================================================================
  public function updateObject() {
    $response = true;
    $custId = $this->buffer->custId;
    $doc_products = $this->buffer->doc_products;
    $doc_cust = $this->buffer->doc_cust;
    // проверить если пустой массив, то не сохранять
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
      $p ["docId"] = $this->keyValue;
    }
    parent::updateObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbOProducts ($this->keyValue);
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
    // $this->buffer->doc_type = $this->docType;//надо передавать через буфер
    
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
    $prod = new RbOProducts ($docId);
    $prod->createObject ();
    $response = $response && $prod->response;
    
    $this->response = $this->response && $response;
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
  public function getDocList() { 
    $db = JFactory::getDBO ();
    
    $input = JFactory::getApplication ()->input;
    $iDisplayStart = $input->getInt ('iDisplayStart');
    $iDisplayLength = $input->getInt ('iDisplayLength');
    $sEcho = $input->getString ('sEcho');
    $doc_type = $input->getString ('doc_type');
    $sSearch = $input->getString ('sSearch');
    $sWhere = array ();
    $sWhere [] = $db->quoteName ('doc_type') . "='" . $doc_type . "'";
    if (isset ($sSearch) && $sSearch != "") $sWhere [] = $db->quoteName ('rc.cust_name') . " LIKE '%" .
         $sSearch . "%'";
    
    $query = $db->getQuery (true);
    
    $query->clear ();
    $query->select (
        array ("docId","doc_num","doc_date","rc.cust_name doc_cust","doc_sum","doc_status","doc_firm",
            "doc_manager" ));
    $query->from ($db->quoteName ('rbo_docs', 'rd'));
    $query->where ($sWhere);
    $query->order ($db->quoteName ('rd.docId') . " DESC");
    $query->leftJoin (
        $db->quoteName ('rbo_cust', 'rc') . ' ON (' . $db->quoteName ('rd.custId') . ' = ' .
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
    $res->iTotalDisplayRecords = $iTotalDisplayRecords;
    $res->aaData = $data_rows_assoc_list;
    $this->response = json_encode ($res);
  }
  
  // =================================================================
  function getNextDocNumber() {
    $currentTime = new JDate ();
    $year = $currentTime->format ('Y', false);
    
    try {
      $db = JFactory::getDBO ();
      $query = $db->getQuery (true);
      $query->select ("MAX(doc_num)");
      $query->from ("rbo_docs");
      $query->where ("doc_type='" . $this->buffer->doc_type . "'");
      $query->where ("DATE_FORMAT(doc_date,'%Y')=$year");
      $db->setQuery ($query);
      $newNumber = $db->loadResult ();
      $res = new stdClass ();
      $res->new_num = $newNumber + 1;
      $res->new_date = $currentTime->format ('d.m.Y', true);
      echo json_encode ($res);
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
}

