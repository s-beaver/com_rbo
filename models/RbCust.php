<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";

class RbCust extends RbObject
{

    /**
     * RbCust constructor.
     * @param null $keyValue
     */
    public function __construct($keyValue = null)
    {
        parent::__construct($keyValue);

        $this->setTableName("rbo_cust");
        $this->flds ["custId"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["cust_name"] = array("type" => "string");
        $this->flds ["cust_fullname"] = array("type" => "string");
        $this->flds ["cust_email"] = array("type" => "string");
        $this->flds ["cust_data"] = array("type" => "string");
        $this->flds ["cust_phone"] = array("type" => "string");
        $this->flds ["cust_is_own_firm"] = array("type" => "numeric");
        $this->flds ["cust_rem"] = array("type" => "string");

        $this->flds ["created_by"] = array("type" => "string");
        $this->flds ["created_on"] = array("type" => "datetime");
        $this->flds ["modified_by"] = array("type" => "string");
        $this->flds ["modified_on"] = array("type" => "datetime");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->custId;
    }

    /**
     * Очень странный метод. Используется только в RbOpers, тогда как аналогичные действия в RbDocs производятся по-другому
     * @param $custId
     * @param $doc_cust
     */
    static function updateOrCreateCustomer(& $custId, $doc_cust)//todo исправить?
    {
        $doc_cust = ( object )$doc_cust;
        $input = JFactory::getApplication()->input;
        $input->set("rbo_cust", $doc_cust);
        $cust = new RbCust ($custId);
        if ($custId > 0) {
            if (!isset ($doc_cust) || !isset ($doc_cust->cust_name) || $doc_cust->cust_name == '') return;
            if ($cust->buffer->_cust_data_changed) {//_cust_data_changed - нигде не встречается???
                $cust->updateObject();
            } else {
                $cust->response = true;
            }
        } elseif ($custId == -1) {
            $custId = 0;
            $cust->response = true;
        } else {
            if (!isset ($doc_cust) || !isset ($doc_cust->cust_name) || $doc_cust->cust_name == '') return;
            $cust->createObject();
            $custId = $cust->insertid;
        }
    }

    /**
     * @return object
     */
    public function getCustListBySubstr()
    {
        $input = JFactory::getApplication()->input;
        $searchSubstr = $input->get("search", null, null);

        if (!is_string($searchSubstr) || strlen($searchSubstr) < 2) {
            return null;
        }
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->clear();
        //$query->select ("custId, cust_name, cust_is_own_firm, cust_fullname, cust_email, cust_data, cust_phone");
        $query->select($this->getFieldsForSelectClause());
        $query->from($this->table_name);
        $query->where("cust_name LIKE '%" . $searchSubstr . "%'", "OR");
        // $query->where ("cust_data LIKE '%" . $searchSubstr . "%'", "OR");пока не хотим искать по данным

        $res = new stdClass ();
        try {
            $db->setQuery($query, 0, 30);
            $buffer = $db->loadObjectList();
            foreach ($buffer as &$v) {
                $v->cust_data = json_decode($v->cust_data);
            }
            $res->count = $db->getAffectedRows();
            $res->result = $buffer;
        } catch (Exception $e) {
            $res->errorCode = 110;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список контрагентов";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
//        echo json_encode($res);
        return $res;
    }

    /**
     * @return object
     */
    static function getCustList()
    {
        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('start');
        $iDisplayLength = $input->getInt('length');
        $iDraw = $input->getString('draw');
        $aSearch = $input->get("search", null, "array");
        $searchSubstr = null;
        if (!is_null($aSearch)) {
            $searchSubstr = $aSearch["value"];
        }

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $custRef = new RbCust();
        $query->clear();
        $query->select($custRef->getFieldsForSelectClause());
        $query->from($db->quoteName($custRef->table_name, "rc"));
        $query->order($db->quoteName('rc.custId') . " DESC");

        $where = array();
        if (!empty ($searchSubstr)) {
            $searchAr = preg_split("/[\s,]+/", $searchSubstr);// split the phrase by any number of commas or space characters
            foreach ($searchAr as $v) {
                $where[] = "LOWER(cust_name) LIKE '%" . mb_strtolower($v) . "%'";
            }
        }
        if (count($where) > 0) $query->where($where);

        $res = new stdClass ();
        try {
            if (isset ($iDisplayStart) && $iDisplayLength != '-1') {
                $db->setQuery($query, intval($iDisplayStart), intval($iDisplayLength));
            } else {
                $db->setQuery($query);
            }

            $data_rows_assoc_list = $db->loadAssocList();

            $query->clear();
            $query->select('count(*)');
            $query->from($db->quoteName($custRef->table_name, "rc"));
            if (count($where) > 0) $query->where($where);
            $db->setQuery($query);
            $iRecordsTotal = $db->loadResult();

            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsTotal;
            $res->data = $data_rows_assoc_list;
//            echo json_encode($res);
        } catch (Exception $e) {
            $res->errorCode = 110;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список контрагентов";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }

}



