<?php
include_once "models/rbobject.php";
class RbOpers extends RbObject {
  
  // =================================================================
  public function __construct($keyValue) {
    parent::__construct ($keyValue);
    
    $this->is_multiple = true;
    $this->table_name = "ss_opers";
    
    $this->flds ["sKey"] = array ("type" => "numeric","is_key" => true );
    $this->flds ["sOperType"] = array ("type" => "string" );
    $this->flds ["sDate"] = array ("type" => "date" );
    $this->flds ["sContragent"] = array ("type" => "string" );
    $this->flds ["sSklad1"] = array ("type" => "string" );
    $this->flds ["sSklad2"] = array ("type" => "string" );
    $this->flds ["sProductID"] = array ("type" => "numeric" );
    $this->flds ["sProductCode"] = array ("type" => "string" );
    $this->flds ["sProductName"] = array ("type" => "string" );
    $this->flds ["sPrice"] = array ("type" => "numeric" );
    $this->flds ["sCnt"] = array ("type" => "numeric" );
    $this->flds ["sSum"] = array ("type" => "numeric" );
    $this->flds ["sOperMan"] = array ("type" => "string" );
    $this->flds ["sAuthor"] = array ("type" => "string" );
    $this->flds ["sPayDate"] = array ("type" => "date" );
    $this->flds ["sCashPlace1"] = array ("type" => "string" );
    $this->flds ["sCashPlace2"] = array ("type" => "string" );
    $this->flds ["sPaySum"] = array ("type" => "numeric" );
    $this->flds ["sRem"] = array ("type" => "numeric" );
    $this->flds ["sLevel"] = array ("type" => "numeric" );
    /*
     * sAuthorTime datetime DEFAULT NULL,
     * sTZ varchar(5) DEFAULT NULL,
     */
    
    $this->getInputBuffer ();
  }
  
  // =================================================================
  public function getOperList() {
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
  }



