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
        $this->flds ["productFoundCount"] = array("type" => "numeric");
        $this->flds ["productFoundId"] = array("type" => "numeric");
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

    // =================================================================
    public function readImportSettings($fileName)
    {
        if (!$lines = file($fileName)) return null;
        $settings = new stdClass ();
        for ($ln = 0; $ln < count($lines); $ln++) {
            $line = preg_split("/=/", $lines[$ln]);
            $line[1] = preg_replace(array("/\"/","/\r/","/\n/"),"",$line[1]);
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
                        $a[1] = preg_replace("/\'/","",$a[1]);
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
                        $settings->dbFlds[$matches[1]] = $line[1];
                    }
                }
            }
        }
        return $settings;
    }

    // =================================================================
    public function loadPriceFromCSV($fileName)
    {
        //todo убрать возможность запускать повторно, пока не выполнена текущая загрузка
        $settings = $this->readImportSettings(realpath(dirname(__FILE__)) . "/../import_settings.ini");
        if (empty($settings)) return;

        $prd = new RbProducts ();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        try {
            //читаем файл
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

                $columns = explode(";", $lines[$ln]);
                if (count($columns) != count($settings->csvColumns)) {
                    //записать в лог
                    continue;
                }

                $buffer = array();
                for ($i = 0; $i < count($settings->csvColumns); $i++) {
                    $buffer[$settings->csvColumns[$i]] = $columns[$i];
                }
                if (empty($buffer[$settings->columnCheckLineValid])) {
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
                $query->select('count(*) as cnt,productId');
                $query->from($db->quoteName($prd->getTableName(), "rp"));
                $query->where("rp.product_name='" . $buffer[$settings->columnToSearch] . "'");//todo проверять текущий прайс?
                $db->setQuery($query);
                $productFound = $db->loadAssoc();

                $query->clear();
                $ins = $this->getArraysForInsert($db, $buffer);
                //добавим номер импортированной строки
                $ins[0][] = "id";
                $ins[1] = $ins[1] . "," . ($ln + 1);

                //добавим ссылку на найденный товар
                if (!empty($productFound)) {
                    $ins[0][] = "productFoundCount";
                    $ins[1] = $ins[1] . "," . $productFound["cnt"];

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

}

