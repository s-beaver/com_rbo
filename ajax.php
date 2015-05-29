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
include_once "models/rbodocument.php";
include_once "models/rbocust.php";
JLog::addLogger (array ('text_file' => 'com_rbo.php' ), JLog::ALL, array ('com_rbo' ));

$app = JFactory::getApplication ('site');
$app->initialise ();

RbOHelper::checkAccess();

$input = $app->input;
$cmd = $input->getCmd ('task');

switch ($cmd) {
  case "get_doc_list" :
    {
      $docList = new RbODocument ();
      $docList->getDocList();
      echo $docList->getResponse ();
      break;
    }
  
    case "doc_read" :
    {
      $doc = new RbODocument ();
      $doc->readObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_create" :
    {
      $doc = new RbODocument ();
      $doc->createObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_update" :
    {
      $doc = new RbODocument ();
      $doc->updateObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_delete" :
    {
      $doc = new RbODocument ();
      $doc->deleteObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "product_search" :
    {
      RbOProducts::getProductListBySubstr ();
      break;
    }
  
  case "cust_search" :
    {
      $cust = new RbOCust();
      $cust->getCustListBySubstr ();
      break;
    }
  
  case "get_doc_num" :
    {
      $doc = new RbODocument ();
      $doc->getNextDocNumber ();
      break;
    }
  
  default :
    {
      echo "";
    }
}



