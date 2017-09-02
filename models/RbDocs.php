<?php
require_once "models/RbObject.php";
require_once "models/RbDocsProducts.php";
require_once "models/RbProducts.php";
require_once "models/RbCust.php";
require_once "models/RbHelper.php";
require_once "configuration.php";

class RbDocs extends RbObject
{
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
     * @return bool
     */
    public function readObject()
    {
        try {
            $result = parent::readObject();
            $custId = $this->buffer->custId;
            $doc_base = $this->buffer->doc_base;

            if ($doc_base && $this->readBaseDocument) {
                if (!isset ($doc_base)) $doc_base = 0; // иначе объект возьмет из буфера, а там ключ самого себя
                $doc_base_doc = new RbDocs ($doc_base, false);
                $result = $result && $doc_base_doc->readObject();
                $this->buffer->doc_base_doc = $doc_base_doc->buffer;
            }

            $prod = new RbDocsProducts ($this->keyValue);
            $result = $result && $prod->readObject();
            $this->buffer->doc_products = $prod->buffer;

            $cust = new RbCust ($custId);
            $result = $result && $cust->readObject();
            $cust->buffer->cust_data = json_decode($cust->buffer->cust_data);
            $this->buffer->doc_cust = $cust->buffer;

            $firm = RbConfig::$firms [mb_strtoupper($this->buffer->doc_firm, "UTF-8")];
            if (is_string($firm ["copyof"]) && strlen($firm ["copyof"]) > 0) {
                $firm = RbConfig::$firms [$firm ["copyof"]];
            }
            $this->buffer->doc_firm_details = $firm;
            $this->buffer->doc_manager_details = RbConfig::$managers [$this->buffer->doc_manager];
        } catch (Exception $e) {
            $result = false;
            $this->buffer->errorCode = 10;
            $this->buffer->errorMsg = $e->getMessage();
            if (!$this->buffer->errorMsg)
                $this->buffer->errorMsg = "Не удалось прочитать документ";
//            if (!$echoResponse) throw $e;
        }
//        $this->response = json_encode($this->buffer, JSON_UNESCAPED_UNICODE);
//        if ($echoResponse) echo $this->response;
        return $result;
    }

    /**
     * @return bool
     */
    public function updateObject()
    {
        $result = true;
        $res = new stdClass ();
        try {
            $custId = $this->buffer->custId;
            $doc_products = $this->buffer->doc_products;
            $doc_cust = (array)$this->buffer->doc_cust;
            // проверить если пустой массив, то не сохранять
            $doc_cust ['cust_data'] = json_encode($doc_cust ['cust_data'], JSON_UNESCAPED_UNICODE);

            $this->buffer->modified_by = JFactory::getUser()->username;
            $this->buffer->modified_on = RbHelper::getCurrentTimeForDb();

            $input = JFactory::getApplication()->input;//todo устарело?
            $input->set("rbo_cust", $doc_cust);
            $cust = new RbCust ($custId);
            if ($custId > 0) {
                $result = $result && $cust->updateObject();
            } elseif ($custId == -1) {
                $this->buffer->custId = 0;
            } else {
                $result = $result && $cust->createObject();
                $this->buffer->custId = $cust->insertid;
            }

            //При удалении старых операций требуется "отменить" изменение остатков товаров
            //todo Учесть документ инвентаризацию 
            $prod = new RbDocsProducts ($this->keyValue);
            $result = $result && $prod->readObject();
            foreach ($prod->buffer as &$p) {
                $prodRef = new RbProducts ($p->productId);
                $result = $result && $prodRef->updateProductInStock($p, true /*обратная операция*/);
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
                    $result = $result && $prodRef->createObject();
                    $p ["productId"] = $prodRef->insertid;
                }
                $p ["docId"] = $this->keyValue;
                $this->setOpersFromDocByStatus($p);

                $prodRef = new RbProducts ($p["productId"]);
                $result = $result && $prodRef->updateProductInStock($p);
            }
            $result = $result && parent::updateObject();

            $input = JFactory::getApplication()->input;
            $input->set("rbo_opers", $doc_products);
            $prod = new RbDocsProducts ($this->keyValue);
            $result = $result && $prod->deleteObject();
            $result = $result && $prod->createObject();

            $res->docId = $this->buffer->docId;

        } catch (Exception $e) {
            $result = false;
            $res->errorCode = 20;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось обновить документ";
//            if (!$echoResponse) throw $e;
        }
        $this->buffer = $res;
//        $this->response = json_encode($res);
//        if ($echoResponse) echo $this->response;
        return $result;

