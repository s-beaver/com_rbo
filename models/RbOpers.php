<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";
require_once "models/RbCust.php";
require_once "models/RbHelper.php";

class RbOpers extends RbObject
{

    /**
     * RbOpers constructor.
     * @param null $keyValue
     */
    public function __construct($keyValue = null)
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
        $this->flds ["pay_date"] = array("type" => "date");
        $this->flds ["oper_manager"] = array("type" => "string");
        $this->flds ["oper_rem"] = array("type" => "string");

        $this->flds ["created_by"] = array("type" => "string");
        $this->flds ["created_on"] = array("type" => "datetime");
        $this->flds ["modified_by"] = array("type" => "string");
        $this->flds ["modified_on"] = array("type" => "datetime");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->operId;
    }

    /**
     * @throws Exception
     */
    public function readObject()
    {
        try {
            parent::readObject();
            $custId = $this->buffer->custId;
            $cust = new RbCust ($custId);
            $cust->readObject();
            $cust->buffer->cust_data = json_decode($cust->buffer->cust_data);
            $this->buffer->oper_cust = $cust->buffer;
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function updateObject()
    {
        try {
            $custId = $this->buffer->custId;
            $oper_cust = $this->buffer->oper_cust;
            $oper_cust ['cust_data'] = json_encode($oper_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
            $productId = $this->buffer->productId;

            RbCust::updateOrCreateCustomer($custId, $oper_cust);
            $this->buffer->custId = $custId;

            RbProducts::updateOrCreateProduct($productId, $this->buffer);
            $this->buffer->productId = $productId;

            $this->buffer->modified_by = JFactory::getUser()->username;
            $this->buffer->modified_on = RbHelper::getCurrentTimeForDb();
            parent::updateObject();
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function createObject()
    {
        try {
            $custId = $this->buffer->custId;
            $oper_cust = $this->buffer->oper_cust;
            $oper_cust ['cust_data'] = json_encode($oper_cust ['cust_data'], JSON_UNESCAPED_UNICODE);
            $productId = $this->buffer->productId;
            RbCust::updateOrCreateCustomer($custId, $oper_cust);
            $this->buffer->custId = $custId;
            RbProducts::updateOrCreateProduct($productId, $this->buffer);
            $this->buffer->productId = $productId;
            $this->buffer->created_by = JFactory::getUser()->username;
            $this->buffer->created_on = RbHelper::getCurrentTimeForDb();
            parent::createObject();
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * @return object
     */
    public function getOperList()
    {
        $db = JFactory::getDBO();

        $res = new stdClass ();
        try {
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
            $sPeriodFilter = $input->getString('period_filter', "day");
            if (empty($sPeriodFilter)) $sPeriodFilter = "day";
            $sTypeFilter = $input->getString('type_filter', "");

            $query = $db->getQuery(true);

            $query->clear();
            $select = $this->getFieldsForSelectClause('so');
            $select[] = "rc.cust_name cust_name";
            $select[] = "rd.doc_type doc_type";
            $select[] = "rd.doc_num doc_num";
            $select[] = "rd.doc_date doc_date";
            $query->select($select);
            $query->from($db->quoteName($this->table_name, 'so'));
            $query->order($db->quoteName('so.oper_date') . " DESC");

            $rboCustTableName = RbHelper::getTableName("rbo_cust");
            $query->leftJoin(
                $db->quoteName($rboCustTableName, 'rc') . ' ON (' . $db->quoteName('so.custId') . ' = ' .
                $db->quoteName('rc.custId') . ')');
            $rboDocTableName = RbHelper::getTableName("rbo_docs");
            $query->leftJoin(
                $db->quoteName($rboDocTableName, 'rd') . ' ON (' . $db->quoteName('so.docId') . ' = ' .
                $db->quoteName('rd.docId') . ')');

            $whereAND = array();
            $whereAND[] = "so.oper_date>0"; //$query->where("so.oper_date>0");
            if (isset($sDateFilter) && $sDateFilter != "") {
                $tz = RbHelper::getTimezone();
                if (isset($sPeriodFilter) && $sPeriodFilter != "") {
                    $date = new JDate ($sDateFilter, $tz);
                    $date->modify('+1 ' . $sPeriodFilter);
                    $whereAND[] = "so.oper_date<'" . $date->format('Y-m-d') . "'";//$query->where("so.oper_date<'".$date->format('Y-m-d')."'");
                }
                $date = new JDate ($sDateFilter, $tz);
                $whereAND[] = "so.oper_date>='" . $date->format('Y-m-d') . "'";//$query->where("so.oper_date>='".$date->format('Y-m-d')."'");
            }
            if (isset($sTypeFilter) && $sTypeFilter != "") {
                $whereAND[] = "so.oper_type='$sTypeFilter'";//$query->where("so.oper_type='$sTypeFilter'");
            }
            $whereOR = array();
            if (!empty ($sSearch)) {
                $searchAr = preg_split("/[\s,]+/", $sSearch);// split the phrase by any number of commas or space characters
                foreach ($searchAr as $v) {
                    $whereOR[] = "LOWER(so.product_name) LIKE '%" . mb_strtolower($v) . "%'";
                    $whereOR[] = "LOWER(so.oper_rem) LIKE '%" . mb_strtolower($v) . "%'";
                }
            }
            $cond = "";
            if (count($whereAND) > 0) {
                $cond = implode(" AND ", $whereAND);
            }
            if (count($whereOR) > 0) {
                if (count($whereAND) > 0) $cond = $cond . " AND ";
                $cond = $cond . "(" . implode(" OR ", $whereOR) . ")";
            }
            $query->where($cond);

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
            //if (count($whereAND) > 0) $query->where($whereAND);
            if ($cond != "") $query->where($cond);
            $db->setQuery($query);
            $iRecordsTotal = $db->loadResult();
            $iRecordsFiltered = $iRecordsTotal;

            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsFiltered;
            $res->data = $data_rows_assoc_list;
        } catch (Exception $e) {
            $res->errorCode = 80;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список операций";
        }

        $this->buffer = $res;
        return $res;
    }

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
            return $result;
        }
        return "";
    }

    // =================================================================
    static function getEverydayReport()
    {
        $tz = RbHelper::getTimezone();
        $currentTime = new JDate ("now", $tz);
        $reportTime = new JDate ("now -1 day", $tz);
        $date = $reportTime->format('d.m.Y', true);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->clear();
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName(RbHelper::getTableName("rbo_log"), 'log'));
        $query->where("DATE_FORMAT(log.event_time,'%d.%m.%Y')='" . $currentTime->format('d.m.Y', true) . "'");
        $query->where("log.event_type='everydayReportSent'");
        $db->setQuery($query);
        $iCnt = $db->loadResult();
        if (isset($iCnt) && $iCnt > 0) { //отчет уже был выслан
            return;
        }

        $opers = RbOpers::getOpersArrayByQuery($date, $date, null, null, null, null, null, null, null, 500, null);
        $res = "";
        $operTypeTotals = array();
        foreach ($opers as $key => $value) {
            if (!is_null($value["oper_sum"]))
                $operTypeTotals[$value["oper_type"]] += $value["oper_sum"];
            $res .= $value["operId"] . "|" . $value["oper_date"] . "|" . $value["oper_type"] . "|" .
                $value["product_price"] . "|" . $value["product_cnt"] . "|" . $value["oper_sum"] . "|" .
                $value["oper_firm"] . "|" . $value["product_name"] . "|" . $value["cust_name"] . "|" . $value["doc_num"] . "|" . $value["oper_rem"] . "\r\n";
        }
        if (empty($res)) {
            $res .= "Операций не было";
        } else {
            $res .= "Итого по типам операций:\r\n";
            foreach ($operTypeTotals as $key => $value) {
                $res .= "  - " . $key . "=" . $value . "\r\n";
            }
        }

        if (RbHelper::sendEMail($_SERVER["SERVER_NAME"] . " - отчет за " . $date, $res)) {
            $query = $db->getQuery(true)
                ->insert(RbHelper::getTableName('rbo_log'))
                ->columns($db->quoteName(array("event_ip", "event_time", "event_type")))
                ->values("'" . $_SERVER["REMOTE_ADDR"] . "','$currentTime','everydayReportSent'");

            $db->setQuery($query);
            $db->execute();
        }
    }

    // =================================================================

    /**
     * @param $d1 - строка даты в формате dd.mm.YYYY
     * @param $d2 - строка даты в формате dd.mm.YYYY
     * @param $oper_type - строка операции или все (пустая строка)
     * @param $custId - id контрагента или массив id контрагентов (или пусто)
     * @param $prodId - id товара, массив id товаров или подстрока названия товара
     * @param $prod_type - 1- товар, 2 - услуга, null - любое
     * @param $firm - строка
     * @param $sort - DESC или пусто
     * @param $limit - лимит возвращаемых записей
     * @param $positive_cnt - возвращать операции с только положительными количествами
     * @return mixed
     */
    static function getOpersArrayByQuery($d1, $d2, $oper_type, $custId, $prodId, $prod_type, $firm, $manager, $sort, $limit, $positive_cnt)
    {
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
                "op.oper_manager",
                "op.oper_rem",
                "rrc.cust_name",
                "rrd.doc_num",
                "rrd.doc_type")));
        $query->select("DATE_FORMAT(op.oper_date,'%d.%m.%Y') as oper_date");
        $query->select("DATE_FORMAT(rrd.doc_date,'%d.%m.%Y') as doc_date");
        $query->from($db->quoteName($oper->table_name, "op"));
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_cust"), 'rrc') . ' ON (' . $db->quoteName('op.custId') . ' = ' .
            $db->quoteName('rrc.custId') . ')');
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_docs"), 'rrd') . ' ON (' . $db->quoteName('op.docId') . ' = ' .
            $db->quoteName('rrd.docId') . ')');
        $query->leftJoin(
            $db->quoteName(RbHelper::getTableName("rbo_products"), 'rrp') . ' ON (' . $db->quoteName('op.productId') . ' = ' .
            $db->quoteName('rrp.productId') . ')');
        $query->where("oper_date>=STR_TO_DATE('$d1','%d.%m.%Y')");
        $query->where("oper_date<=STR_TO_DATE('$d2','%d.%m.%Y')");
        if (isset($positive_cnt))
            $query->where("product_cnt>0");

        if (isset($prodId)) {
            switch (gettype($prodId)) {
                case "string": {
                    $query->where("op.product_name like '%" . $prodId . "%'");
                    break;
                }
                case "integer": {
                    $query->where("op.productId = " . $prodId);
                    break;
                }
                case "array": {
                    $query->where("op.productId in (" . implode(", ", $prodId) . ")");
                    break;
                }
            }
        }

        if (isset($oper_type)) {
            if (is_array($oper_type))
                $query->where("op.oper_type in ('" . implode("', '", $oper_type) . "')");
            elseif (is_string($oper_type))
                $query->where("op.oper_type='" . $oper_type . "'");
        }

        if (isset($prod_type))
            $query->where("rrp.product_type=" . $prod_type);

        if (isset($firm) && $firm != "")
            $query->where("op.oper_firm='" . $firm . "'");

        if (isset($manager) && $manager != "")
            $query->where("op.oper_manager='" . $manager . "'");

        if (isset($custId)) {
            if (is_array($custId)) {
                $query->where("op.custId in (" . implode(", ", $custId) . ")");
            } elseif ($custId > 0) {
                $query->where("op.custId = " . $custId);
            }
        }

        if (isset($sort) && $sort != "")
            $query->order($db->quoteName('op.oper_date') . " " . $sort);
        else
            $query->order($db->quoteName('op.oper_date'));

        if (version_compare(JPlatform::getShortVersion(), "12.1.0", ">=")) {
            if (isset($limit)) {
                $query->setLimit($limit);
            }
        }
        //JLog::add($query->__toString(), 'debug', 'com_rbo');

        try {
            if (version_compare(JPlatform::getShortVersion(), "12.1.0", "<")) {
                if (isset($limit)) {
                    $db->setQuery($query, 0, $limit);
                } else {
                    $db->setQuery($query);
                }
            } else {
                $db->setQuery($query);
            }
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }

        return $db->loadAssocList();
    }

    /** Получение товарной ведомости по параметрам:
     * - период
     * - подстрока названия товара
     * - контрагент
     * - сотрудник
     */
    static function getProdVedomost()
    {
        $input = JFactory::getApplication()->input;
        $dateStart = $input->getString('date_start', "");
        $dateEnd = $input->getString('date_end', "");
        if ($dateStart == "") $dateStart = '01.' . JFactory::getDate()->format('m.Y');
        if ($dateEnd == "") $dateEnd = JFactory::getDate()->format('d.m.Y');
        $prodSubstr = $input->getString('search', '');
        $prodId = $input->getInt('prodId', 0);
        if (!isset($prodId) || $prodId == 0) $prodId = $prodSubstr;
        if ((integer)$prodId == 0) $prodId = $prodSubstr;
        $firmSubstr = $input->getString('firm', null);
        $managerSubstr = $input->getString('manager', null);
        $custSubstr = $input->getString('cust', '');
        $custId = $input->getString('custId', null);
        //JLog::add("Params: ".$dateStart."-".$dateEnd."-".$custId."-".$prodId."-".$firmSubstr, 'debug', 'com_rbo');

        $res = new stdClass ();
        try {
            $res->data = RbOpers::getOpersArrayByQuery($dateStart, $dateEnd, null, $custId, $prodId, 1, $firmSubstr, $managerSubstr, 'ASC', null, "cnt>0");
            foreach ($res->data as &$o) {
                if (isset($o["productId"]) && $o["productId"] != "" && isset($o["oper_date"]) && $o["oper_date"] != "") {
                    $buyPriceHist = RbOpers::getOpersArrayByQuery(null, $o["oper_date"], array("закуп", "декомплект"), null, (int)$o["productId"], 1, null, null, "DESC", 1, "cnt>0");
                    $o["buyPrice"] = $buyPriceHist[0]["product_price"];
                    $o["buyDocId"] = $buyPriceHist[0]["docId"];
                    $o["buyOperType"] = $buyPriceHist[0]["oper_type"];
                }
            }
            $res->date_start = $dateStart;
            $res->date_end = $dateEnd;
//            echo json_encode($res);
        } catch (Exception $e) {
            $res->errorCode = 70;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не построить товарную ведомость";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }

    static function getIncomeOpers($year, $month, $cat, $oper_type = "продажа")
    {
        $data = null;
        try {
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
            $data = $db->loadAssocList();

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }

        return $data;
    }

    static function getCostsOpers($year, $month, $oper_type)
    {
    }

    /* @param $productId - ключ товара
     * @param $parties - массив объектов партий товара до выполнения операции {id,cnt,price}
     * @param $operation - объект, содержащий описание операции {oper_type,cnt,price}
     * @param $prevMove - ссылка на предыдущее движение партии
     * @return array
     * Функция возвращает измененное состояние партий $parties
     * для указанного товара $productId, после применений к нему
     * операции $oper_type
     * */
    static function partyMove($productId, $parties, $operation, $prevMove)
    {
        if (!isset($parties)) {
            $parties = array();
            $parties[0] = new stdClass ();
            $parties[0]->id = 0;
            $parties[0]->cnt = 0;
            $parties[0]->price = 0;
        }
        //еще как-то надо для продажи подсчитать себестоимость и ее вернуть в документ
    }

}



