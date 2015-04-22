<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class RbOViewInvList extends JViewLegacy
{
	protected $invList;

	public function display($tpl = null) 
	{
		$this->invList = $this->get('InvList');
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