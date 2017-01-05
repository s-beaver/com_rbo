<?php
require_once "models/RbObject.php";

/**
 *
 */
define('IMPORT_SETTINGS_FILE', realpath(dirname(__FILE__) . "/../") . "/import_settings.ini");
define('TEMP_PRICE_TABLE', '#__rbo_products_import');
define('PRICE_TABLE', '#__rbo_products');

class RbPriceImport extends RbObject
{
    // =================================================================
    public function __construct($keyValue = null)
    {
        parent::__construct($keyValue);

        $this->is_multiple = false;
        $this->setTableName("rbo_price_import");
        $this->flds ["id"] = ["type" => "numeric", "is_key" => true];
        $this->flds ["product_category"] = ["type" => "string"];
        $this->flds ["product_code"] = ["type" => "string"];
        $this->flds ["product_name"] = ["type" => "string"];
        $this->flds ["product_price"] = ["type" => "numeric"];
        $this->flds ["product_price1"] = ["type" => "numeric"];
        $this->flds ["product_price2"] = ["type" => "numeric"];
        $this->flds ["product_price3"] = ["type" => "numeric"];
        $this->flds ["product_price_vip"] = ["type" => "numeric"];
        $this->flds ["productFoundCount"] = ["type" => "numeric"];
        $this->flds ["productFoundId"] = ["type" => "numeric"];

        $this->getInputBuffer();
        if (!isset ($keyValue)) $this->keyValue = $this->buffer->id;
    }

    // =================================================================
    static function readINIFile()
    {
        $settings = new stdClass ();
        if (!$settings->lines = file(IMPORT_SETTINGS_FILE)) return null;
        echo json_encode($settings, JSON_UNESCAPED_UNICODE);
    }

