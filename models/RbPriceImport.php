<?php
require_once "models/RbObject.php";

class RbPriceImport extends RbObject
{

    // =================================================================
    public function __construct($keyValue)
    {
        parent::__construct($keyValue);

        $this->is_multiple = false;
        $this->setTableName("rbo_price_import");
        $this->flds ["id"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["product_code"] = array("type" => "string");
        $this->flds ["product_name"] = array("type" => "string");
        $this->flds ["product_price"] = array("type" => "numeric");
        $this->flds ["product_price1"] = array("type" => "numeric");
        $this->flds ["product_price2"] = array("type" => "numeric");
        $this->flds ["product_price3"] = array("type" => "numeric");
        $this->flds ["product_price_vip"] = array("type" => "numeric");
        $this->flds ["imported"] = array("type" => "numeric");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->id;
    }

    // =================================================================
    static function getPriceImportList()
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbPriceImport ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name, "rp"));
//        $query->order($db->quoteName('rp.id') . " DESC");

        $where = array();
        if (!empty ($sSearch)) {
            $searchAr = preg_split("/[\s,]+/", $sSearch);
            foreach ($searchAr as $v) {
                $where[] = "LOWER(product_name) LIKE '%" . strtolower($v) . "%'";
            }
        }
        if (count($where) > 0) $query->where($where);

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

            $res = new stdClass ();
            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsTotal;
            $res->data = $data_rows_assoc_list;
            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

}

