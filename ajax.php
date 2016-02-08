<?php
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
require_once "models/RbDocsProducts.php";
require_once "models/RbCust.php";
require_once "models/RbOpers.php";
require_once "models/RbFirms.php";
JLog::addLogger(array('text_file' => 'com_rbo.php'), JLog::ALL, array('com_rbo'));

$app = JFactory::getApplication('site');
$app->initialise();

RbHelper::checkAccess();

$input = $app->input;
$cmd = $input->getCmd('task');

switch ($cmd) {
    case "add_trigger" : {
        $db = JFactory::getDBO();
        $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_insert.sql'));
        $result = $db->execute();

        $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_delete.sql'));
        $result = $db->execute();

        $db->setQuery(file_get_contents(RBO_PATH . '/admin/install.com_rbo_trigger_update.sql'));
        $result = $db->execute();

        break;
    }

    case "get_current_date" : {
        $currentTime = new JDate ();
        $res = new stdClass ();
        $res->new_date = $currentTime->format('d.m.Y', true);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        break;
    }

    // ==================================== doc ==========================
    case "get_doc_list" : {
        $docList = new RbDocs ();
        $docList->getDocList(true);
        break;
    }

    case "doc_read" : {
        $doc = new RbDocs ();
        $doc->readObject(true);
        break;
    }

    case "doc_create" : {
        $doc = new RbDocs ();
        $doc->createObject(true);
        break;
    }

    case "doc_update" : {
        $doc = new RbDocs ();
        $doc->updateObject(true);
        break;
    }

    case "doc_delete" : {
        $doc = new RbDocs ();
        $doc->deleteObject(true);
        break;
    }

    case "doc_copy" : {
        $doc = new RbDocs ();
        $doc->copyDocTo(true);
        break;
    }

    case "get_doc_num" : {
        $doc = new RbDocs ();
        $doc->getNextDocNumber(true);
        break;
    }

    // ==================================== opers ==========================
    case "get_oper_list" : {
        $operList = new RbOpers ();
        $operList->getOperList();
        echo $operList->getResponse();
        break;
    }

    case "oper_read" : {
        $oper = new RbOpers ();
        $oper->readObject();
        echo $oper->getResponse();
        break;
    }

    case "oper_create" : {
        $oper = new RbOpers ();
        $oper->createObject();
        echo $oper->getResponse();
        break;
    }

    case "oper_update" : {
        $oper = new RbOpers ();
        $oper->updateObject();
        echo $oper->getResponse();
        break;
    }

    case "oper_delete" : {
        $oper = new RbOpers ();
        $oper->deleteObject();
        echo $oper->getResponse();
        break;
    }

    // ==================================== product ==========================
    case "product_search" : {
        RbProducts::getProductListForm();
        break;
    }

    case "get_product_list" : {
        RbProducts::getProductList();
        break;
    }

    case "product_read" : {
        $prd = new RbProducts ();
        $prd->readObject(true);
        break;
    }

    case "product_create" : {
        $prd = new RbProducts ();
        $prd->createObject(true);
        break;
    }

    case "product_update" : {
        $prd = new RbProducts ();
        $prd->updateObject(true);
        break;
    }

    case "product_delete" : {
        $prd = new RbProducts ();
        $prd->deleteObject(true);
        break;
    }

    // ==================================== customer ==========================
    case "cust_search" : {
        $cust = new RbCust ();
        $cust->getCustListBySubstr();
        break;
    }

    case "get_cust_list" : {
        RbCust::getCustList();
        break;
    }

    case "cust_read" : {
        $cst = new RbCust ();
        $cst->readObject(true);
        break;
    }

    case "cust_create" : {
        $cst = new RbCust ();
        $cst->createObject(true);
        break;
    }

    case "cust_update" : {
        $cst = new RbCust ();
        $cst->updateObject(true);
        break;
    }

    case "cust_delete" : {
        $cst = new RbCust ();
        $cst->deleteObject(true);
        break;
    }

    // ==================================== customer ==========================
    case   "report_in_stock": {
        RbProducts::getProductInStock();
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



