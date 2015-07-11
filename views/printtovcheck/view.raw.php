<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
define ('RBO_PATH', realpath (dirname (__FILE__) . '/../..'));
include_once RBO_PATH . "/configuration.php";
define ('JRBO_PATH', str_replace (RbOConfig::$pathRemoveForJsScrips, '', RBO_PATH));

//http://robik.ru/index.php?option=com_rbo&view=printtovcheck&format=raw

class RbOViewPrintTovCheck extends JViewLegacy
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