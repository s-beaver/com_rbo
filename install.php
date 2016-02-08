<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if (!defined('_JDEFINES')) {
    define('RBO_PATH', realpath(dirname(__FILE__)));
    define('JPATH_BASE', realpath(dirname(__FILE__) . "/../.."));
    require_once JPATH_BASE . '/includes/defines.php';
}

class Com_RbOInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
        echo 'Проверка_Проверка_Проверка_Проверка_';
        echo '<p>' . JText::_('COM_RBO_DESCRIPTION') . '</p>';
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        echo 'Creating triggers.';
        try {
            $db = JFactory::getDBO();
            $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_insert.sql'));
            $result = $db->execute();
            echo ' Insert.';

            $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_delete.sql'));
            $result = $db->execute();
            echo ' Delete.';

            $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_update.sql'));
            $result = $db->execute();
            echo ' Update.';
        } catch (Exception $e) {
            echo "Ошибка при создании триггера after insert";
        }
    }
}