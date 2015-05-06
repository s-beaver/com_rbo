<?php 

class RbOInvoiceList
{
  public $user_id="";
  public $inv_list="";

  public function __construct () {

    $aColumns = array( 'invId', 'inv_time', 'cust_lastname');

    $db = JFactory::getDBO();
    $this->user =& JFactory::getUser ();
    $this->user_id = $this->user->id; 
    $is_admin = !(array_search("8",$this->user->groups)===false && array_search("7",$this->user->groups)===false);

    $input = JFactory::getApplication()->input;
    $iDisplayStart = $input->getInt('iDisplayStart');
    $iDisplayLength = $input->getInt('iDisplayLength');
    $sEcho = $input->getString('sEcho');

    //Paging
    $sLimit = "";
    if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' ) {
      $sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
        intval( $_POST['iDisplayLength'] );
    }

    //Ordering
    /*$sOrder = "";
    if ( isset( $_POST['iSortCol_0'] ) ) {
      $sOrder = "ORDER BY  ";
      for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ ) {
      	if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" ) { 
      	  $sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
      	    ($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
      	}
      }

      $sOrder = substr_replace( $sOrder, "", -2 );
      if ( $sOrder == "ORDER BY" ) {
      	$sOrder = "";
      }
    }*/

    //Filtering
    $sWhere = $is_admin?"WHERE ":"WHERE phUserID=".$this->user->id;
    if ( isset($_POST['sSearch']) && $_POST['sSearch'] != "" ) {
      $sWhere .= " AND (";
      for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
      }
      $sWhere = substr_replace( $sWhere, "", -3 );
      $sWhere .= ')';
    }


    //Individual column filtering
/*    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
      if ( isset($_POST['bSearchable_'.$i]) && $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' ) {
        if ( $sWhere == "" ) {
          $sWhere = "WHERE ";
        }
        else {
          $sWhere .= " AND ";
        }
        $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
      }
    }*/

    $sWhere = $is_admin?"":"WHERE phUserID=".$this->user->id;
    $q = "SELECT count(*) FROM rbo_invoices ".$sWhere;
    $db->setQuery($q);
    $iTotalRecords = $db->loadResult();

    $q = "SELECT invID, inv_num, inv_date, inv_cust, inv_sum, inv_status FROM rbo_invoices ORDER BY invId DESC $sLimit";
    
    $db->setQuery($q);
    $data_rows_assoc_list = $db->loadAssocList();
    $iTotalDisplayRecords = $db->getAffectedRows ();

    $s = '';

    
    $iCnt=0;
    foreach ($data_rows_assoc_list as $v) {
	if ($s!='') $s.=',';
	$s.='{"0":"<a class=aid_ href='.$v['invID'].'>'.$v['inv_num'].'</a>",';
        $s.='"1":"'.JFactory::getDate($v['inv_date'])->toFormat('%d %b %Y (%a)').'",';
	$s.='"2":"'.$v['inv_cust']  .'",';
	$s.='"3":"'.$v['inv_sum']   .'",';
	$s.='"4":"'.$v['inv_status']   .'",';
	$s.='"DT_RowId":"dtrid_'.$v['invID'].'"}';
        $iCnt++;
    }             

    $this->inv_list = 
      '{"sEcho":'.$sEcho.
      ',"iTotalRecords":'.$iTotalRecords.
      ',"iTotalDisplayRecords":'.$iTotalRecords.
      ',"aaData":['.$s.']}';
 
  }

  public function getinvList()
  {
    return $this->inv_list;
  }

  
}

