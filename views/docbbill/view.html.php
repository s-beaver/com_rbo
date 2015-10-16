<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
define ('RBO_PATH', realpath (dirname (__FILE__) . '/../..'));
require_once RBO_PATH . "/configuration.php";
define ('JRBO_PATH', str_replace (realpath(dirname(__FILE__) . "/../../../.."), '', RBO_PATH));

class RbOViewDocBBill extends JViewLegacy
{
	public function display($tpl = null) 
	{
		if (count($errors = $this->get('Errors')))
		{
			foreach ($errors as $error)
			{
				JLog::add($error, JLog::ERROR, 'com_rbo');
			}
		}

		parent::display($tpl);
	}
}