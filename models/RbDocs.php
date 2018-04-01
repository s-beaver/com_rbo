<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";
require_once "models/RbDocsProducts.php";
require_once "models/RbProducts.php";
require_once "models/RbCust.php";
require_once "models/RbHelper.php";
require_once "configuration.php";

class RbDocs extends RbObject
{
    /**
     * @var bool
     */
    public $readBaseDocument = true;

    /**
     * RbDocs constructor.
     * @param null $keyValue
     * @param null $readBaseDocument
     */
    public function __construct($keyValue = null, $readBaseDocument = null)
    {
        parent::__construct($keyValue);

        $this->setTableName("rbo_docs");
        $this->flds ["docId"] = array("type" => "numeric", "is_key" => true);
        $this->flds ["doc_num"] = array("type" => "string");
        $this->flds ["doc_date"] = array("type" => "date");
        $this->flds ["doc_type"] = array("type" => "string");
        $this->flds ["doc_status"] = array("type" => "string");
        $this->flds ["doc_base"] = array("type" => "numeric");

        $this->flds ["custId"] = array("type" => "numeric");

        $this->flds ["doc_sum"] = array("type" => "numeric");
        $this->flds ["pay_date"] = array("type" => "date");
        $this->flds ["doc_manager"] = array("type" => "string");
        $this->flds ["doc_firm"] = array("type" => "string");
        $this->flds ["doc_rem"] = array("type" => "string");

        $this->flds ["created_by"] = array("type" => "string");
        $this->flds ["created_on"] = array("type" => "datetime");
        $this->flds ["modified_by"] = array("type" => "string");
        $this->flds ["modified_on"] = array("type" => "datetime");

        $this->getInputBuffer();

        if (!isset ($keyValue)) $this->keyValue = $this->buffer->docId;
        if (isset ($readBaseDocument)) $this->readBaseDocument = $readBaseDocument;
        else $this->readBaseDocument = true;
    }

    /**
     * @throws RbException
     */
    public function readObject()
    {
        try {
            parent::readObject();
            $custId = $this->buffer->custId;
            $doc_base = $this->buffer->doc_base;

            if ($doc_base && $this->readBaseDocument) {
                if (!isset ($doc_base)) $doc_base = 0; // иначе объект возьмет из буфера, а там ключ самого себя
                $doc_base_doc = new RbDocs ($doc_base, false);
                $doc_base_doc->readObject();
                $this->buffer->doc_base_doc = $doc_base_doc->buffer;
            }

            $prod = new RbDocsProducts ($this->keyValue);
            $prod->readObject();
            $this->buffer->doc_products = $this->addInStockData($prod->buffer);

            if ($custId > 0) {
                $cust = new RbCust ($custId);
                $cust->readObject();
                $cust->buffer->cust_data = json_decode($cust->buffer->cust_data);
                $this->buffer->doc_cust = $cust->buffer;
            }

            $firm = RbConfig::$firms [mb_strtoupper($this->buffer->doc_firm, "UTF-8")];
            if (is_string($firm ["copyof"]) && strlen($firm ["copyof"]) > 0) {
                $firm = RbConfig::$firms [$firm ["copyof"]];
            }
            $this->buffer->doc_firm_details = $firm;
            $this->buffer->doc_manager_details = RbConfig::$managers [$this->buffer->doc_manager];
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw new RbException("Не удалось прочитать документ", 10);
        }
    }

