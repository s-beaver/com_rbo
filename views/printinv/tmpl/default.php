<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/models/printinv.js"></script>

<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
}

table {
	border-collapse: collapse;
}

table, th, td {
	border: 1px solid black;
	padding: 5px;
}

.table_no_border {
	border: 0;
	width: 100%;
}

.table_no_border tr td {
	border: 0;
}

.inv_num {
	font-size: 150%;
	font-weight: bold;
	margin: 10px;
}

.inv_recv, .inv_header, .inv_sum_words {
	font-size: 10pt;
}

.inv_products {
	width: 100%;
	padding: 2px;
}

.inv_products thead * {
	text-align: center;
	font-weight: bold;
}

[class~=inv_sign] {
	font-size: 10pt;
	text-align: center;
}

[class~=underlined] {
	border-bottom-style: solid;
	border-width: 1px;
}

.img_stamp {
	position: absolute;
	left: 0px;
	top: 200px;
	height: 32mm;
	width: 32mm;
	overflow: visible;
	z-index: -1;
}
</style>

</head>
<body style="width: 180mm;">
  <div style="visibility: hidden" id="docid_transfer"><?php
  $input = JFactory::getApplication ()->input;
  echo $input->getCmd ('docid');
  ?>
  </div>
  <table class="table_no_border">
    <tr>
      <td>
        <img src="components/com_rbo/images/logo.jpg"
          style="width: 54mm;" />
      </td>
      <td>Внимание! Оплата данного счета означает согласие с условиями
        поставки товара. Уведомление об оплате обязательно, в противном
        случае не гарантируется наличие товара на складе. Товар
        отпускается по факту прихода денег на р/с Поставщика,
        самовывозом, при наличии доверенности и паспорта</td>
    </tr>
  </table>
  <br>

  <div class="inv_recv">
    <table style="width: 100%">
      <tr>
        <td colspan=2 rowspan=2 id="f_bank">ххБАНКхх</td>
        <td>БИК</td>
        <td rowspan=2>
          <span id="f_bik">ххБИКхх</span><br> <span id="f_kch">ххСч.№хх</span>
        </td>
      </tr>
      <tr>
        <td>Сч.№</td>
      </tr>
      <tr>
        <td>
          ИНН <span id="f_inn">ххИННхх</span>
        </td>
        <td>
          КПП <span id="f_kpp">ххКППхх</span>
        </td>
        <td rowspan=2>Сч.№</td>
        <td rowspan=2 id="f_rch">ххСч.№хх</td>
      </tr>
      <tr>
        <td colspan=2 id="f_name">ххПолучательхх</td>
      </tr>
    </table>
  </div>

  <div class="inv_header">
    <div class="inv_num">
      Счет <span id="inv_num"></span> от <span id="inv_date"></span>
    </div>
    <div>Счет действителен в течение 3 банковских дней с даты его
      выставления</div>
    <hr noshade>
    <table class="table_no_border">
      <tr>
        <td style="width: 10%">Поставщик:</td>
        <td style="width: 90%" id="inv_firm">ххООО Робик.РУхх</td>
      </tr>
      <tr>
        <td style="width: 10%">Покупатель:</td>
        <td style="width: 90%" id="inv_cust">ххПокупательхх</td>
      </tr>
    </table>
  </div>

  <table class="inv_products">
    <thead>
      <tr>
        <td>№</td>
        <td>Товары (работы, услуги)</td>
        <td>Ед.изм</td>
        <td>К-во</td>
        <td>Цена, руб</td>
        <td>Сумма, руб</td>
      </tr>
    </thead>
    <tbody id="inv_products"></tbody>
    <tfoot id="inv_products">
      <tr>
        <td colspan=3>Итого</td>
        <td id="inv_cnt_sum" style='text-align: center'>ххКвоИтогохх</td>
        <td>&nbsp;</td>
        <td id="inv_sum" style='text-align: right'>ххСуммаИтогохх</td>
      </tr>
    </tfoot>
  </table>
  
  <div class="inv_sum_words">
    Сумма прописью: <span id="inv_sum_words"></span>. Без НДС.
  </div>

  <div class="inv_condition"></div>
  <hr noshade>
  <br>

  <table class="table_no_border inv_sign">
    <tr>
      <td>Менеджер</td>
      <td>
        <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
      </td>
      <td id="inv_manager" class="underlined">ххМенеджерхх</td>
    </tr>
  </table>

  <!-- img id="img_sign" class="img_sign" src=""/-->
  <img id="img_stamp" class="img_stamp" src="" />

</body>
</html>
