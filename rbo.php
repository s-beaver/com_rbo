<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;

// Подключаем логирование.
JLog::addLogger(array('text_file' => 'com_rbo.php'), JLog::ALL, array('com_rbo'));
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('RbO');
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
$controller->redirect();