    /**
     * @throws RbException
     */
    public function updateObject()
    {
        $res = new stdClass ();
        try {
            $custId = $this->buffer->custId;
            $doc_products = $this->buffer->doc_products;
            $doc_cust = (array)$this->buffer->doc_cust;
            // проверить если пустой массив, то не сохранять
            $doc_cust ['cust_data'] = json_encode($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);

            $this->buffer->modified_by = JFactory::getUser()->username;
            $this->buffer->modified_on = RbHelper::getCurrentTimeForDb();

            $input = JFactory::getApplication()->input;
            $this->buffer->custId = RbCust::updateOrCreateCustomer($custId, $doc_cust);

            //При удалении старых операций требуется "отменить" изменение остатков товаров
            //todo Учесть документ инвентаризацию 
            $prod = new RbDocsProducts ($this->keyValue);
            $prod->readObject();
            foreach ($prod->buffer as &$p) {
                $prodRef = new RbProducts ($p->productId);
                $prodRef->updateProductInStock($p, true /*обратная операция*/);
            }

            foreach ($doc_products as &$p) {
                $p = (array)$p;
                if (!($p ["productId"] > 0)) { // создадим новый товар в справочнике
                    $pRef = array();
                    $pRef ["productId"] = $p ["productId"];
                    $pRef ["product_code"] = $p ["product_code"];
                    $pRef ["product_name"] = $p ["product_name"];
                    $pRef ["product_price"] = $p ["product_price"];
                    $pRef ["product_in_stock"] = 0;

                    $input->set("rbo_products", $pRef);
                    $prodRef = new RbProducts ();
                    $prodRef->createObject();
                    $p ["productId"] = $prodRef->insertid;
                }
                $p ["docId"] = $this->keyValue;
                $this->setOpersFromDocByStatus($p);

                $prodRef = new RbProducts ($p["productId"]);
                $prodRef->updateProductInStock($p);
            }
            parent::updateObject();

            $input = JFactory::getApplication()->input;
            $input->set("rbo_opers", $doc_products);
            $prod = new RbDocsProducts ($this->keyValue);
            $prod->deleteObject();
            $prod->createObject();

            $res->docId = $this->buffer->docId;//todo зачем?
            $this->buffer = $res;

        } catch (RbException $e) {
            $msg = "Не удалось обновить документ по причине: " . $e->getMessage();
            JLog::add(
                get_class() . ":" . $msg . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw new RbException($msg, $e->getCode());
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }

        /*
         * if ($this->response) {
         * RbOHelper::sendEMail ("документ изменен",
         * "Изменен документ. Детали: " . RbODocs::docBuffer2Str ($this->buffer));
         * }
         */
    }

    /**
     * @throws Exception
     * @throws RbException
     */
    public function createObject()
    {
        $res = new stdClass ();
        try {
            $custId = $this->buffer->custId;
            $doc_products = $this->buffer->doc_products;
            $doc_cust = (array)$this->buffer->doc_cust;
            $doc_cust ['cust_data'] = json_encode($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);

            $this->buffer->created_by = JFactory::getUser()->username;
            $this->buffer->created_on = RbHelper::getCurrentTimeForDb();
            $this->buffer->modified_by = null;
            $this->buffer->modified_on = null;
            // $this->buffer->doc_type = $this->docType;//надо передавать через буфер
            if (empty($this->buffer->doc_num)) {
                for ($i = 0; $i < 1000; $i++) {
                    $newObj = $this->getNextDocNumber();
                    if ($newObj)
                        $this->buffer->doc_num = $newObj->new_num;
                    else throw new RbException("Не удалось получить очередной номер документа", 100);//todo 100 - это значит номер надо уточнить
                    if (!$this->duplicateExists($this->buffer->doc_num)) break;
                }
            }
            if (empty($this->buffer->doc_date)) $this->buffer->doc_date = RbHelper::getCurrentTimeForDb();

            $input = JFactory::getApplication()->input;
            $input->set("rbo_cust", $doc_cust);
            $cust = new RbCust ($custId);
            if ($custId > 0) {
                $cust->updateObject();
            } elseif ($custId == -1) {
                $this->buffer->custId = 0;
            } else {
                $cust->createObject();
                $this->buffer->custId = $cust->insertid;
            }

            parent::createObject();

            $docId = $this->insertid;
            foreach ($doc_products as &$p) {
                $p = (array)$p;
                if (!($p ["productId"] > 0)) { // создадим новый товар в справочнике
                    $pRef = array();
                    $pRef ["productId"] = $p ["productId"];
                    $pRef ["product_code"] = $p ["product_code"];
                    $pRef ["product_name"] = $p ["product_name"];
                    $pRef ["product_price"] = $p ["product_price"];
                    $pRef ["product_in_stock"] = 0;

                    $input->set("rbo_products", $pRef);
                    $prodRef = new RbProducts ();
                    $prodRef->createObject();
                    $p ["productId"] = $prodRef->insertid;
                }
                $p ["docId"] = $docId;
                $this->setOpersFromDocByStatus($p);

                $prodRef = new RbProducts ($p["productId"]);
                $prodRef->updateProductInStock($p);
            }

            $input->set("rbo_opers", $doc_products);
            $prod = new RbDocsProducts ($docId);
            $prod->createObject();
            $res->docId = $docId;
            $this->buffer = $res;

        } catch (RbException $e) {
            $msg = "Не удалось создать документ по причине: " . $e->getMessage();
            JLog::add(
                get_class() . ":" . $msg . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw new RbException($msg, $e->getCode());
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
    public function deleteObject()
    {
        try {
            $this->readObject();
            $this->buffer->doc_status = 'удален';
            $this->updateObject();
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
    public function deleteDocHard()
    {
        try {
            parent::deleteObject();
            $prod = new RbDocsProducts ($this->keyValue);
            $prod->deleteObject();
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * На входе требуется ключ документа откуда копируются данные и тип нового документа
     * @throws Exception
     */
    public function copyDocTo()
    {
        $res = new stdClass ();
        try {
            $keyValue = $this->buffer->doc_base;
            $doc_type = $this->buffer->doc_type;
            $doc_based_on_id = $this->docBasedOnExists($keyValue, $doc_type);
            if ($doc_based_on_id) {//если есть такой документ, то удалим его
                $docToDelete = new RbDocs ($doc_based_on_id, false);
                $docToDelete->deleteDocHard();
            }
            $this->keyValue = $keyValue;
            $this->readObject();
            $this->buffer->docId = null;
            $this->buffer->doc_base = $this->keyValue;
            if (!RbConfig::$continuousNumbering) {
                $this->buffer->doc_num = null;
            }

            $this->buffer->doc_date = null;
            $this->buffer->doc_type = $doc_type;
            $this->buffer->doc_status = "";
            $this->createObject();
            $res->docId = $this->insertid;
            $this->buffer = $res;
        } catch (RbException $e) {
            $msg = "Не удалось скопировать по причине: " . $e->getMessage();
            JLog::add(
                get_class() . ":" . $msg . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw new RbException($msg, $e->getCode());
        } catch (Exception $e) {
            JLog::add(
                get_class() . ":" . $e->getMessage() . " (" . $e->getCode() . ") buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
            throw $e;
        }
    }

    /**
     * @param $product
     */
    public function setOpersFromDocByStatus(&$product)
    {
        if (strtotime($this->buffer->doc_date) < strtotime('1 November 2015')) return;
        if ($this->buffer->doc_status == "подписан" && $this->buffer->doc_cust["cust_is_own_firm"] != "1") {
            $operName = RbConfig::$doc2oper[$this->buffer->doc_type];
            if (!isset($operName)) return;
            $product["oper_type"] = $operName;
            $product["oper_date"] = $this->buffer->doc_date;
            $product["pay_date"] = $this->buffer->pay_date;
            //$product["oper_sum"] = (integer)$product["product_price"]*(integer)$product["product_cnt"];
            $product["custId"] = $this->buffer->custId;
            $product["oper_firm"] = $this->buffer->doc_firm;
            $product["oper_manager"] = $this->buffer->doc_manager;
        } else {
            $product["oper_date"] = null;
        }
    }

    /**
     * @return object
     */
    public function getDocList()
    {
        $db = JFactory::getDBO();
        $res = new stdClass ();
        try {
            $input = JFactory::getApplication()->input;
            $iDisplayStart = $input->getInt('start', -1);
            $iDisplayLength = $input->getInt('length', -1);
            $iDraw = $input->getString('draw', 1);
            $doc_type = $input->getString('doc_type', "");
            $aSearch = $input->get("search", null, "array");
            $sSearch = null;
            if (!is_null($aSearch)) {
                $sSearch = $aSearch["value"];
            }

            $sWhere = " WHERE doc_type='" . $doc_type . "'";
            if (isset ($sSearch) && $sSearch != "") {
                $sWhere .= " AND (rc.cust_name LIKE '%" . $sSearch . "%' OR doc_num=" . (integer)$sSearch . ")";
            }

            $rboCustTableName = RbHelper::getTableName("rbo_cust");
            $sSelect = "SELECT docId, doc_num, doc_date, pay_date, rc.cust_name doc_cust, doc_sum, doc_status, doc_firm, doc_manager, doc_rem ";
            $sRestOfQuery = " FROM " . $this->table_name . " rd LEFT JOIN " . $rboCustTableName . " rc ON rd.custId = rc.custId " .
                $sWhere . " ORDER BY rd.doc_date DESC, rd.docId DESC";

            if (isset ($iDisplayStart) && $iDisplayLength != '-1') {
                $db->setQuery($sSelect . $sRestOfQuery, intval($iDisplayStart), intval($iDisplayLength));
            } else {
                $db->setQuery($sSelect . $sRestOfQuery);
            }

            $data_rows_assoc_list = $db->loadAssocList();

            $db->setQuery('SELECT count(*) ' . $sRestOfQuery);
            $iRecordsTotal = $db->loadResult();

            $db->setQuery("SELECT doc_base, docId, doc_num, doc_date, doc_type " .
                "FROM " . $this->table_name . " WHERE doc_base IN (SELECT docId " . $sRestOfQuery . ") AND doc_status!='удален'");
            $baseDocs = $db->loadAssocList();

            foreach ($data_rows_assoc_list as &$v) {
                $v['doc_date'] = JFactory::getDate($v['doc_date'])->format('d.m.y'); // https://php.net/manual/en/function.date.php
                $v['childs'] = array();
                foreach ($baseDocs as $bd) {
                    if ($bd['doc_base'] == $v['docId']) {
                        $childElem = new stdClass ();
                        $childElem->docId = $bd['docId'];
                        $childElem->doc_num = $bd['doc_num'];
                        $childElem->doc_date = JFactory::getDate($bd['doc_date'])->format('d.m'); // https://php.net/manual/en/function.date.php
                        $childElem->doc_type = $bd['doc_type'];
                        $v['childs'][] = $childElem;
                    }
                }
            }

            $res->draw = (integer)$iDraw;
            $res->recordsTotal = $iRecordsTotal;
            $res->recordsFiltered = $iRecordsTotal;
            $res->data = $data_rows_assoc_list;
        } catch (Exception $e) {
            $res->errorCode = 60;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось получить список документов";
        }
        $this->buffer = $res;
        return $res;
    }

    /**
     * @throws Exception
     */
    public function getNextDocNumber()
    {
        $currentTime = new JDate ();
        $newNumber = RbHelper::getNextDocNumber($this->buffer->doc_type);
        $res = new stdClass ();
        $res->new_num = $newNumber;
        $res->new_date = $currentTime->format('d.m.Y', true);
        return $res;
    }

    /**
     * @param $docNum
     * @return bool
     */
    function duplicateExists($docNum)
    {
        if (is_null($docNum)) return false;
        $currentTime = new JDate ();
        $year = $currentTime->format('Y', false);

        $db = JFactory::getDBO();
        try {
            $query = $db->getQuery(true);
            $query->select("docId");
            $query->from($this->table_name);
            $query->where("doc_type='" . $this->buffer->doc_type . "'");//здесь не нужно проверять $continuousNumbering
            $query->where("DATE_FORMAT(doc_date,'%Y')=$year");
            $query->where("doc_num=$docNum");
            $query->where("doc_status<>'удален'");
            $db->setQuery($query);
            $docId = $db->loadResult();
            if ($docId > 0) return true;
            return false;
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return true;
    }

    /**
     * @param $docId
     * @param $docType
     * @return bool|mixed
     */
    function docBasedOnExists($docId, $docType)
    {
        if (is_null($docId) || is_null(($docType))) return false;
        $currentTime = new JDate ();
        $year = $currentTime->format('Y', false);

        $db = JFactory::getDBO();
        try {
            $query = $db->getQuery(true);
            $query->select("docId");
            $query->from($this->table_name);
            $query->where("doc_type='" . $docType . "'");//здесь не нужно проверять $continuousNumbering
            $query->where("DATE_FORMAT(doc_date,'%Y')=$year");
            $query->where("doc_base=$docId");
            $db->setQuery($query);
            $docId = $db->loadResult();
            return $docId;
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return true;
    }

    /**
     * @param $buffer
     * @return string
     */
    function docBuffer2Str($buffer)
    {
        $s = "";
        $s .= "N " . $buffer->doc_num;
        $s .= " от " . $buffer->doc_date;
        $s .= " " . $buffer->doc_type;
        $s .= " " . $buffer->doc_status;
        $s .= " Сумма:" . $buffer->doc_sum;
        $s .= " " . $buffer->doc_manager;
        $s .= " " . $buffer->doc_firm;
        $s .= " " . $buffer->doc_rem;

        $s .= " Покупатель:" . $buffer->doc_cust->cust_name;

        return $s;
    }

    function addInStockData($products)
    {
        if (is_null($products)) return $products;
        $pIDs = array();
        foreach ($products as $p) {
            array_push($pIDs, $p->productId);
        }

        $db = JFactory::getDBO();
        try {
            $prodRef = new RbProducts ();
            $query = $db->getQuery(true);
            $query->select(array("productId", "product_in_stock"));
            $query->from($db->quoteName($prodRef->table_name));
            $query->where("productId in (" . join(",", $pIDs) . ")");
            $query->where("product_in_stock!=0");
            $db->setQuery($query);
            $inStockData = $db->loadAssocList();
            foreach ($products as &$p) {
                $p->product_in_stock = "-";
                if ($inStockData)
                    foreach ($inStockData as &$v) {
                        if ($v['productId'] == $p->productId) {
                            $p->product_in_stock = $v["product_in_stock"];
                            break;
                        }
                    }
            }
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
        return $products;
    }
}

