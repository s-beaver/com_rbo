<?php
require_once "models/rbobject.php";

class RbOProducts extends RbObject
{

    // =================================================================
    public function __construct($keyValue)
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
        $this->flds ["product_type"] = array("type" => "numeric");

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->productId;
    }

    // =================================================================
    static function updateOrCreateProduct(& $prodId, $prod_data)
    {
        $prod_data = ( object )$prod_data;
        $input = JFactory::getApplication()->input;
        $input->set("rbo_products", $prod_data);
        $prodRef = new RbOProducts ($prodId);
        if ($prodId > 0) {
            if (!isset ($prod_data) || !isset ($prod_data->product_name) || $prod_data->product_name == '') return true;
            if ($prodRef->buffer->_product_data_changed) {
                $prodRef->updateObject();
            } else {
                $prodRef->response = true;
            }
        } elseif ($prodId == -1) {
            $prodId = 0;
            $prodRef->response = true;
        } else {
            if (!isset ($prod_data) || !isset ($prod_data->product_name) || $prod_data->product_name == '') return true;
            $prodRef->createObject();
            $prodId = $prodRef->insertid;
        }
        return $prodRef->response;
    }

    // =================================================================
    static function getProductListForm()
    {
        $input = JFactory::getApplication()->input;
        $searchSubstr = $input->getString('search');
        if (!is_string($searchSubstr) || strlen($searchSubstr) < 2) return;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbOProducts ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name));

        $searchAr = preg_split("/[\s,]+/", $searchSubstr);// split the phrase by any number of commas or space characters
        foreach ($searchAr as $v) {
            $query->where("LOWER(product_name) LIKE '%" . strtolower($v) . "%'");
        }

        try {
            $db->setQuery($query, 0, 30);
            $buffer = $db->loadObjectList();
            $count = $db->getAffectedRows();
            $res = new stdClass ();
            $res->count = $count;
            $res->result = $buffer;
            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

    // =================================================================
    static function getProductList()
    {
        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('iDisplayStart');
        $iDisplayLength = $input->getInt('iDisplayLength');
        $sEcho = $input->getString('sEcho');
        $searchSubstr = $input->getString('sSearch');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbOProducts ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name, "rp"));
        $query->order($db->quoteName('rp.productId') . " DESC");

        if (!empty ($searchSubstr)) {
            $searchAr = preg_split("/[\s,]+/", $searchSubstr);// split the phrase by any number of commas or space characters
            foreach ($searchAr as $v) {
                $query->where("LOWER(product_name) LIKE '%" . strtolower($v) . "%'");
            }
        }

        try {
            if (isset ($iDisplayStart) && $iDisplayLength != '-1') {
                $db->setQuery($query, intval($iDisplayStart), intval($iDisplayLength));
            } else {
                $db->setQuery($query);
            }

            $data_rows_assoc_list = $db->loadAssocList();
            $iTotalDisplayRecords = $db->getAffectedRows();

            $res = new stdClass ();
            $res->sEcho = $sEcho;
            $res->iTotalRecords = $iTotalDisplayRecords;
            $res->iTotalDisplayRecords = $iTotalDisplayRecords;
            $res->aaData = $data_rows_assoc_list;
            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

    // =================================================================
    static function getProductInStock()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $prodRef = new RbOProducts ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause());
        $query->from($db->quoteName($prodRef->table_name, "rp"));
        $query->order($db->quoteName('rp.product_name'));
        $query->where($db->quoteName('rp.product_type') . '=0');
        $query->where($db->quoteName('rp.product_in_stock') . '>0');

        try {
            $db->setQuery($query);

            $res = new stdClass ();
            $res->date = RbOHelper::getCurrentTimeForDb();
            $res->products = $db->loadAssocList();

            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }
}

