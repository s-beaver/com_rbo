<?php
require_once "models/RbObject.php";

class RbDocsProducts extends RbObject
{

    // =================================================================
    public function __construct($keyValue)
    {
        parent::__construct($keyValue);

        $this->is_multiple = true;
        $this->setTableName("rbo_opers");
        $this->flds ["docId"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["oper_type"] = array("type" => "string");
        $this->flds ["oper_date"] = array("type" => "date");
        $this->flds ["pay_date"] = array("type" => "date");
        $this->flds ["custId"] = array("type" => "numeric");
        $this->flds ["oper_firm"] = array("type" => "string");
        $this->flds ["productId"] = array("type" => "numeric");
        $this->flds ["product_code"] = array("type" => "string");
        $this->flds ["product_name"] = array("type" => "string");
        $this->flds ["product_price"] = array("type" => "numeric");
        $this->flds ["product_cnt"] = array("type" => "numeric");
        $this->flds ["product_uom"] = array("type" => "string", "read_only" => true,
            "formula" => "'шт.'");
        $this->flds ["oper_sum"] = array("type" => "numeric");
        $this->flds ["oper_manager"] = array("type" => "string");
        $this->flds ["oper_rem"] = array("type" => "string");

        $this->flds ["created_by"] = array("type" => "string");
        $this->flds ["created_on"] = array("type" => "datetime");
        $this->flds ["modified_by"] = array("type" => "string");
        $this->flds ["modified_on"] = array("type" => "datetime");

        $this->getInputBuffer();
    }

}