        /*
         * if ($this->response) {
         * RbOHelper::sendEMail ("документ изменен",
         * "Изменен документ. Детали: " . RbODocs::docBuffer2Str ($this->buffer));
         * }
         */
    }

    /**
     * @return bool
     */
    public function createObject()
    {
        $result = true;
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
                $newObj = $this->getNextDocNumber();
                if ($newObj)
                    $this->buffer->doc_num = $newObj->new_num;
                else $result = false;
            }
            if (empty($this->buffer->doc_date)) $this->buffer->doc_date = RbHelper::getCurrentTimeForDb();

            $input = JFactory::getApplication()->input;
            $input->set("rbo_cust", $doc_cust);
            $cust = new RbCust ($custId);
            if ($custId > 0) {
                $result = $result && $cust->updateObject();
            } elseif ($custId == -1) {
                $this->buffer->custId = 0;
            } else {
                $result = $result && $cust->createObject();
                $this->buffer->custId = $cust->insertid;
            }

            if ($this->duplicateExists($this->buffer->doc_num)) {
                throw new Exception("Документ с таким номером уже существует");
            }
            $result = $result && parent::createObject();

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
                    $result = $result && $prodRef->createObject();
                    $p ["productId"] = $prodRef->insertid;
                }
                $p ["docId"] = $docId;
                $this->setOpersFromDocByStatus($p);

                $prodRef = new RbProducts ($p["productId"]);
                $result = $result && $prodRef->updateProductInStock($p);
            }

            $input->set("rbo_opers", $doc_products);
            $prod = new RbDocsProducts ($docId);
            $result = $result && $prod->createObject();
            $res->docId = $docId;

        } catch (Exception $e) {
            $result = false;
            $res->errorCode = 30;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось создать документ";
//          throw $e; - приводит к в ветке error в $.ajax({error:{}})
        }
        $this->buffer = $res;
