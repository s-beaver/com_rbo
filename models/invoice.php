<?php
jimport ('etc.json_lib');
class RbOInvoice {
 // public $user_email="";
 // public $user_id="";
 private $db;
 private $command = "";
 private $response = "";
 private $oJson;
 public $invId = "";
 public $inv_num = "";
 public $inv_date = "";
 public $inv_cust = array ();
 public $inv_sum = 0;
 public $inv_status = "";
 public $inv_rem = "";
 public $inv_firm = "";
 public $inv_manager = "";
 public $created_by = "";
 public $created_on = "";
 public $modified_by = "";
 public $modified_on = "";
 public $inv_products = array ();
 
 // =================================================================
 public function __construct() {
  $this->db = JFactory::getDBO ();
  /*
   * $this->user =& JFactory::getUser ();
   * $this->user_email = $this->user->email;
   * $this->user_id = $this->user->id;
   */
  
  $this->oJson = new Services_JSON ();
  
  $input = JFactory::getApplication ()->input;
  $this->invId = $input->getString ('invId', '0');
  $this->inv_num = $input->getString ('inv_num');
  $this->inv_date = $input->getString ('inv_date');
  $this->inv_sum = $input->getString ('inv_sum');
  $this->inv_manager = $input->getString ('inv_manager', '');
  $this->inv_cust = $input->getString ('inv_cust', '');
  $this->inv_firm = $input->getString ('inv_firm', '');
  
  /*
   * $this->inv_email = $input->getString('inv_email','');
   * $this->inv_type = $input->getString('inv_type','');
   * $this->inv_text = $input->getString('inv_text','');
   * $this->inv_cnt = $input->getUint('inv_cnt',0);
   * $this->inv_price = $input->getUint('inv_price');
   * $this->inv_files = $input->get('inv_files',null,"ARRAY");
   * $this->inv_status = $input->getString('inv_status','');
   */
 }
 
 // =================================================================
 public function getResponse() {
  return $this->response;
 }
 
 // =================================================================
 public function invRead() {
  $q = "SELECT inv_num, inv_date, inv_cust, inv_sum, inv_status, inv_rem, " .
     "inv_firm, inv_manager, created_by, created_on, modified_by, modified_on " .
     "FROM rbo_invoices WHERE invId=" . $this->invId;
  
  $this->db->setQuery ($q);
  $inv = $this->db->loadAssoc ();
  //$this->invId = $inv ["invId"];
  $inv ["invId"] = $this->invId;
  $this->inv_num = $inv ["inv_num"];
  $this->inv_date = $inv ["inv_date"];
  $this->inv_sum = $inv ["inv_sum"];
  $this->inv_status = $inv ["inv_status"];
  $this->inv_rem = $inv ["inv_rem"];
  $this->inv_firm = $inv ["inv_firm"];
  $this->inv_manager = $inv ["inv_manager"];
  $this->created_by = $inv ["created_by"];
  $this->created_on = $inv ["created_on"];
  $this->modified_by = $inv ["modified_by"];
  $this->modified_on = $inv ["modified_on"];
  
  $q = "SELECT product_code, product_name, product_cnt, product_price, product_cnt*product_price FROM rbo_invoices_products WHERE inv_num=" .
     $inv ["inv_num"];
  $this->db->setQuery ($q);
  $inv ["inv_products"] = $this->db->loadRowList ();
  
  // $["inv_cust"]=array();
  // $["inv_products"]=array();
  
  $this->response = $this->oJson->encode ($inv);
 }
 
 // =================================================================
 public function invUpdate() {
  $q = "UPDATE rbo_invoices SET inv_num=" . $this->inv_num . ", inv_date=STR_TO_DATE('" .
     $this->inv_date . "','%d.%m.%Y'), inv_cust='" . $this->inv_cust . "', inv_sum='" .
     $this->inv_sum . "', inv_status='" . $this->inv_status . "', inv_rem='" . $this->inv_rem . "' " .
     "inv_firm='" . $this->inv_firm . "', inv_manager='" . $this->inv_text . "', modified_by='" .
     $this->modified_by . "', modified_on='" . $this->modified_on . "' " .
     "FROM rbo_invoices WHERE invId=" . $this->invId;
  
  $res ["result"] = $q;
  $this->response = $this->oJson->encode ($res);
  return;
  
  $this->db->setQuery ($q);
  $this->db->query ();
  
  $res ["result"] = "success";
  $this->response = $this->oJson->encode ($res);
 }
 
