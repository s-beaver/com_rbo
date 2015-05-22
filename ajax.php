<?php
define ('_JEXEC', 1);
define ('DS', DIRECTORY_SEPARATOR);

if (file_exists (dirname (__FILE__) . '/defines.php')) {
  include_once dirname (__FILE__) . '/defines.php';
}
if (! defined ('_JDEFINES')) {
  define ('RBO_PATH', realpath (dirname (__FILE__)));
  define ('JPATH_BASE', realpath (dirname (__FILE__) . "/../.."));
  require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';
include_once "models/rbohelper.php";
JLog::addLogger (array ('text_file' => 'com_rbo.php' ), JLog::ALL, array ('com_rbo' ));

$app = JFactory::getApplication ('site');
$app->initialise ();
$user = JFactory::getUser ();
$input = $app->input;
$cmd = $input->getCmd ('task');

switch ($cmd) {
  case "get_invoice_list" :
    {
      include_once "models/invoicelist.php";
      $invList = new RbOInvoiceList ();
      echo $invList->getinvList ();
      break;
    }
  
  case "invoice_read" :
    {
      include_once "models/invoice.php";
      $inv = new RbOInvoice ();
      $inv->readObject ();
      echo $inv->getResponse ();
      break;
    }
  
  case "invoice_create" :
    {
      include_once "models/invoice.php";
      $inv = new RbOInvoice ();
      $inv->createObject ();
      echo $inv->getResponse ();
      break;
    }
  
  case "invoice_update" :
    {
      include_once "models/invoice.php";
      $inv = new RbOInvoice ();
      $inv->updateObject ();
      echo $inv->getResponse ();
      break;
    }
  
  case "invoice_delete" :
    {
      include_once "models/invoice.php";
      $inv = new RbOInvoice ();
      $inv->deleteObject ();
      echo $inv->getResponse ();
      break;
    }
  
  case "product_search" :
    {
      RbOHelper::getProductListBySubstr ();
      break;
    }
  
  case "cust_search" :
    {
      include_once "models/rbocust.php";
      $cust = new RbOCust();
      $cust->getCustListBySubstr ();
      break;
    }
  
  case "get_inv_num" :
    {
      RbOHelper::getNextDocNumber ('счет');
      break;
    }
  
  default :
    {
      echo "";
    }
}



