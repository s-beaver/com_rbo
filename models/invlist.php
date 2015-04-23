<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
//robik.ru/ajax.php?option=com_rbo&OrderId=190&view=invlist&format=raw

jimport('joomla.application.component.modelitem');

class RbOModelInvList extends JModelItem
{
  public $user_id="";
  public $inv_list="";

  public function __construct ($params) {

    $aColumns = array( 'orderId', 'order_time', 'cust_lastname', 'order_answer' );
    parent::__construct($params);

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
    $q = "SELECT count(*) FROM rbo_orders ".$sWhere;
    $db->setQuery($q);
    $iTotalRecords = $db->loadResult();

    /*$q = "SELECT `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
       FROM ss_order $sWhere $sLimit";        */

    /*$q = "SELECT so.orderID, so.order_time, CONCAT(so.cust_lastname, ' ', so.cust_firstname) customer, "+
         " SUM(soc.Price * soc.Quantity) ordersum, so.order_answer FROM SS_orders so "+
         " LEFT JOIN SS_ordered_carts soc ON so.orderID = soc.orderID "+
         " GROUP BY so.orderID $sLimit";*/
         
    $q = "SELECT so.orderID, so.order_num, so.order_date, so.order_cust, so.order_sum, so.order_status FROM rbo_orders so ORDER BY orderID DESC $sLimit";

    $db->setQuery($q);
    $data_rows_assoc_list = $db->loadAssocList();
    $iTotalDisplayRecords = $db->getAffectedRows ();

    $s = '';
	
    $iCnt=0;
    foreach ($data_rows_assoc_list as $v) {
	if ($s!='') $s.=',';
	$s.='{"0":"<a class=aid_ href='.$v['orderID'].'>'.$v['order_num'].'</a>",';
        $s.='"1":"['.$v['order_date'].']",';
	$s.='"2":"'.$v['order_cust']  .'",';
	$s.='"3":"'.$v['order_sum']   .'",';
	$s.='"4":"'.$v['order_status']   .'",';
	$s.='"DT_RowId":"dtrid_'.$v['orderID'].'"}';
        $iCnt++;
    }             

    $this->inv_list = 
      '{"sEcho":'.$sEcho.
      ',"iTotalRecords":'.$iTotalRecords.
      ',"iTotalDisplayRecords":'.$iTotalRecords.
      ',"aaData":['.$s.']}';
 
  }

  public function getSelectList($current)
  {
    return $current;
    $option_list = array("заказано"=>"заказано","отменено"=>"отменено");
    $s = "";
    foreach ($option_list as $k=>$v) $s .= "<option value='$k'>$v</option>";
    return "<select id=sid_ value=$current>$s</select>";
  }

  public function getinvList()
  {
    return $this->inv_list;
  }

  
}

