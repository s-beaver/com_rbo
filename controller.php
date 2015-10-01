<?php
defined ('_JEXEC') or die ();
jimport ('joomla.application.component.controller');
require_once "models/RboHelper.php";
class RbOController extends JControllerLegacy {
  private $user;
  public $user_id;
  function __construct($params) {
    parent::__construct ($params);
    
    $this->user = & JFactory::getUser ();
    $this->user_id = $this->user->id;
  }
  function display($cachable = false) {
    RbHelper::checkAccess ();
    parent::display ($cachable);
  }
}

