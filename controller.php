<?php
defined ('_JEXEC') or die ();
jimport ('joomla.application.component.controller');
include_once "library/rbohelper.php";
class RbOController extends JControllerLegacy {
  private $user;
  public $user_id;
  function __construct($params) {
    parent::__construct ($params);
    
    $this->user = & JFactory::getUser ();
    $this->user_id = $this->user->id;
  }
  function display($cachable = false) {
    RbOHelper::checkAccess ();
    parent::display ($cachable);
  }
}

