<?php
defined ('_JEXEC') or die ();

jimport ('joomla.application.component.controller');
class RbOController extends JControllerLegacy {
  private $user;
  public $user_id;
  function __construct($params) {
    parent::__construct ($params);
    
    $this->user = & JFactory::getUser ();
    $this->user_id = $this->user->id;
  }
  function display($cachable = false) {
    if ($this->user->guest) { // неверно! нельзя пускать всех авторизированных пользователей. Только админов
      $this->setRedirect ("/component/users/?view=login", "Вам необходимо авторизоваться");
    } else {}
    parent::display ($cachable);
  }
}

