<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

//http://robik.ru/index.php?option=com_rbo&view=printtorg12&format=raw

class RbOViewPrintTorg12 extends JViewLegacy
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