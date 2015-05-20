<?php
class RbOInvoiceList {
  public $user_id = "";
  public $doc_list = "";
  public function __construct() {
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
    $query->order($db->quoteName ('rd.docId') . " DESC");
    $query->leftJoin (
        $db->quoteName ('rbo_cust', 'rc') . ' ON (' . $db->quoteName ('rd.custId') . ' = ' .
             $db->quoteName ('rc.custId') . ')');
    
    if (isset ($_POST ['iDisplayStart']) && $_POST ['iDisplayLength'] != '-1') {
      $db->setQuery ($query,intval ($_POST ['iDisplayStart']),intval ($_POST ['iDisplayLength']));
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
    
    $this->doc_list = '{"sEcho":' . $sEcho . ',"iTotalRecords":' . $iTotalRecords .
         ',"iTotalDisplayRecords":' . $iTotalRecords . ',"aaData":[' . $s . ']}';
  }
  public function getinvList() {
    return $this->doc_list;
  }
}