//        $this->response = json_encode($res);
//        if ($echoResponse) echo $this->response;
        return $result;
    }

    /**
     * @return bool
     */
    public function deleteObject()
    {
        $result = true;
        $result = $result && $this->readObject();
        $this->buffer->doc_status = 'удален';
        $result = $result && $this->updateObject();
        return $result;
    }

    /**
     * @return bool
     */
    public function deleteObjectOld()
    {//todo а как же ведут себя подчиненные записи?
        $result = true;
        $res = new stdClass ();
        try {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update($db->quoteName($this->table_name));
            $query->set("doc_status='удален'");
            $query->where($this->getWhereClause());
            $db->setQuery($query);
            $result = (bool)$db->execute();
            $this->response = $result;
        } catch (Exception $e) {
            $result = false;
            $res->errorCode = 50;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось удалить документ";
//            if (!$echoResponse) throw $e;
            JLog::add(
                get_class() . ":" . $e->getMessage() . " buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
        }
//        if ($echoResponse) echo $this->response;
        $this->buffer = $res;
        return $result;
    }

    /**
     * @return bool
     */
    public function deleteObjectHard()
    {
        $result = true;
        $res = new stdClass ();
        try {
            $result = $result && parent::deleteObject();
            $prod = new RbDocsProducts ($this->keyValue);
            $result = $result && $prod->deleteObject();

        } catch (Exception $e) {
            $result = false;
            $res->errorCode = 50;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось удалить документ";
//            if (!$echoResponse) throw $e;
            JLog::add(
                get_class() . ":" . $e->getMessage() . " buffer=" . print_r($this->buffer, true),
                JLog::ERROR, 'com_rbo');
        }
//        if ($echoResponse) echo $this->response;
        $this->buffer = $res;
        return $result;
    }

    // =================================================================

    /**
     * На входе требуется ключ документа откуда копируются данные и тип нового документа
     * @return bool
     */
    public function copyDocTo()
    {
        $result = true;
        $res = new stdClass ();
        try {
            $keyValue = $this->buffer->doc_base;
            $doc_type = $this->buffer->doc_type;
            $doc_based_on_id = $this->docBasedOnExists($keyValue, $doc_type);
            if ($doc_based_on_id) {//если есть такой документ, то удалим его
                $docToDelete = new RbDocs ($doc_based_on_id, false);
                $result = $result && $docToDelete->deleteObjectHard();
            }
            $this->keyValue = $keyValue;
            $result = $result && $this->readObject();
            $this->buffer->docId = null;
            $this->buffer->doc_base = $this->keyValue;
            if (!RbConfig::$continuousNumbering) {
                $this->buffer->doc_num = null;
            }

            $this->buffer->doc_date = null;
            $this->buffer->doc_type = $doc_type;
            $this->buffer->doc_status = "";
            $result = $result && $this->createObject();
            $res->docId = $this->insertid;

        } catch (Exception $e) {
            $result = false;
            $res->errorCode = 40;
            $res->errorMsg = $e->getMessage();
            if (!$res->errorMsg)
                $res->errorMsg = "Не удалось скопировать документ";
//            if (!$echoResponse) throw $e;
        }
//        $this->response = json_encode($res);
//        if ($echoResponse) echo $this->response;
        $this->buffer = $res;
        return $result;
    }

    /**
     * @param $product
     */
    public function setOpersFromDocByStatus(&$product)
    {
        if (strtotime($this->buffer->doc_date) < strtotime('1 November 2015')) return;

        if ($this->buffer->doc_status == "подписан" && $this->buffer->doc_cust["cust_is_own_firm"] != "1") {
            switch ($this->buffer->doc_type) {
                case "акт":
                case "накл": {
                    $product["oper_type"] = "продажа";
                    break;
                }
                case "B_ACT":
                case "B_BIL": {
                    $product["oper_type"] = "закуп";
                    break;
                }
                case "D_CMP": {
                    $product["oper_type"] = "декомплект";
                    break;
                }
                case "B_STK": {
                    $product["oper_type"] = "инвентар";
                    break;
                }
                default:
                    return;
            }
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
                $sWhere . " ORDER BY rd.docId DESC";

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
//        $this->response = json_encode($res);
//        if ($echoResponse) echo $this->response;
        $this->buffer = $res;
        return $res;
    }

    public function getNextDocNumber()
    {
        $currentTime = new JDate ();
        $year = $currentTime->format('Y', false);

        try {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select("MAX(doc_num)");
            $query->from($this->table_name);
            if (!RbConfig::$continuousNumbering) {
                $query->where("doc_type='" . $this->buffer->doc_type . "'");
            }
            $query->where("DATE_FORMAT(doc_date,'%Y')=$year");
            $query->where("doc_base IS NULL");
            $db->setQuery($query);
            $newNumber = $db->loadResult();
            if (is_null($newNumber)) $newNumber = 0;
            $res = new stdClass ();
            $res->new_num = $newNumber + 1;
            $res->new_date = $currentTime->format('d.m.Y', true);

            $result = $res;
        } catch (Exception $e) {
            $result = false;
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
//            if (!$echoResponse) throw $e;
        }
//        if ($echoResponse) echo $this->response;
        return $result;
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
}

