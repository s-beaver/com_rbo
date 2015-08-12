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
include_once "models/rbo_docs.php";
include_once "models/rbo_products.php";
include_once "models/rbo_cust.php";
include_once "models/rbo_opers.php";
JLog::addLogger (array ('text_file' => 'com_rbo.php' ), JLog::ALL, array ('com_rbo' ));

$app = JFactory::getApplication ('site');
$app->initialise ();

RbOHelper::checkAccess ();

$input = $app->input;
$cmd = $input->getCmd ('task');

switch ($cmd) {
  case "get_current_date" :
    {
      $currentTime=new JDate ();
      $res = new stdClass();
      $res->new_date = $currentTime->format ('d.m.Y', true); 
      echo json_encode ($res, JSON_UNESCAPED_UNICODE);
      break;
    }
  
  case "get_doc_list" :
    {
      $docList = new RbODocs ();
      $docList->getDocList ();
      echo $docList->getResponse ();
      break;
    }
  
  case "doc_read" :
    {
      $doc = new RbODocs ();
      $doc->readObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_create" :
    {
      $doc = new RbODocs ();
      $doc->createObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_update" :
    {
      $doc = new RbODocs ();
      $doc->updateObject ();
      echo $doc->getResponse ();
      break;
    }
  
  case "doc_delete" :
    {
      $doc = new RbODocs ();
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
      $cust = new RbOCust ();
      $cust->getCustListBySubstr ();
      break;
    }
  
  case "get_doc_num" :
    {
      $doc = new RbODocs ();
      $doc->getNextDocNumber ();
      break;
    }
  
  case "get_oper_list" :
    {
      $operList = new RbOpers ();
      $operList->getOperList ();
      echo $operList->getResponse ();
      break;
    }
  
  case "oper_read" :
    {
      $oper = new RbOpers ();
      $oper->readObject ();
      echo $oper->getResponse ();
      break;
    }
  
  case "oper_create" :
    {
      $oper = new RbOpers ();
      $oper->createObject ();
      echo $oper->getResponse ();
      break;
    }
  
  case "oper_update" :
    {
      $oper = new RbOpers ();
      $oper->updateObject ();
      echo $oper->getResponse ();
      break;
    }
  
  case "oper_delete" :
    {
      $oper = new RbOpers ();
      $oper->deleteObject ();
      echo $oper->getResponse ();
      break;
    }
  
  default :
    {
      echo "";
    }
}



