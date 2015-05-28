<?php
defined('_JEXEC') or die;

//http://robik.ru/index.php?option=com_rbo&view=printship&format=raw

jimport('joomla.application.component.view');

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