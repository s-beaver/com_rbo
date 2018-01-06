<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";

class RbProducts extends RbObject
{

    /**
     * RbProducts constructor.
     * @param null $keyValue
     */
    public function __construct($keyValue = null)
    {
        parent::__construct($keyValue);

        $this->is_multiple = false;
        $this->setTableName("rbo_products");
        $this->flds ["productId"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["categoryId"] = array("type" => "numeric");
        $this->flds ["product_code"] = array("type" => "string");
        $this->flds ["product_name"] = array("type" => "string");
        $this->flds ["product_in_stock"] = array("type" => "numeric");
        $this->flds ["product_price"] = array("type" => "numeric");
        $this->flds ["product_price1"] = array("type" => "numeric");
        $this->flds ["product_price_bucks"] = array("type" => "numeric");
        $this->flds ["product_type"] = array("type" => "numeric");
        $this->flds ["price_name"] = array("type" => "string");
        $this->flds ["product_autocode"] = array("type" => "string");
        $this->flds ["updated"] = array("type" => "date");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->productId;
    }

    /**
     * @return bool
     */
    public function createObject()
    {
        $this->buffer = ( object )$this->buffer;
        $this->buffer->price_name = JFactory::getDate()->format('Ymd');
        parent::createObject();
    }

    /**
     * @param bool $priceCheck
     * @return bool
     */
    public function updateObject($priceCheck = true)
    {
        $this->buffer = ( object )$this->buffer;
        $this->buffer->price_name = JFactory::getDate()->format('Ymd');
        parent::updateObject();
    }

    public function updateProductInStock($oper, $invertUpdate = false)
    {
        try {
            $oper = (object)$oper;
            if (empty($oper->oper_date) or $oper->oper_date == "00.00.0000") return; //не "проведенная" операция
            $signMove = RbConfig::$operstype[$oper->oper_type]["signMove"];
            if (empty($signMove)) {
                throw new RbException("Не найден дескриптор операции '" . $oper->oper_type . "'", 10);//todo коды надо упорядочивать
            }
            $signMove = (integer)$signMove;
            if ($invertUpdate) $signMove = -$signMove;

            parent::readObject();
            if ($this->buffer->product_type = 1) return;
            if (empty($this->buffer->product_in_stock)) $this->buffer->product_in_stock = 0;
            $this->buffer->product_in_stock = (integer)$this->buffer->product_in_stock + ((integer)$oper->product_cnt * $signMove);
            parent::updateObject();
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * @param $prodId
     * @param $prod_data
     * @return int|null
     */
    static function updateOrCreateProduct($prodId, $prod_data)
    {
        $prod_data = ( object )$prod_data;
        if (isset ($prod_data)) {
            $prod_data->price_name = JFactory::getDate()->format('Ymd');
        }
        $input = JFactory::getApplication()->input;
        $input->set("rbo_products", $prod_data);
        $prodRef = new RbProducts ($prodId);

        if ($prodId == -1) {
            return 0;
        } else {
            if (!isset ($doc_cust) || !isset ($doc_cust->cust_name) || $doc_cust->cust_name == '') return null;
            if ($prodId > 0) {
                $prodRef->updateObject();
                return $prodId;
            } else {
                $prodRef->createObject();
                return $prodRef->insertid;
            }
        }
    }

    /**
     * @return object
     */
    static function getProductListForm()
    {
        $input = JFactory::getApplication()->input;
        $searchSubstr = $input->getString('search', "");
        $searchWithFilter = $input->getString('filter', "1");
        if (!is_string($searchSubstr) || strlen($searchSubstr) < 2) return;

        $db = JFactory::getDBO();
        $res = new stdClass ();
        try {
            $query = $db->getQuery(true);

            $prodRef = new RbProducts ();
            $query->clear();
            $query->select($prodRef->getFieldsForSelectClause());
            $query->from($db->quoteName($prodRef->table_name));
//        if ($searchWithFilter == "1") {пока уберем проверку на наличие на складе
//            $query->where("product_in_stock>0", "AND");
//        }

            $searchAr = preg_split("/[\s,]+/", $searchSubstr);// split the phrase by any number of commas or space characters
            foreach ($searchAr as $v) {
                if (!strpos($v, "\\"))
                    $query->where("LOWER(product_name) LIKE '%" . mb_strtolower($v) . "%'");
            }

            $db->setQuery($query, 0, 30);
            $buffer = $db->loadObjectList();
            $count = $db->getAffectedRows();
            $res->count = $count;
            $res->result = $buffer;
//            echo json_encode($res);
        } catch (Exception $e) {
            $res->errorCode = 90;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список товаров/услуг";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }

    /**
     * @return object
     */
    static function getProductList()
    {
        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('start', -1);
        $iDisplayLength = $input->getInt('length', -1);
        $iDraw = $input->getString('draw', 1);
        $aSearch = $input->get("search", null, "array");
        $sSearch = null;
        if (!is_null($aSearch)) {
            $sSearch = $aSearch["value"];
        }
        $sInStockFilter = $input->getString('filter_instock', -1);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbProducts ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name, "rp"));
        $query->order($db->quoteName('rp.productId') . " DESC");
//        $query->where("product_in_stock>0", "AND");пока уберем проверку на наличие на складе

        $where = array();
        if ($sInStockFilter != -1) {
            //$where[] = $db->quoteName('rp.product_type') . '=1';
            $where[] = $db->quoteName('rp.product_in_stock') . '!=0';
        } elseif (!empty ($sSearch)) {
            $searchAr = preg_split("/[\s,]+/", $sSearch);// split the phrase by any number of commas or space characters
            foreach ($searchAr as $v) {
                $where[] = "LOWER(product_name) LIKE '%" . mb_strtolower($v) . "%'";
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
            $query->from($db->quoteName($prodRef->table_name, "rp"));
            if (count($where) > 0) $query->where($where);
            $db->setQuery($query);
            $iRecordsTotal = $db->loadResult();

            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsTotal;
            $res->data = $data_rows_assoc_list;
        } catch (Exception $e) {
            $res->errorCode = 90;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список товаров/услуг";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }

    static function getProductInStock()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbProducts ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name, "rp"));
        $query->order($db->quoteName('rp.product_name'));
        //$query->where($db->quoteName('rp.product_type') . '=1');//пусть покажутся и услуги, если они неверно были оформлены
        $query->where($db->quoteName('rp.product_in_stock') . '!=0');

        $res = new stdClass ();
        try {
            $db->setQuery($query);

            $res->date = RbHelper::getCurrentTimeForDb();
            $res->products = $db->loadAssocList();

            echo json_encode($res);
        } catch (Exception $e) {
            $res->errorCode = 100;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось построить отчет по остаткам на складе";
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $res;
    }
}

