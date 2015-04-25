<?php 
jimport('etc.json_lib');

class RbOInvoice
{
	//public $user_email="";
	//public $user_id="";

	public $invId="";
	public $inv_num="";
	public $inv_date="";
	public $inv_cust=array();
	public $inv_sum=0;
	public $inv_status="";
	public $inv_rem="";
	public $inv_firm="";
	public $inv_manager="";
	public $created_by="";
	public $created_on="";
	public $modified_by="";
	public $modified_on="";
	public $inv_products=array();

	public $command="";
	public $response="";
	public $oJson;

	//=================================================================
	public function __construct ($params) {

		$db = JFactory::getDBO();
		/*$this->user =& JFactory::getUser ();
		$this->user_email = $this->user->email;
		$this->user_id = $this->user->id;*/

		$this->oJson = new Services_JSON();

		$input = JFactory::getApplication()->input;
		$this->invId = $input->getString('invId','0');
		/*$this->inv_email = $input->getString('inv_email','');
		$this->inv_type = $input->getString('inv_type','');
		$this->inv_text = $input->getString('inv_text','');
		$this->inv_cnt = $input->getUint('inv_cnt',0);
		$this->inv_price = $input->getUint('inv_price');
		$this->inv_files = $input->get('inv_files',null,"ARRAY");
		$this->inv_status = $input->getString('inv_status','');  */
		$this->command = $input->getString('command','read');

		switch ($this->command) {
			case "read":
				$this->invRead($db);
				break;

			case "submit":

				if ($this->inv_num=="0") {//новый заказ
					$this->invCreate($db);
				}

				else { //редактируем существующий
					$this->invUpdate($db);
				}
				break;

			case "delete":
				$this->invDelete($db);
				break;
		}


	}

	//=================================================================
	public function getResponse()
	{
		return $this->response;
	}

	//=================================================================
	public function invRead($db)
	{
		$q = "SELECT order_num, order_date, order_cust, order_sum, order_status, order_rem, ".
                     "order_firm, order_manager, created_by, created_on, modified_by, modified_on ".
                     "FROM rbo_orders WHERE orderID=".$this->invId;
		$db->setQuery($q);
		$inv = $db->loadAssoc();
		//$inv["inv_email"] = $this->user_email;

		$this->response = $this->oJson->encode($inv);
	}

	//=================================================================
	public function invUpdate($db)
	{
		/*$phStatus="";
		if ($this->inv_status!="") $phStatus = ", phStatus='".$this->inv_status."' ";
		$q = "UPDATE phinv SET phDescr='".$this->inv_text."',".
				" phCount=".$this->inv_cnt.", ".
				" phPrice=".((is_null($this->inv_price))?"NULL":$this->inv_price).", ".
				" phType='".$this->inv_type."' ".$phStatus.
				" WHERE phNumber='".$this->inv_num."'";
		//dump($q,"upd");
		$db->setQuery($q);
		$db->query();

		$res["result"] = "success";
		$this->response = $this->oJson->encode($res);*/
	}

	//=================================================================
	public function invCreate($db)
	{
		/*$cur_date=date("d.m.Y");
		//$this->inv_num=date("md-His");//md-Hi
		//$inv_folder=date("ymd-His");
		$this->inv_num = $this->getUniqueinvNumber($db);

		$q = "INSERT INTO phinv (".
				"phNumber,".  //0 autoinc
				"phDescr,".   //1
				"phCount,".   //2
				"phPrice,".   //3
				"phType,".    //4
				"phStatus, ". //5
				"phUserID) ". //6

				"VALUES (".
				"'".$this->inv_num."',".  //0
				"'".$this->inv_text."',". //1
				$this->inv_cnt.",".   //2
				((is_null($this->inv_price))?"NULL":$this->inv_price).",". //3
				"'".$this->inv_type."',". //4
				"'новый',".                 //5
				$this->user_id.")";         //6
		//dump($q,"create");

		$db->setQuery($q);
		$db->query();

		$body = 'Поступил заказ на печать фото (Заказ N'.$this->inv_num.'). Детали заказа см на сайте http://robik.ru/home/zakaz-foto-on-line/'.$this->inv_num;

		SendEMail("s_efremov@mail.ru","Заказ N".$this->inv_num,$body);
		//SendEMail("hkolumb@yandex.ru","Заказ N".$this->inv_num,$body);

		//if (!is_dir(JPATH_BASE.DS.$this->rootfolder.$inv_folder)) $b = mkdir($this->rootfolder.$inv_folder,0777);
		$res["result"] = $this->inv_num;
		$this->response = $this->oJson->encode($res);*/
	}

	//=================================================================
	public function invDelete($db)
	{
		/*$q = "DELETE FROM phinv WHERE phNumber='".$this->inv_num."'";
		$db->setQuery($q);
		$this->response = $q;
		$db->query();
		//удалить каталог

		$res["result"] = "success";
		$this->response = $this->oJson->encode($res); */
	}

	//=================================================================
	public function getUniqueinvNumber($db)
	{
		//    return date("md-Hi");
		/*$uval=date("md-H");
		$min=(int)date("i");
		$db->setQuery("SELECT phNumber FROM phinv WHERE phNumber='".$uval.$min."'");
		$db->query();
		$ok = ($db->getAffectedRows()==0);
		$i = 0;
		while (!$ok && $i<=9) {
			$db->setQuery("SELECT phNumber FROM phinv WHERE phNumber='".$uval.(abs(--$min))."'");
			$db->query();
			if ($db->getAffectedRows ()==0) {
				$ok=true;
			}
			$i++;
		}
		return $uval.$min;*/
	}

}

//===================================================================
function SendEMail($address,$subject,$body)
{
	$enc_to=$address;
	$enc_subject=header_encode($subject);
	$enc_body=header_encode_body($body);

	$headers='';
	$headers.="Mime-Version: 1.0\r\n";
	$headers.="Content-type: text/plain; charset=windows-1251\r\n";
	$headers.="From: ".header_encode('Компания Робик.ру')." <shop@robik.ru>";
	$headers.="Return-path: <shop@robik.ru>";

	return mail($enc_to,$enc_subject,$enc_body,$headers,"-f shop@robik.ru");
}

//===================================================================
function header_encode($str)
{
	return mime_header_encode1($str,"CP1251","windows-1251");
}

//===================================================================
function header_encode_body($str)
{
	return mime_header_encode2($str,"CP1251","windows-1251");
}

//===================================================================
function mime_header_encode1($str, $data_charset, $send_charset) {
	if($data_charset != $send_charset) $str = iconv($data_charset, $send_charset, $str);
	return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}

//===================================================================
function mime_header_encode2($str, $data_charset, $send_charset) {
	if($data_charset != $send_charset) $str = iconv($data_charset, $send_charset, $str);
	return $str;
}



