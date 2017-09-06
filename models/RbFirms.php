<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";
require_once "models/RbHelper.php";

class RbFirms extends RbObject
{

    /**
     * RbFirms constructor.
     * @param null $keyValue
     */
    public function __construct($keyValue = null)
    {
        parent::__construct($keyValue);

        $this->is_multiple = false;
        $this->setTableName("rbo_firms");

        $this->flds ["name"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["balance"] = array("type" => "numeric");
        $this->flds ["disabled"] = array("type" => "boolean");
        $this->flds ["balanceStart"] = array("type" => "numeric");
        $this->flds ["dateStart"] = array("type" => "datetime");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->operId;
    }

    /**
     * @return object
     */
    public function getFirmList()
    {
        $db = JFactory::getDBO();

        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('start', -1);
        $iDisplayLength = $input->getInt('length', -1);
        $iDraw = $input->getString('draw', 1);
        $aSearch = $input->get("search", null, "array");

        $query = $db->getQuery(true);

        $query->clear();
        $select = $this->getFieldsForSelectClause('fm');
        $query->select($select);
        $query->from($db->quoteName($this->table_name, 'fm'));

        if (isset ($iDisplayStart) && $iDisplayLength != '-1') {
            $db->setQuery($query, intval($iDisplayStart), intval($iDisplayLength));//$query->setQuery instead in joomla 3
        } else {
            $db->setQuery($query);
        }

        $res = new stdClass ();
        try {
            $data_rows_assoc_list = $db->loadAssocList();

            $query->clear();
            $query->select('count(*)');
            $query->from($db->quoteName($this->table_name, 'fm'));
            $db->setQuery($query);
            $iRecordsTotal = $db->loadResult();
            $iRecordsFiltered = $iRecordsTotal;

            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsFiltered;
            $res->data = $data_rows_assoc_list;
//            $this->response = json_encode($res);
//            echo $this->response;
        } catch (Exception $e) {
            $res->errorCode = 120;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список фирм";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }
}



