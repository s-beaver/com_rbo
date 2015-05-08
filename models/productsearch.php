<?php
jimport ('etc.json_lib');
class RbOProductSearch {
  function getListBySubstr() {
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $query->select ("count(*)");
    $query->from ("SS_products");
    $query->where ("name LIKE '%" . $searchSubstr . "%'");
    $db->setQuery($query);
    $count = $db->loadResult ();
    
    $query->clear ();
    $query->select ("productID, name, price, product_code");
    $query->from ("SS_products");
    $query->where ("name LIKE '%" . $searchSubstr . "%'");
    
    try {
      $db->setQuery ($query, 0, 30);
      $buffer = $db->loadObjectList ();
      $res = new stdClass();
      $res->count=$count;
      $res->result=$buffer;
      echo json_encode ($res);
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
}