    // =================================================================
    static function saveINIFile()
    {
        $input = JFactory::getApplication()->input;
        $iniContent = $input->get("iniFileContent", null, null);
        if (empty($iniContent)) {
            die(json_encode(array('error' => array(
                'message' => 'Empty ini file',
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        if (file_put_contents(IMPORT_SETTINGS_FILE, $iniContent)) {
            echo json_encode(array('message' => 'SUCCESS'), JSON_UNESCAPED_UNICODE);
        } else {
            die(json_encode(array('error' => array(
                'message' => 'Error while writing ini file',
                'code' => 2,
            )), JSON_UNESCAPED_UNICODE));
        }
    }

    // =================================================================
    public function getPriceImportList()
    {
        $settings = $this->readImportSettings(IMPORT_SETTINGS_FILE);
        $input = JFactory::getApplication()->input;
        $iDisplayStart = $input->getInt('start', -1);
        $iDisplayLength = $input->getInt('length', -1);
        $iDraw = $input->getString('draw', 1);
        $aSearch = $input->get("search", null, "array");
        $sSearch = null;
        if (!is_null($aSearch)) {
            $sSearch = $aSearch["value"];
        }
        $prod = new RbProducts();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $prodRef = new RbPriceImport ();
        $query->clear();
        $query->select($prodRef->getFieldsForSelectClause("rp"));
        foreach ($settings->checkForChange as $prodFld => $priceFld) {
            $query->select($db->quoteName("prod." . $prodFld, "prod_" . $prodFld));
        }
        $query->from($db->quoteName($prodRef->getTableName(), "rp"));
        $query->join("LEFT", $db->quoteName($prod->getTableName(), 'prod') . ' ON (' . $db->quoteName('rp.productFoundId') . ' = ' . $db->quoteName('prod.productId') . ')');

        $where = array();
        if (!empty ($sSearch)) {
            $searchAr = preg_split("/[\s,]+/", $sSearch);
            foreach ($searchAr as $v) {
                $where[] = "LOWER(rp.product_name) LIKE '%" . strtolower($v) . "%'";
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
            $data_rows = array();

            foreach ($data_rows_assoc_list as &$row) {
                $this->checkProductChanged($settings, $row, $row);
                array_push($data_rows, $row);
            }

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
            $res->data = $data_rows;
            echo json_encode($res);
        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

    // =================================================================
    public function readImportSettings($fileName)
    {
        if (!$lines = file($fileName)) return null;
        $settings = new stdClass ();
        for ($ln = 0; $ln < count($lines); $ln++) {
            $line = preg_split("/=/", $lines[$ln]);
            $line[1] = preg_replace(array("/\"/", "/\r/", "/\n/"), "", $line[1]);
            if (count($line) != 2) continue;
            switch ($line[0]) {
                case "PRICE_NAME": {
                    $settings->priceName = $line[1];
                    break;
                }
                case "BUCKS_RATE": {
                    $settings->bucksRate = (float)$line[1];//is_float()
                    break;
                }
                case "SKIP_ROWS": {
                    $settings->skipRows = (int)$line[1];
                    break;
                }
                case "SEARCH_COL": {
                    $settings->searchColumn = $line[1];
                    break;
                }
                case "CHECK_LINE_VALID": {
                    $settings->columnCheckLineValid = $line[1];
                    break;
                }
                case "CONVERT_COLS": {
                    $settings->convert = new stdClass ();
                    $parts = explode("#", $line[1]);
                    if (count($parts) < 2) continue;
                    $settings->convert->columns = explode(",", $parts[0]);
                    $settings->convert->rulesFrom = array();
                    $settings->convert->rulesTo = array();
                    for ($i = 1; $i < count($parts); $i++) {
                        $a = explode("->", $parts[$i]);
                        $a[1] = preg_replace("/\'/", "", $a[1]);
                        array_push($settings->convert->rulesFrom, $a[0]);
                        array_push($settings->convert->rulesTo, $a[1]);
                    }
                    break;
                }
                default: {
                    $matches = array();
                    if (preg_match("/^CSV_COL(\d+)$/", $line[0], $matches) == 1) {
                        $settings->csvColumns[--$matches[1]] = $line[1];
                    } elseif (preg_match("/^DB_(.+)$/", $line[0], $matches) == 1) {
                        $params = preg_split("/,/", $line[1]);
                        $settings->dbFlds[$matches[1]] = $params[0];
                        if (count($params) > 1 && $params[1] == "check") {
                            $settings->checkForChange[$matches[1]] = $params[0];
                        }
                    }
                }
            }
        }
        return $settings;
    }

    // =================================================================
    private function checkProductChanged($settings, $rowFrom, &$rowTo)
    {
        $changed = false;
        foreach ($settings->checkForChange as $prodFld => $priceFld) {
            if (strcmp(substr($priceFld, 0, 1), "$") == 0) {
                $diff = $settings->bucksRate * $rowFrom[substr($priceFld, 1, strlen($priceFld) - 1)] - $rowFrom["prod_" . $prodFld];
            } else
                $diff = $rowFrom[$priceFld] - $rowFrom["prod_" . $prodFld];
            if (abs($diff) > 0.5) {
                if (isset($rowTo))
                    $rowTo[$priceFld] = $rowFrom[$priceFld] . " (" . $diff . ")";
                $changed = true;
            }
        }
        if ($changed) {
        }
        return $changed;
    }

    // =================================================================
    public function loadPriceFromCSV($fileName)
    {
        //todo убрать возможность запускать повторно, пока не выполнена текущая загрузка
        $settings = $this->readImportSettings(IMPORT_SETTINGS_FILE);
        if (empty($settings)) return;

        $prd = new RbProducts ();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        try {
            //читаем файл
            $lines = array();
            if (is_uploaded_file($fileName)) {
                $lines = file($fileName);
                if (count($lines) == 0) return;
            }
            //спрашиваем, завершать или нет транзакцию

            //очищаем таблицу
            $query->clear();
            $query->delete($db->quoteName($this->getTableName()));
            $db->setQuery($query);
            if (!$db->execute())
                throw new Exception('Could not delete table ' . $this->getTableName());

            //загружаем данные в таблицу
            for ($ln = 0; $ln < count($lines); $ln++) {
                if (is_int($settings->skipRows) && $settings->skipRows > 0 && $ln < $settings->skipRows) continue;

                $columns = str_getcsv($lines[$ln], ";");
                if (count($columns) != count($settings->csvColumns)) {
                    //записать в лог
                    continue;
                }

                $buffer = array();
                for ($i = 0; $i < count($settings->csvColumns); $i++) {
                    $buffer[$settings->csvColumns[$i]] = $columns[$i];
                }
                if (isset($settings->columnCheckLineValid) && empty($buffer[$settings->columnCheckLineValid])) {
                    continue; //это не товар, а заголовок группы
                }

                if (!empty($settings->convert)) {
                    foreach ($settings->convert->columns as $columnName) {
                        $buffer[$columnName] = preg_replace(
                            $settings->convert->rulesFrom,
                            $settings->convert->rulesTo,
                            $buffer[$columnName]
                        );
                    }
                }

                $query->clear();
                $query->select($prd->getFieldsForSelectClause());
                $query->from($db->quoteName($prd->getTableName()));
                $query->where("product_name='" . $buffer[$settings->searchColumn] . "'");
                $query->order($db->quoteName('price_name') . " DESC");
                $db->setQuery($query);
                $productFound = $db->loadAssoc();
                foreach ($productFound as $columnName => $value) {
                    $buffer["prod_" . $columnName] = $value;
                }

                if (!$this->checkProductChanged($settings, $buffer)) {
                    continue;
                }

                $query->clear();
                $ins = $this->getArraysForInsert($db, $buffer);
                //добавим номер импортированной строки
                $ins[0][] = "id";
                $ins[1] = $ins[1] . "," . ($ln + 1);

                //добавим ссылку на найденный товар
                if (!empty($productFound)) {
                    $ins[0][] = "productFoundId";
                    $ins[1] = $ins[1] . "," . $productFound["productId"];
                }

                $query->insert($db->quoteName($this->table_name));
                $query->columns($ins [0]);
                $query->values($ins [1]);
                $db->setQuery($query);
                if (!$db->execute())
                    throw new Exception('Could not insert ' . $lines[$ln]);
            }

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

    // =================================================================
    public function importPrice()
    {
        //Создание точки восстановления
        $result = RbHelper::executeQuery("DROP TABLE IF EXISTS " . TEMP_PRICE_TABLE);
        $result = $result && RbHelper::executeQuery("CREATE TABLE " . TEMP_PRICE_TABLE . " SELECT * FROM " . PRICE_TABLE);

        //Импорт прайса
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->clear();
        $select = $this->getFieldsForSelectClause();
        $query->select($select);
        $query->from($db->quoteName($this->table_name));
        $db->setQuery($query);
        try {
            $price = $db->loadAssocList();
            $input = JFactory::getApplication()->input;
            $settings = $this->readImportSettings(IMPORT_SETTINGS_FILE);
            if (empty($settings)) return;
            foreach ($price as &$p) {
                $p = (array)$p;

                if (!($p ["productFoundId"] > 0)) {
                    continue;
                }

                $pRef = $this->convertByRate($settings, $p);
                $pRef["price_name"] = JFactory::getDate()->format('Ymd'); // https://php.net/manual/en/function.date.php
                $input->set("rbo_products", $pRef);
                $prodRef = new RbProducts ($p ["productFoundId"]);
                $prodRef->updateObject(false, false);
            }
            $result = $result && RbHelper::executeQuery("DELETE FROM " . $db->quoteName($this->table_name) . " WHERE productFoundId is not null");

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
        }
    }

    // =================================================================
    public function addProduct()
    {
        $input = JFactory::getApplication()->input;
        $lineNumber = $input->getInt("importLineNumber", -1);
        if ($lineNumber == -1) return;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->clear();
        $select = $this->getFieldsForSelectClause();
        $query->select($select);
        $query->from($db->quoteName($this->table_name));
        $query->where("id=" . $lineNumber);
        $db->setQuery($query);
        $pFound = $db->loadAssocList();
        if (!isset($pFound)) return;
        $p = $pFound[0];

        $input = JFactory::getApplication()->input;
        $settings = $this->readImportSettings(IMPORT_SETTINGS_FILE);
        if (empty($settings)) return;
        $pRef = $this->convertByRate($settings, (array)$p);
        $pRef["price_name"] = JFactory::getDate()->format('Ymd'); // https://php.net/manual/en/function.date.php
        $input->set("rbo_products", $pRef);
        if ($p ["productFoundId"] > 0) {
            $prodRef = new RbProducts ($p ["productFoundId"]);
            $prodRef->updateObject();
        } else {
            $prodRef = new RbProducts ();
            $prodRef->createObject();
        }
//        $prodRef->insertid;

        RbHelper::executeQuery("DELETE FROM " . $db->quoteName($this->table_name) . " WHERE id=" . $lineNumber);
        echo json_encode(array('message' => 'SUCCESS'), JSON_UNESCAPED_UNICODE);
    }

    // =================================================================
    private function convertByRate($settings, $p)
    {
        $pRef = array();
        foreach ($settings->dbFlds as $k => $v) {
            if (strcmp(substr($v, 0, 1), "$") == 0) {//преобразуем в рубли
                $value = $settings->bucksRate * $p[substr($v, 1, strlen($v) - 1)];
            } elseif (!(array_search($v, $settings->csvColumns) === false)) {//если в settings указано название поля
                $value = $p[$v];
            } else {//если в settings указана константа
                $value = $v;
            }
            $pRef[$k] = $value;
        }
        return $pRef;
    }


}