 // =================================================================
 public function invCreate() {
  /*
   * $cur_date=date("d.m.Y");
   * //$this->inv_num=date("md-His");//md-Hi
   * //$inv_folder=date("ymd-His");
   * $this->inv_num = $this->getUniqueinvNumber();
   *
   * $q = "INSERT INTO phinv (".
   * "phNumber,". //0 autoinc
   * "phDescr,". //1
   * "phCount,". //2
   * "phPrice,". //3
   * "phType,". //4
   * "phStatus, ". //5
   * "phUserID) ". //6
   *
   * "VALUES (".
   * "'".$this->inv_num."',". //0
   * "'".$this->inv_text."',". //1
   * $this->inv_cnt.",". //2
   * ((is_null($this->inv_price))?"NULL":$this->inv_price).",". //3
   * "'".$this->inv_type."',". //4
   * "'новый',". //5
   * $this->user_id.")"; //6
   * //dump($q,"create");
   *
   * $this->db->setQuery($q);
   * $this->db->query();
   *
   * $body = 'Поступил заказ на печать фото (Заказ N'.$this->inv_num.').
   * Детали заказа см на сайте
   * http://robik.ru/home/zakaz-foto-on-line/'.$this->inv_num;
   *
   * SendEMail ( "s_efremov@mail.ru", "Заказ N" . $this->inv_num, $body );
   * //SendEMail("hkolumb@yandex.ru","Заказ N".$this->inv_num,$body);
   *
   * //if (!is_dir(JPATH_BASE.DS.$this->rootfolder.$inv_folder)) $b =
   * mkdir ( $this->rootfolder . $inv_folder, 0777 );
   * $res ["result"] = $this->inv_num;
   * $this->response = $this->oJson->encode ( $res );
   * }
   *
   * // =================================================================
   * public function invDelete() {
   * /*
   * $q = "DELETE FROM phinv WHERE phNumber='".$this->inv_num."'";
   * $this->db->setQuery($q);
   * $this->response = $q;
   * $this->db->query();
   * //удалить каталог
   *
   * $res["result"] = "success";
   * $this->response = $this->oJson->encode($res);
   */
 }
 
 // =================================================================
 public function getUniqueinvNumber() {
  // return date("md-Hi");
  /*
   * $uval=date("md-H");
   * $min=(int)date("i");
   * $this->db->setQuery("SELECT phNumber FROM phinv WHERE
   * phNumber='".$uval.$min."'");
   * $this->db->query();
   * $ok = ($this->db->getAffectedRows()==0);
   * $i = 0;
   * while (!$ok && $i<=9) {
   * $this->db->setQuery("SELECT phNumber FROM phinv WHERE
   * phNumber='".$uval.(abs(--$min))."'");
   * $this->db->query();
   * if ($this->db->getAffectedRows ()==0) {
   * $ok=true;
   * }
   * $i++;
   * }
   * return $uval.$min;
   */
 }
}

// ===================================================================
function SendEMail($address, $subject, $body) {
 $enc_to = $address;
 $enc_subject = header_encode ($subject);
 $enc_body = header_encode_body ($body);
 
 $headers = '';
 $headers .= "Mime-Version: 1.0\r\n";
 $headers .= "Content-type: text/plain; charset=windows-1251\r\n";
 $headers .= "From: " . header_encode ('Компания Робик.ру') . " <shop@robik.ru>";
 $headers .= "Return-path: <shop@robik.ru>";
 
 return mail ($enc_to, $enc_subject, $enc_body, $headers, "-f shop@robik.ru");
}

// ===================================================================
function header_encode($str) {
 return mime_header_encode1 ($str, "CP1251", "windows-1251");
}

// ===================================================================
function header_encode_body($str) {
 return mime_header_encode2 ($str, "CP1251", "windows-1251");
}

// ===================================================================
function mime_header_encode1($str, $data_charset, $send_charset) {
 if ($data_charset != $send_charset)
  $str = iconv ($data_charset, $send_charset, $str);
 return '=?' . $send_charset . '?B?' . base64_encode ($str) . '?=';
}

// ===================================================================
function mime_header_encode2($str, $data_charset, $send_charset) {
 if ($data_charset != $send_charset)
  $str = iconv ($data_charset, $send_charset, $str);
 return $str;
}



