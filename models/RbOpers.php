<?php
require_once "models/RbObject.php";
require_once "models/RbCust.php";
require_once "models/RbHelper.php";

class RbOpers extends RbObject
{

    // =================================================================
    public function __construct($keyValue)
    {
        parent::__construct($keyValue);

        $this->is_multiple = false;
        $this->setTableName("rbo_opers");

        $this->flds ["operId"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["oper_type"] = array("type" => "string");
        $this->flds ["oper_date"] = array("type" => "date");
        $this->flds ["custId"] = array("type" => "numeric");
        $this->flds ["docId"] = array("type" => "numeric");
        $this->flds ["oper_firm"] = array("type" => "string");
        $this->flds ["productId"] = array("type" => "numeric");
        $this->flds ["product_code"] = array("type" => "string");
        $this->flds ["product_name"] = array("type" => "string");
        $this->flds ["product_price"] = array("type" => "numeric");
        $this->flds ["product_cnt"] = array("type" => "numeric");
        $this->flds ["oper_sum"] = array("type" => "numeric");
        $this->flds ["oper_manager"] = array("type" => "string");
        $this->flds ["oper_rem"] = array("type" => "string");

        $this->flds ["created_by"] = array("type" => "string");
        $this->flds ["created_on"] = array("type" => "datetime");
        $this->flds ["modified_by"] = array("type" => "string");
        $this->flds ["modified_on"] = array("type" => "datetime");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->operId;
    }

    // =================================================================
    public function readObject()
    {
        parent::readObject();
        $custId = $this->buffer->custId;

        $cust = new RbCust ($custId);
        $cust->readObject();
        $cust->buffer->cust_data = json_decode($cust->buffer->cust_data);
        $this->buffer->oper_cust = $cust->buffer;

        $this->response = json_encode($this->buffer, JSON_UNESCAPED_UNICODE);
    }

    // =================================================================
    public function updateObject()
    {
        $response = true;
        $custId = $this->buffer->custId;
        $oper_cust = $this->buffer->oper_cust;
        $oper_cust ['cust_data'] = json_encode($oper_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
        $productId = $this->buffer->productId;

        $response = $response && RbCust::updateOrCreateCustomer($custId, $oper_cust);
        $this->buffer->custId = $custId;

        $response = $response && RbProducts::updateOrCreateProduct($productId, $this->buffer);
        $this->buffer->productId = $productId;

        $this->buffer->modified_by = JFactory::getUser()->username;
        $this->buffer->modified_on = RbHelper::getCurrentTimeForDb();

        parent::updateObject();

        $this->response = $this->response && $response;
    }

    // =================================================================
    public function createObject()
    {
        $response = true;
        $custId = $this->buffer->custId;
        $oper_cust = $this->buffer->oper_cust;
        $oper_cust ['cust_data'] = json_encode($oper_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
        $productId = $this->buffer->productId;

        $response = $response && RbCust::updateOrCreateCustomer($custId, $oper_cust);
        $this->buffer->custId = $custId;

        $response = $response && RbProducts::updateOrCreateProduct($productId, $this->buffer);
        $this->buffer->productId = $productId;

        $this->buffer->created_by = JFactory::getUser()->username;
        $this->buffer->created_on = RbHelper::getCurrentTimeForDb();

        parent::createObject();

        $this->response = $this->response && $response;
    }

    // =================================================================
    public function getOperList()
    {
        $db = JFactory::getDBO();

        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('start', -1);
        $iDisplayLength = $input->getInt('length', -1);
        $iDraw = $input->getString('draw', 1);
        $aSearch = $input->get("search", null, "array");
        $sSearch = null;
        if (!is_null($aSearch)) {
            $sSearch = $aSearch["value"];
        }
        $sDateFilter = $input->getString('date_filter', "");
        $sTypeFilter = $input->getString('type_filter', "");

        $query = $db->getQuery(true);

        $query->clear();
        $select = $this->getFieldsForSelectClause('so');
        $select[] = "rc.cust_name cust_name";
        $query->select($select);
        $query->from($db->quoteName($this->table_name, 'so'));
        $query->order($db->quoteName('so.oper_date') . " DESC");

        $rboCustTableName = RbHelper::getTableName("rbo_cust");
        $query->leftJoin(
            $db->quoteName($rboCustTableName, 'rc') . ' ON (' . $db->quoteName('so.custId') . ' = ' .
            $db->quoteName('rc.custId') . ')');

        $query->where("so.oper_date>0");
        if (isset($sDateFilter) && $sDateFilter != "") {
            $query->where("DATE_FORMAT(so.oper_date,'%d.%m.%Y')='$sDateFilter'");
        }
        if (isset($sTypeFilter) && $sTypeFilter != "") {
            $query->where("so.oper_type='$sTypeFilter'");
        }
        $where = array();
        if (!empty ($sSearch)) {
            $searchAr = preg_split("/[\s,]+/", $sSearch);// split the phrase by any number of commas or space characters
            foreach ($searchAr as $v) {
                $where[] = "LOWER(so.product_name) LIKE '%" . strtolower($v) . "%'";
                $where[] = "LOWER(so.oper_rem) LIKE '%" . strtolower($v) . "%'";
            }
        }
        if (count($where) > 0) {
            $cond = implode(" OR ", $where);
            $query->where("(" . $cond . ")");
        }

        if (isset ($iDisplayStart) && $iDisplayLength != '-1') {
            $db->setQuery($query, intval($iDisplayStart), intval($iDisplayLength));//$query->setQuery instead in joomla 3
        } else {
            $db->setQuery($query);
        }
        $data_rows_assoc_list = $db->loadAssocList();

        foreach ($data_rows_assoc_list as &$v) {
            $v ['doc_date'] = JFactory::getDate($v ['doc_date'])->format('d.m.y'); // https://php.net/manual/en/function.date.php
        }

        $query->clear();
        $query->select('count(*)');
        $query->from($db->quoteName($this->table_name, 'so'));
        if (count($where) > 0) $query->where($where);
        $db->setQuery($query);
        $iRecordsTotal = $db->loadResult();
        $iRecordsFiltered = $iRecordsTotal;

        $res = new stdClass ();
        $res->draw = (integer)$iDraw;
        $res->recordsTotal = $iRecordsTotal;
        $res->recordsFiltered = $iRecordsFiltered;
        $res->data = $data_rows_assoc_list;
        $this->response = json_encode($res);
    }

    // =================================================================
    /** Получение отчета по операциям. Варианты запуска:
     * - Отчет доход+затраты урупненный report_type = income_costs
     * - Отчет детализированный по доходам или по затратам report_type = income / costs
     */
    public function getOperReport()
    {
        //$this->buffer->report_type
        //$this->buffer->year
        //$this->buffer->month
        //$this->buffer->cat
        //$this->buffer->oper_type
        if (isset($this->buffer->report_type)) {
            $result = new stdClass();
            if ($this->buffer->report_type == "income_costs") {
                $result->income = RbOpers::getIncomeOpers($this->buffer->year, $this->buffer->month, $this->buffer->cat, $this->buffer->oper_type);
                $result->costs = RbOpers::getCostsOpers($this->buffer->year, $this->buffer->month, $this->buffer->oper_type);
            }
            if ($this->buffer->report_type == "income") {
                $result->income = RbOpers::getIncomeOpers($this->buffer->year, $this->buffer->month, $this->buffer->cat, $this->buffer->oper_type);
            }
            if ($this->buffer->report_type == "costs") {
                $result->costs = RbOpers::getCostsOpers($this->buffer->year, $this->buffer->month, $this->buffer->oper_type);
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }
        echo "";
    }

    // =================================================================
    /** Получение товарной ведомости по параметрам:
     * - период
     * - подстрока названия товара
     * - контрагент
     * - сотрудник
     */
    static function getProdVedomost()
    {
        $input = JFactory::getApplication()->input;
        $dateStart = $input->getString('date_start', '01.01.' . JFactory::getDate()->format('y'));
        $dateEnd = $input->getString('date_end', JFactory::getDate()->format('d.m.Y'));
        $prodSubstr = $input->getString('search', '');

        $oper = new RbOpers ();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->clear();
        $query->select($db->quoteName(
            array("op.operId",
                "op.oper_type",
                "op.custId",
                "op.docId",
                "op.productId",
                "op.product_code",
                "op.product_name",
                "op.product_price",
                "op.product_cnt",
                "op.oper_sum",
                "op.oper_firm",
                "op.oper_rem",
                "rrc.cust_name",
                "rrd.doc_num",
                "rrd.doc_type")));
        $query->select("DATE_FORMAT(op.oper_date,'%d.%m.%Y') as oper_date");
        $query->from($db->quoteName($oper->table_name,"op"));
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_cust"), 'rrc') . ' ON (' . $db->quoteName('op.custId') . ' = ' .
            $db->quoteName('rrc.custId') . ')');
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_docs"), 'rrd') . ' ON (' . $db->quoteName('op.docId') . ' = ' .
            $db->quoteName('rrd.docId') . ')');
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_products"), 'rrp') . ' ON (' . $db->quoteName('op.productId') . ' = ' .
            $db->quoteName('rrp.productId') . ')');
        $query->where("op.product_name LIKE '%" . $prodSubstr . "%'");
        $query->where("oper_date>=STR_TO_DATE('$dateStart','%d.%m.%Y')");
        $query->where("oper_date<=STR_TO_DATE('$dateEnd','%d.%m.%Y')");
        $query->where("rrp.product_type=1");
        $query->order($db->quoteName('oper_date'));

        try {
            $db->setQuery($query);
            $res = new stdClass ();
            $res->data = $db->loadAssocList();
            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }

    }

    // =================================================================
    static function getIncomeOpers($year, $month, $cat, $oper_type = "продажа")
    {
        $having = array();
        $group_by = array();
        if (isset($year)) array_push($having, "sYear = $year");
        else array_push($group_by, "sYear");

        if (isset($month)) array_push($having, "sMonth = $month");
        else array_push($group_by, "sYear");

        if (isset($cat)) array_push($having, "Cat = '$cat'");
        else array_push($group_by, "Cat");

        array_push($having, "oper_type = '$oper_type'");

        $sql = file_get_contents(RBO_PATH . '/admin/income_opers.sql');
        $sql = str_replace("%group_by%", implode(", ", $group_by), $sql);
        $sql = str_replace("%having%", implode(" AND ", $having), $sql);

        $db = JFactory::getDBO();
        $db->setQuery($sql);
        try {
            $data = $db->loadAssocList();

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }

        return $data;
    }

    // =================================================================
    static function getCostsOpers($year, $month, $oper_type)
    {
    }
}



