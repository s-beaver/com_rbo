<?php
require_once "models/RbObject.php";
require_once "models/RbDocsProducts.php";
require_once "models/RbProducts.php";
require_once "models/RboCust.php";
require_once "models/RboHelper.php";
require_once "configuration.php";
class RbDocs extends RbObject {
  public $readBaseDocument = true;
  
  // =================================================================
  public function __construct($keyValue, $readBaseDocument) {
    parent::__construct ($keyValue);
    
    $this->setTableName ("rbo_docs");
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
      if (! isset ($doc_base)) $doc_base = 0; // иначе объект возъмет из буфера, а там ключ самого себя
      $doc_base_doc = new RbDocs ($doc_base, false);
      $doc_base_doc->readObject ();
      $this->buffer->doc_base_doc = $doc_base_doc->buffer;
    }
    
    $prod = new RbDocsProducts ($this->keyValue);
    $prod->readObject ();
    $this->buffer->doc_products = $prod->buffer;
    
    $cust = new RbCust ($custId);
    $cust->readObject ();
    $cust->buffer->cust_data = json_decode ($cust->buffer->cust_data);
    $this->buffer->doc_cust = $cust->buffer;
    
    $firm = RbConfig::$firms [mb_strtoupper ($this->buffer->doc_firm,"UTF-8")];
    if (is_string ($firm ["copyof"]) && strlen ($firm ["copyof"]) > 0) {
      $firm = RbConfig::$firms [$firm ["copyof"]];
    }
    $this->buffer->doc_firm_details = $firm;
    $this->buffer->doc_manager_details = RbConfig::$managers [$this->buffer->doc_manager];
    $this->response = json_encode ($this->buffer, JSON_UNESCAPED_UNICODE);
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
    $this->buffer->modified_on = RbHelper::getCurrentTimeForDb ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_cust", $doc_cust);
    $cust = new RbCust ($custId);
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
      if (! ($p ["productId"] > 0)) { // создадим новый товар в справочнике
        $pRef = array ();
        $pRef ["productId"] = $p ["productId"];
        $pRef ["product_code"] = $p ["product_code"];
        $pRef ["name"] = $p ["product_name"];
        $pRef ["price"] = $p ["product_price"];
        // $pRef ["categoryID"] = ;
        // $pRef ["list_price"] = ;
        
        $input->set ("SS_products", $pRef);
        $prodRef = new RbProducts ();
        $prodRef->createObject ();
        $p ["productId"] = $prodRef->insertid;
      }
      $p ["docId"] = $this->keyValue;
    }
    parent::updateObject ();
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbDocsProducts ($this->keyValue);
    $prod->deleteObject ();
    $prod->createObject ();
    $response = $response && $prod->response;
    
    $this->response = $this->response && $response;
    /*
     * if ($this->response) {
     * RbOHelper::sendEMail ("документ изменен",
     * "Изменен документ. Детали: " . RbODocs::docBuffer2Str ($this->buffer));
     * }
     */
  }
  
  // =================================================================
  public function createObject() {
    $response = true;
    $custId = $this->buffer->custId;
    $doc_products = $this->buffer->doc_products;
    $doc_cust = $this->buffer->doc_cust;
    $doc_cust ['cust_data'] = json_encode ($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
    
    $this->buffer->created_by = JFactory::getUser ()->username;
    $this->buffer->created_on = RbHelper::getCurrentTimeForDb ();
    // $this->buffer->doc_type = $this->docType;//надо передавать через буфер
    
    $input = JFactory::getApplication ()->input;
    $input->set ("rbo_cust", $doc_cust);
    $cust = new RbCust ($custId);
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
      if (! ($p ["productId"] > 0)) { // создадим новый товар в справочнике
        $pRef = array ();
        $pRef ["productId"] = $p ["productId"];
        $pRef ["product_code"] = $p ["product_code"];
        $pRef ["name"] = $p ["product_name"];
        $pRef ["price"] = $p ["product_price"];
        // $pRef ["categoryID"] = ;
        // $pRef ["list_price"] = ;
        
        $input->set ("SS_products", $pRef);
        $prodRef = new RbProducts ();
        $prodRef->createObject ();
        $p ["productId"] = $prodRef->insertid;
      }
      $p ["docId"] = $docId;
    }
    
    $input->set ("rbo_docs_products", $doc_products);
    $prod = new RbDocsProducts ($docId);
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
          get_class () . ":" . $e->getMessage () . " buffer=" . print_r ($this->buffer, true),
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
        array ("docId","doc_num","doc_date","rc.cust_name doc_cust","doc_sum","doc_status",
            "doc_firm","doc_manager" ));
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
      JLog::add (get_class () . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
  
  // =================================================================
  function docBuffer2Str($buffer) {
    $s = "";
    $s .= "N " . $buffer->doc_num;
    $s .= " от " . $buffer->doc_date;
    $s .= " " . $buffer->doc_type;
    $s .= " " . $buffer->doc_status;
    $s .= " Сумма:" . $buffer->doc_sum;
    $s .= " " . $buffer->doc_manager;
    $s .= " " . $buffer->doc_firm;
    $s .= " " . $buffer->doc_rem;
    
    $s .= " Покупатель:" . $buffer->doc_cust->cust_name;
    
    return $s;
  }
}

