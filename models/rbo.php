<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class RbOModelRbO extends JModelItem
{
	private $user;
	public $user_email="";

	function __construct ($params) {
		parent::__construct($params);

		$this->user =& JFactory::getUser ();
		$this->user_email = $this->user->email;
	}

	public function getUserEMail() 
	{
		return $this->user_email;
	}

}
