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
  public function getInvList() {
    $user_id = "";
    $doc_list = "";
    $aColumns = array ('docId','doc_time','cust_lastname' );
    
    $db = JFactory::getDBO ();
    $this->user = & JFactory::getUser ();
    $this->user_id = $this->user->id;
    $is_admin = ! (array_search ("8", $this->user->groups) === false &&
         array_search ("7", $this->user->groups) === false);
    
    $input = JFactory::getApplication ()->input;
    $iDisplayStart = $input->getInt ('iDisplayStart');
    $iDisplayLength = $input->getInt ('iDisplayLength');
    $sEcho = $input->getString ('sEcho');
    
    // Paging
    $sLimit = "";
    if (isset ($_POST ['iDisplayStart']) && $_POST ['iDisplayLength'] != '-1') {
      $sLimit = "LIMIT " . intval ($_POST ['iDisplayStart']) . ", " .
           intval ($_POST ['iDisplayLength']);
    }
    
    // Ordering
    /*
     * $sOrder = "";
     * if ( isset( $_POST['iSortCol_0'] ) ) {
     * $sOrder = "ORDER BY ";
     * for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ ) {
     * if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" ) {
     * $sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
     * ($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
     * }
     * }
     * $sOrder = substr_replace( $sOrder, "", -2 );
     * if ( $sOrder == "ORDER BY" ) {
     * $sOrder = "";
     * }
     * }
     */
    
    // Filtering
    $sWhere = $is_admin ? "WHERE " : "WHERE phUserID=" . $this->user->id;
    if (isset ($_POST ['sSearch']) && $_POST ['sSearch'] != "") {
      $sWhere .= " AND (";
      for($i = 0; $i < count ($aColumns); $i ++) {
        $sWhere .= "`" . $aColumns [$i] . "` LIKE '%" . mysql_real_escape_string (
            $_POST ['sSearch']) . "%' OR ";
      }
      $sWhere = substr_replace ($sWhere, "", - 3);
      $sWhere .= ')';
    }
    
    // Individual column filtering
    /*
     * for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
     * if ( isset($_POST['bSearchable_'.$i]) && $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' ) {
     * if ( $sWhere == "" ) {
     * $sWhere = "WHERE ";
     * }
     * else {
     * $sWhere .= " AND ";
     * }
     * $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
     * }
     * }
     */
    
    $sWhere = $is_admin ? "" : "WHERE phUserID=" . $this->user->id;
    $q = "SELECT count(*) FROM rbo_docs " . $sWhere;
    $db->setQuery ($q);
    $iTotalRecords = $db->loadResult ();
    
    $query = $db->getQuery (true);
    $query->select (
        array ("docId","doc_num","doc_date","rc.cust_name doc_cust","doc_sum","doc_status",
            "doc_manager" ));
    $query->from ($db->quoteName ('rbo_docs', 'rd'));
    $query->where ($db->quoteName ('rd.doc_type') . "='счет'");
    $query->order ($db->quoteName ('rd.docId') . " DESC");
    $query->leftJoin (
        $db->quoteName ('rbo_cust', 'rc') . ' ON (' . $db->quoteName ('rd.custId') . ' = ' .
             $db->quoteName ('rc.custId') . ')');
    
    if (isset ($_POST ['iDisplayStart']) && $_POST ['iDisplayLength'] != '-1') {
      $db->setQuery ($query, intval ($_POST ['iDisplayStart']), intval ($_POST ['iDisplayLength']));
    } else {
      $db->setQuery ($query);
    }
    
    $data_rows_assoc_list = $db->loadAssocList ();
    $iTotalDisplayRecords = $db->getAffectedRows ();
    
    $s = '';
    $iCnt = 0;
    foreach ( $data_rows_assoc_list as $v ) {
      if ($s != '') $s .= ',';
      $s .= '{"doc_num":"' . $v ['doc_num'] . '",';
      $s .= '"docId":"' . $v ['docId'] . '",';
      $s .= '"doc_date":"' . JFactory::getDate ($v ['doc_date'])->format ('d M Y (D)') . '",'; // https://php.net/manual/en/function.date.php
      $s .= '"doc_cust":"' . $v ['doc_cust'] . '",';
      $s .= '"doc_sum":"' . $v ['doc_sum'] . '",';
      $s .= '"doc_status":"' . $v ['doc_status'] . '",';
      $s .= '"doc_manager":"' . $v ['doc_manager'] . '"}';
      $iCnt ++;
    }
    
    $this->response = '{"sEcho":' . $sEcho . ',"iTotalRecords":' . $iTotalRecords .
         ',"iTotalDisplayRecords":' . $iTotalRecords . ',"aaData":[' . $s . ']}';
  }
}

