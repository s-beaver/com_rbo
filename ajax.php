<?php

//http://robik.ru/components/com_rbo/ajax.php?task=get_current_date

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

if (file_exists(dirname(__FILE__) . '/defines.php')) {
    require_once dirname(__FILE__) . '/defines.php';
}
if (!defined('_JDEFINES')) {
    define('RBO_PATH', realpath(dirname(__FILE__)));
    define('JPATH_BASE', realpath(dirname(__FILE__) . "/../.."));
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';
require_once "models/RbHelper.php";
require_once "models/RbDocs.php";
require_once "models/RbProducts.php";
require_once "models/RbPriceImport.php";
require_once "models/RbDocsProducts.php";
require_once "models/RbCust.php";
require_once "models/RbOpers.php";
require_once "models/RbFirms.php";
JLog::addLogger(array('text_file' => 'com_rbo.php'), JLog::ALL, array('com_rbo'));

$app = JFactory::getApplication('site');
$app->initialise();

RbHelper::checkAccess();
RbOpers::getEverydayReport();

$input = $app->input;
$cmd = $input->getCmd('task');

switch ($cmd) {
    case "get_current_date" : {
        $currentTime = new JDate ();
        $res = new stdClass ();
        $res->new_date = $currentTime->format('d.m.Y', true);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        break;
    }

    // ==================================== doc ==========================
    case "doc_create" :
    case "doc_read" :
    case "doc_update" :
    case "doc_delete" :
    case "doc_copy" : {
        $doc = new RbDocs ();
        $db = JFactory::getDbo();
        $db->transactionStart();
        $res = true;
        switch ($cmd) {
            case "doc_create" : {
                $res = $doc->createObject();
                break;
            }
            case "doc_read" : {
                $res = $doc->readObject();
                break;
            }
            case "doc_update" : {
                $res = $doc->updateObject();
                break;
            }
            case "doc_delete" : {
                $res = $doc->deleteObject();
                break;
            }
            case "doc_copy" : {
                $res = $doc->copyDocTo();
                break;
            }
        }
        if ($res) {
            $db->transactionCommit();
        } else {
            $db->transactionRollback();
        }

        $doc->echoResponse();
        break;
    }

    case "get_doc_list" : {
        $doc = new RbDocs ();
        echo json_encode($doc->getDocList());
        break;
    }

    case "get_doc_num" : {
        $doc = new RbDocs ();
        echo json_encode($doc->getNextDocNumber());
        break;
    }

    // ==================================== opers ==========================
    case "oper_create" :
    case "oper_read" :
    case "oper_update" :
    case "oper_delete" : {
        $oper = new RbOpers ();
        $db = JFactory::getDbo();
        $db->transactionStart();
        $res = true;
        switch ($cmd) {
            case "oper_create" : {
                $res = $oper->createObject();
                break;
            }
            case "oper_read" : {
                $res = $oper->readObject();
                break;
            }
            case "oper_update" : {
                $res = $oper->updateObject();
                break;
            }
            case "oper_delete" : {
                $res = $oper->deleteObject();
                break;
            }
        }
        if ($res) {
            $db->transactionCommit();
        } else {
            $db->transactionRollback();
        }

        $oper->echoResponse();
        break;
    }

    case "get_oper_list" : {
        $operList = new RbOpers ();
        echo json_encode($operList->getOperList());
        break;
    }

    case "oper_report" : {
        $oper = new RbOpers ();
        echo json_encode($oper->getOperReport());
        break;
    }

    case "report_prod_ved" : {
        echo json_encode(RbOpers::getProdVedomost());
        break;
    }

    // ==================================== product ==========================
    case "product_search" : {
        echo json_encode(RbProducts::getProductListForm());
        break;
    }

    case "get_product_list" : {
        echo json_encode(RbProducts::getProductList());
        break;
    }

    case "product_create" :
    case "product_read" :
    case "product_update" :
    case "product_delete" : {
        $prod = new RbProducts ();
        $db = JFactory::getDbo();
        $db->transactionStart();
        $res = true;
        switch ($cmd) {
            case "product_create" : {
                $res = $prod->createObject();
                break;
            }
            case "product_read" : {
                $res = $prod->readObject();
                break;
            }
            case "product_update" : {
                $res = $prod->updateObject();
                break;
            }
            case "product_delete" : {
                $res = $prod->deleteObject();
                break;
            }
        }
        if ($res) {
            $db->transactionCommit();
        } else {
            $db->transactionRollback();
        }

        $prod->echoResponse();
        break;
    }

    // ==================================== price import ==========================
    case "get_price_import_list" : {
        $pi = new RbPriceImport ();
        $pi->getPriceImportList();
        break;
    }

    case "import_read_ini" : {
        RbPriceImport::readINIFile();
        break;
    }

    case "import_save_ini" : {
        RbPriceImport::saveINIFile();
        break;
    }

    case "import_open_csv" : {
        RbPriceImport::importVMFromCSV($_FILES['import_csv']['tmp_name']);
        break;
    }

    case "import_csv_in_stock" : {
        RbPriceImport::importInStockFromCSV($_FILES['import_csv']['tmp_name']);
        break;
    }

    case "import_import_price" : {
        $pi = new RbPriceImport ();
        $pi->importPrice();
        break;
    }

    case "import_product_add" : {
        $pi = new RbPriceImport ();
        $pi->addProduct();
        break;
    }

    // ==================================== customer ==========================
    case "cust_search" : {
        $cust = new RbCust ();
        echo json_encode($cust->getCustListBySubstr());
        break;
    }

    case "get_cust_list" : {
        echo json_encode(RbCust::getCustList());
        break;
    }

    case "cust_create" :
    case "cust_read" :
    case "cust_update" :
    case "cust_delete" : {
        $cust = new RbCust ();
        $db = JFactory::getDbo();
        $db->transactionStart();
        $res = true;
        switch ($cmd) {
            case "cust_create" : {
                $res = $cust->createObject();
                break;
            }
            case "cust_read" : {
                $res = $cust->readObject();
                break;
            }
            case "cust_update" : {
                $res = $cust->updateObject();
                break;
            }
            case "cust_delete" : {
                $res = $cust->deleteObject();
                break;
            }
        }
        if ($res) {
            $db->transactionCommit();
        } else {
            $db->transactionRollback();
        }

        $cust->echoResponse();
        break;
    }

    // ==================================== reports ==========================
    case   "report_in_stock": {
        echo json_encode(RbProducts::getProductInStock());
        break;
    }

    case   "send_oper_report": {
        RbOpers::getEverydayReport();
        echo "report sent";
        break;
    }

    // ==================================== firms ==========================
    case   "get_firm_list": {
        $frm = new RbFirms();
        $frm->getFirmList();
        break;
    }

    default : {
        echo "";
    }
}

