<?php
jimport ('etc.json_lib');
class RbOHelper {
  // =================================================================
  static function getTimeZone() {
    $userTz = JFactory::getUser ()->getParam ('timezone');
    $timeZone = JFactory::getConfig ()->getValue ('offset');
    if ($userTz) {
      $timeZone = $userTz;
    }
    return new DateTimeZone ($timeZone);
  }
  
  // =================================================================
  static function getCurrentTimeForDb() {
    $tz = self::getTimezone ();
    $currentTime = new JDate ("now",$tz);
    return $currentTime->format ('d.m.Y H:i:00', true); // https://php.net/manual/en/function.date.php время добавить - скорректировать timezone
  }
  
  // =================================================================
  static function getListBySubstr() {
    $input = JFactory::getApplication ()->input;
    $searchSubstr = $input->get ("search", null, null);
    
    if (! is_string ($searchSubstr) || strlen ($searchSubstr) < 2) {return;}
    $db = JFactory::getDBO ();
    $query = $db->getQuery (true);
    
    $query->select ("count(*)");
    $query->from ("SS_products");
    $query->where ("name LIKE '%" . $searchSubstr . "%'");
    $db->setQuery ($query);
    $count = $db->loadResult ();
    
    $query->clear ();
    $query->select ("productID, name, price, product_code");
    $query->from ("SS_products");
    $query->where ("name LIKE '%" . $searchSubstr . "%'");
    
    try {
      $db->setQuery ($query, 0, 30);
      $buffer = $db->loadObjectList ();
      $res = new stdClass ();
      $res->count = $count;
      $res->result = $buffer;
      echo json_encode ($res);
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
  
  // =================================================================
  static function getNextDocNumber($sDocType) {//счет, накл, акт
    $currentTime = new JDate ();
    $year = $currentTime->format ('Y', false);
    
    try {
      $db = JFactory::getDBO ();
      $query = $db->getQuery (true);
      $query->select ("MAX(doc_num)");
      $query->from ("rbo_docs");
      $query->where ("doc_type='$sDocType'");
      $query->where ("DATE_FORMAT(doc_date,'%Y')=$year");
      $db->setQuery ($query);
      $newNumber = $db->loadResult ();
      $res = new stdClass ();
      $res->new_num = $newNumber;
      $res->new_date = $currentTime->format('d.m.Y', true);
      echo json_encode ($res);
    } catch ( Exception $e ) {
      JLog::add (get_class ($this) . ":" . $e->getMessage (), JLog::ERROR, 'com_rbo');
    }
  }
}

