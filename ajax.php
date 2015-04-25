<?php define('_JEXEC', 1);   
define('DS', DIRECTORY_SEPARATOR);   

if (file_exists(dirname(__FILE__) . '/defines.php')) { 	
	include_once dirname(__FILE__) . '/defines.php'; 
}  
if (!defined('_JDEFINES')) { 	
	define('JPATH_BASE', realpath(dirname(__FILE__)."/../.."));
	require_once JPATH_BASE.'/includes/defines.php'; 
}  

require_once JPATH_BASE.'/includes/framework.php';  

$app = JFactory::getApplication('site'); 
$app->initialise();  
$user = JFactory::getUser(); 
//$db = JFactory::getDBO();
$input = $app->input;
$cmd = $input->getCmd('task');
//echo "ответ cервера user=".$user->email."; command=$cmd";

switch ($cmd) {
	case "get_invoice_list":{
		include_once "models/invoicelist.php";
		$invList = new RbOInvoiceList();
		echo $invList->getinvList();
		break;
	}
	
	case "invoice_read":{
		include_once "models/invoice.php";
		$inv = new RbOInvoice();
		$inv->invRead();
		echo $inv->getResponse();
		break;
	}

	case "invoice_submit":{
		include_once "models/invoice.php";
		$inv = new RbOInvoice();
		
		if ($inv->invId=="0") {//новый заказ
			$inv->invCreate();
		}
	
		else { //редактируем существующий
			$inv->invUpdate();
		}
		break;
	}
	
	case "invoice_delete": {
		include_once "models/invoice.php";
		$inv = new RbOInvoice();
		$inv->invDelete();
		break;
	}
	
	default: {
		echo "";		
	}
}



