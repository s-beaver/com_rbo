<?php
require_once 'models/RbException.php';
require_once "models/RbObject.php";

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
        $this->flds ["product_code"] = ["type" => "string"];
        $this->flds ["product_category"] = ["type" => "string"];
        $this->flds ["product_code"] = ["type" => "string"];
        $this->flds ["product_name"] = ["type" => "string"];
        $this->flds ["product_price"] = ["type" => "numeric"];
        $this->flds ["product_price1"] = ["type" => "numeric"];
        $this->flds ["product_price2"] = ["type" => "numeric"];
        $this->flds ["product_price3"] = ["type" => "numeric"];
        $this->flds ["product_price_vip"] = ["type" => "numeric"];
        $this->flds ["product_autocode"] = ["type" => "string"];
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
        $db = JFactory::getDBO();
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
                $where[] = "LOWER(rp.product_name) LIKE '%" . mb_strtolower($v) . "%'";
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
                case "PRICE_NAME":
                    {
                        $settings->priceName = $line[1];
                        break;
                    }
                case "BUCKS_RATE":
                    {
                        $settings->bucksRate = (float)$line[1];//is_float()
                        break;
                    }
                case "SKIP_ROWS":
                    {
                        $settings->skipRows = (int)$line[1];
                        break;
                    }
                case "SEARCH_COL":
                    {
                        $settings->searchColumn = $line[1];
                        break;
                    }
                case "CHECK_LINE_VALID":
                    {
                        $settings->columnCheckLineValid = $line[1];
                        break;
                    }
                case "CONVERT_COLS":
                    {
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
                default:
                    {
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $line = 0;
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
                $line++;
                if (is_int($settings->skipRows) && $settings->skipRows > 0 && $ln < $settings->skipRows) continue;

                $columns = str_getcsv($lines[$ln], ";");
//                if (count($columns) != count($settings->csvColumns)) {
//                    //записать в лог
//                    continue;
//                }

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

                $buffer['product_autocode'] = preg_replace(
                    ['/[\s\\,\/]/', '/с/', '/б/', '/к/'],
                    ['', 'S', 'B', 'K'],
                    mb_strtolower($buffer[$settings->searchColumn])
                );
                $query->clear();
                $query->select($prd->getFieldsForSelectClause());
                $query->from($db->quoteName($prd->getTableName()));
                $query->where("product_name='" . $buffer[$settings->searchColumn] . "'", "OR");
                $query->where("product_autocode='" . $buffer['product_autocode'] . "'");
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
                $ins = $this->getArraysForInsert($buffer);
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
            die(json_encode(array('error' => array(
                'message' => "Ошибка в строке $line. " . $e->getMessage(),
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        echo json_encode(array('success' => array(
            'message' => "Обработано " . count($lines) . " строк",
        )), JSON_UNESCAPED_UNICODE);

    }

    // =================================================================
    public function importPrice()
    {
        //Создание точки восстановления
        $result = RbHelper::executeQuery("DROP TABLE IF EXISTS " . TEMP_PRICE_TABLE);
        $result = $result && RbHelper::executeQuery("CREATE TABLE " . TEMP_PRICE_TABLE . " SELECT * FROM " . PRICE_TABLE);

        //Импорт прайса
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->clear();
        $select = $this->getFieldsForSelectClause();
        $query->select($select);
        $query->from($db->quoteName($this->table_name));
        $db->setQuery($query);
        $line = 0;
        $lines = 0;
        try {
            $price = $db->loadAssocList();
            $input = JFactory::getApplication()->input;
            $settings = $this->readImportSettings(IMPORT_SETTINGS_FILE);
            if (empty($settings)) return;
            foreach ($price as &$p) {
                $line++;
                $p = (array)$p;

                if (!($p ["productFoundId"] > 0)) {
                    continue;
                }

                $pRef = $this->convertByRate($settings, $p);
                $pRef["price_name"] = $settings->priceName;
                $pRef["updated"] = RbHelper::getCurrentTimeForDb();
                $input->set("rbo_products", $pRef);
                $prodRef = new RbProducts ($p ["productFoundId"]);
                $prodRef->updateObject(false);
                $lines++;
            }
            $result = $result && RbHelper::executeQuery("DELETE FROM " . $db->quoteName($this->table_name) . " WHERE productFoundId is not null");

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo');
            die(json_encode(array('error' => array(
                'message' => "Ошибка в строке $line. " . $e->getMessage(),
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        echo json_encode(array('success' => array(
            'message' => "Обработано " . count($lines) . " строк",
        )), JSON_UNESCAPED_UNICODE);

    }

    // =================================================================
    public function addProduct()
    {
        $input = JFactory::getApplication()->input;
        $lineNumber = $input->getInt("importLineNumber", -1);
        if ($lineNumber == -1) return;

        $db = JFactory::getDBO();
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
        $pRef["price_name"] = $settings->priceName;
        $pRef["updated"] = RbHelper::getCurrentTimeForDb();
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

    // =================================================================
    /*
        $chainedCategory делим на части (|).
        Проверяем есть ли каждая из частей в j3_virtuemart_categories_ru_ru (category_name) если нет - добавляем.
        Получаем virtuemart_category_id для каждой из частей.
        Проверяем в таблице j3_virtuemart_category_categories что есть все записи цепочки по полям
        category_parent_id и category_child_id
        Если такой нет - добавляем нехватающую
        Сохраняем ключ последней категории
    */
    public static function getVMCategory($chainedCategory, &$cachedCatList)
    {
        if (!empty ($chainedCategory)) {
            $catsAr = preg_split("/\|/", $chainedCategory);
            $catList = array();
            foreach ($catsAr as &$catName) {
                $catName = trim($catName);
                if (is_null($cachedCatList[$catName])) {
                    $id = RbHelper::SQLGet("select virtuemart_category_id from #__virtuemart_categories_ru_ru where category_name ='$catName'");
                    if (is_null($id)) {
                        $id = RbHelper::insertQuery("insert into #__virtuemart_categories() values ()");
                        $slug = RbHelper::translit($catName);
                        RbHelper::executeQuery("insert into #__virtuemart_categories_ru_ru(virtuemart_category_id,category_name,slug) values ($id,'$catName','$slug')");
                        JLog::add("Создана категория=$catName", JLog::INFO, 'com_rbo_vm');
                    }
                    $cachedCatList[$catName] = $id;
                    array_push($catList, array("category_id" => $id, "category_name" => $catName));
                } else {
                    array_push($catList, array("category_id" => $cachedCatList[$catName], "category_name" => $catName));
                }
            }

            for ($i = 0; $i < count($catList); $i++) {//с 1 это правильно
                $cid = $catList[$i]["category_id"];
                $pid = ($i == 0) ? 0 : $catList[$i - 1]["category_id"];
                $res = RbHelper::SQLGet("select count(*) from #__virtuemart_category_categories where category_parent_id=$pid and category_child_id=$cid");
                if (is_null($res) || $res == 0) {
                    RbHelper::insertQuery("insert into #__virtuemart_category_categories(category_parent_id,category_child_id) values ($pid,$cid)");
                }
            }
            return $catList[count($catList) - 1]["category_id"];
        }

    }

// =================================================================
    /* Проверяем есть ли товар с указанным названием в таблице j3_virtuemart_products_ru_ru (product_name)
       Проверяем прикреплен ли товар к нужной категории (сохраненный ключ последней категории).
       Если нет - пишем в лог и переприкрепляем к нужной категории

        Если товара нет, то создаем его, добавляем в таблицы j3_virtuemart_products_ru_ru, j3_virtuemart_products

       Если не удалось создать товар то исключение
    */
    public static function getVMProduct($productName, $productSKU, $categoryId)
    {
        $productName = trim($productName);
        $productSKU = trim($productSKU);
        $productIdList = RbHelper::SQLGetAssocList("select virtuemart_product_id from #__virtuemart_products_ru_ru where product_name='$productName'");
        $productId = null;
        if (empty($productIdList)) {
            if (is_null($productSKU)) $productSKU = "";
            $productId = RbHelper::insertQuery("insert into #__virtuemart_products(product_sku,published) values ('$productSKU',1)");
            //добавить производителя
            $slug = RbHelper::translit($productName);
            RbHelper::executeQuery("insert into #__virtuemart_products_ru_ru(virtuemart_product_id,product_name,slug) values ($productId,'$productName','$slug')");
            JLog::add("Создана товар=$productName", JLog::INFO, 'com_rbo_vm');
        } else {
            if (count($productIdList) != 1) { //пишем в лог что товар не один
                JLog::add("Надено несколько товаров (" . count($productIdList) . ") с названием $productName", JLog::ALERT, 'com_rbo_vm');
            }
            $productId = $productIdList[0]["virtuemart_product_id"];
        }
        $res = RbHelper::SQLGet("select id from #__virtuemart_product_categories where virtuemart_product_id=$productId and virtuemart_category_id=$categoryId");
        if (empty($res)) {
            RbHelper::executeQuery("delete from #__virtuemart_product_categories where virtuemart_product_id=$productId ");
            RbHelper::executeQuery("insert into #__virtuemart_product_categories(virtuemart_product_id,virtuemart_category_id) values ($productId,$categoryId)");
            JLog::add("Товар '$productName' привязан к категории=$categoryId", JLog::INFO, 'com_rbo_vm');
        }
        return $productId;
    }

// =================================================================;
    /*Обновляем цены в j3_virtuemart_product_prices. Находим запись запросом и подменяем в ней только цену, если не нашли, то создаем новую*/
    public static function setVMProductPrices(
        $productId,
        $priceGroupList, //ассоциативный массив: название группы - ключ в БД
        $pricesAr //ассоциативный массив: название колонки цен - цена
    )
    {
        foreach ($pricesAr as $priceName => &$priceVal) {
            $priceVal = mb_ereg_replace("\s", "", $priceVal);
            $priceVal = str_replace(",", ".", $priceVal);
            //$priceVal = preg_replace(array("/,/", "/\s/", "/\xA0/"), array(".", "", ""), $priceVal);
            if (empty($priceVal)) {
                JLog::add("Пропущена пустая цена '$priceName'", JLog::ALERT, 'com_rbo_vm');
                continue;
            }
            $priceId = RbHelper::SQLGet("select virtuemart_product_price_id from #__virtuemart_product_prices where virtuemart_product_id=$productId and virtuemart_shoppergroup_id=" . $priceGroupList[$priceName]);
            if (empty($priceId)) {
                $priceId = RbHelper::insertQuery("insert into #__virtuemart_product_prices () values ()");
                JLog::add("Добавлена цена '$priceName'", JLog::ALERT, 'com_rbo_vm');
            }
            JLog::add("update #__virtuemart_product_prices set virtuemart_product_id=$productId, virtuemart_shoppergroup_id=" . $priceGroupList[$priceName] . ", product_price=$priceVal where virtuemart_product_price_id=$priceId", JLog::ALERT, 'com_rbo_vm');
            RbHelper::executeQuery("update #__virtuemart_product_prices set virtuemart_product_id=$productId, virtuemart_shoppergroup_id=" . $priceGroupList[$priceName] . ", product_price=$priceVal where virtuemart_product_price_id=$priceId");
            JLog::add("Обновлена цена '$priceName'=$priceVal", JLog::ALERT, 'com_rbo_vm');
        }
        //product_currency
    }

// =================================================================
    public static function importVMFromCSV($fileName)
    {
        $skuCol = 0; //артикул
        $catCol = 8; //категория
        $nameCol = 1; //наименование
        $priceCols = array(
            "Розница" => 3,
            "1 Оптовая У.Е." => 4,
            "2 Базовая У.Е." => 5,
            "3 Партнер У.Е." => 6,
            "Цена VIP У.Е" => 7
        );

        JLog::addLogger(
            array(
                'text_file' => 'com_rbo_vm.php'
            ),
            JLog::ALL,
            array('com_rbo_vm')
        );
        $res = RbHelper::SQLGetAssocList("select virtuemart_shoppergroup_id,shopper_group_name from #__virtuemart_shoppergroups");
        $priceGroupList = array();
        foreach ($res as $v) {
            $priceGroupList[$v["shopper_group_name"]] = $v["virtuemart_shoppergroup_id"];
        }
        JLog::add("Получены ценовые категории=" . implode(",", $priceGroupList), JLog::INFO, 'com_rbo_vm');

        try {//читаем файл
            $lines = array();
            if (is_uploaded_file($fileName)) {
                $lines = file($fileName);
                if (count($lines) == 0) return;
            }

            $cachedCatList = array();
            $line = 0;
            for ($ln = 0; $ln < count($lines); $ln++) {
                $line++;
                $columns = str_getcsv($lines[$ln], ";");
                JLog::add("Строка $line =" . implode(",", $columns), JLog::INFO, 'com_rbo_vm');
                if (empty($columns[$nameCol])) continue;
                if (empty($columns[$catCol])) continue;
                $vmCategoryId = RbPriceImport::getVMCategory($columns[$catCol], $cachedCatList);
                if (is_null($vmCategoryId))  //пишем в лог и пропускаем
                    JLog::add("Не удалось создать категорию " . $columns[$catCol], JLog::ALERT, 'com_rbo_vm');

                $vmProductId = RbPriceImport::getVMProduct($columns[$nameCol], $columns[$skuCol], $vmCategoryId);

                $pricesAr = array();
                foreach ($priceCols as $colName => $colPos) {
                    $pricesAr[$colName] = $columns[$colPos];
                }
                RbPriceImport::setVMProductPrices($vmProductId, $priceGroupList, $pricesAr);
            }

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo_vm');
            die(json_encode(array('error' => array(
                'message' => "Ошибка в строке $line. " . $e->getMessage(),
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        echo json_encode(array('success' => array(
            'message' => "Обработано " . count($lines) . " строк",
        )), JSON_UNESCAPED_UNICODE);

    }

// =================================================================
    public static function importInStockFromCSV($fileName)
    {
        $idCol = 0;
        $nameCol = 1;
        $inStockCol = 2;

        JLog::addLogger(
            array(
                'text_file' => 'com_rbo_import_in_stock.php'
            ),
            JLog::ALL,
            array('com_rbo_iis')
        );

        try {
            $lines = array();
            if (is_uploaded_file($fileName)) {
                $lines = file($fileName);
                if (count($lines) == 0) return;
            }

            $line = 0;
            for ($ln = 0; $ln < count($lines); $ln++) {
                $line++;
                $columns = str_getcsv($lines[$ln], ";");
                JLog::add("Строка $line =" . implode(",", $columns), JLog::INFO, 'com_rbo_iis');
                if ($columns[$idCol]=="") continue;
                if ($columns[$nameCol]=="") continue;
                if ($columns[$inStockCol]=="") continue;
                $productId = trim($columns[$idCol]);
                $productName = trim($columns[$nameCol]);
//                $productIdList = RbHelper::SQLGetAssocList("select productId from #__rbo_products where product_name='$productName'");
                $productIdList = RbHelper::SQLGetAssocList("select productId from #__rbo_products where productId=$productId");
                if (empty($productIdList)) {
                    JLog::add("Строка $line. Не удалось найти товар '$productName'", JLog::ERROR, 'com_rbo_iis');
                    continue;
                }
                if (count($productIdList) > 1) {
                    JLog::add("Найдено несколько товаров (" . count($productIdList) . ") с названием $productName", JLog::ERROR, 'com_rbo_iis');
                    continue;
                }
                $productId = $productIdList[0]["productId"];
                $productInStock = (integer)(trim($columns[$inStockCol]));
                RbHelper::executeQuery("update #__rbo_products set product_in_stock=$productInStock where productId=$productId");
                JLog::add("Строка $line. Установлен остаток для товара ($productName)=$productInStock", JLog::INFO, 'com_rbo_iis');
            }

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo_iis');
            die(json_encode(array('error' => array(
                'message' => "Ошибка в строке $line. " . $e->getMessage(),
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        echo json_encode(array('success' => array(
            'message' => "Обработано " . count($lines) . " строк",
        )), JSON_UNESCAPED_UNICODE);

    }

// =================================================================
    public static function importInStockFromCSV1($fileName)
    {
        JLog::addLogger(
            array(
                'text_file' => 'com_rbo_import_in_stock.php'
            ),
            JLog::ALL,
            array('com_rbo_iis')
        );

        try {
            $lines = array();
            if (is_uploaded_file($fileName)) {
                $lines = file($fileName);
                if (count($lines) == 0) return;
            }

            $line = 0;
            for ($ln = 0; $ln < count($lines); $ln++) {
                $line++;
                $columns = str_getcsv($lines[$ln], ";");
                JLog::add("Строка $line =" . implode(",", $columns), JLog::INFO, 'com_rbo_iis');
                if (empty($columns[1])) continue;
                $productName = trim($columns[1]);
                $productIdList = RbHelper::SQLGetAssocList("select productId from #__rbo_products where product_name='$productName'");
                $productId = $productIdList[0]["productId"];
                if (count($productIdList) > 1) {
                    JLog::add("Найдено несколько товаров (" . count($productIdList) . ") с названием $productName", JLog::ERROR, 'com_rbo_iis');
                    continue;
                }
                $catId = (integer)(trim($columns[0]));
                $product_price = str_replace(",", ".", trim($columns[2]));
                $product_price1 = str_replace(",", ".", trim($columns[3]));
                $product_type = trim($columns[4]);
                if (empty($productIdList)) {
                    $productId = RbHelper::insertQuery("insert into #__rbo_products(product_name) values ('$productName')");
                }
                RbHelper::executeQuery("update #__rbo_products set categoryId=$catId, product_name='$productName',product_price=$product_price,product_price1=$product_price1,product_type=$product_type where productId=$productId");
                JLog::add("Обновлен товар $productName", JLog::INFO, 'com_rbo_iis');
            }

        } catch (Exception $e) {
            JLog::add(get_class() . ":" . $e->getMessage(), JLog::ERROR, 'com_rbo_iis');
            die(json_encode(array('error' => array(
                'message' => "Ошибка в строке $line. " . $e->getMessage(),
                'code' => 1,
            )), JSON_UNESCAPED_UNICODE));
        }
        echo json_encode(array('success' => array(
            'message' => "Обработано " . count($lines) . " строк",
        )), JSON_UNESCAPED_UNICODE);

    }

}

//DELETE FROM `test.robik.ru`.j3_virtuemart_products;
//DELETE FROM `test.robik.ru`.j3_virtuemart_products_ru_ru;
//DELETE FROM `test.robik.ru`.j3_virtuemart_product_categories;
//DELETE FROM `test.robik.ru`.j3_virtuemart_categories;
//DELETE FROM `test.robik.ru`.j3_virtuemart_categories_ru_ru;
//DELETE FROM `test.robik.ru`.j3_virtuemart_category_categories;
//DELETE FROM `test.robik.ru`.j3_virtuemart_product_prices;