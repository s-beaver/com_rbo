<?php
jimport ('etc.json_lib');
class RbOHelper {
  // =================================================================
  static function checkAccess() {
    $user = JFactory::getUser ();
    $can_access = !(array_search ("8", $user->groups) === false &&
         array_search ("7", $user->groups) === false && array_search ("6", $user->groups) === false);
    if (! $can_access) {
      JLog::add ("Доступ запрещен для " . $user->name, JLog::ERROR, 'com_rbo');
      echo ("Access denied for " . $user->name);
      header('Refresh: 3; URL=http://robik.ru/');
      exit;
    }
  }
  
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
    $currentTime = new JDate ("now", $tz);
    return $currentTime->format ('d.m.Y H:i:00', true); // https://php.net/manual/en/function.date.php время добавить - скорректировать timezone
  }
  
  // =================================================================
  static function getProductListBySubstr() {
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
}
