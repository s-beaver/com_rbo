<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class RbOViewRbO extends JViewLegacy
{
	protected $UserEMail;
	public function display($tpl = null) 
	{
		$this->UserEMail = $this->get('UserEMail');

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