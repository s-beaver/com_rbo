<?php
defined('_JEXEC') or die;

//http://robik.ru/index.php?option=com_rbo&view=printship&format=raw

jimport('joomla.application.component.view');
define ('RBO_PATH', realpath (dirname (__FILE__) . '/../..'));
require_once RBO_PATH . "/configuration.php";
define ('JRBO_PATH', str_replace (RbConfig::$pathRemoveForJsScrips, '', RBO_PATH));

class RbOViewPrintAct extends JViewLegacy
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