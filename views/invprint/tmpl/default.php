<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/views/invprint/tmpl/invprint.js"></script>

<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
}

.width {
	display: table;
	width: 100%;
	border1: solid 1px grey;
}

.width10 {
	display: table-cell;
	width: 10%;
}

.width20 {
	display: table-cell;
	width: 20%;
}

.width30 {
	display: table-cell;
	width: 30%;
}

.width40 {
	display: table-cell;
	width: 40%;
}

.width50 {
	display: table-cell;
	width: 50%;
}

.width60 {
	display: table-cell;
	width: 60%;
}

.width70 {
	display: table-cell;
	width: 70%;
}

.width80 {
	display: table-cell;
	width: 80%;
}

.width90 {
	display: table-cell;
	width: 90%;
}

.width100 {
	display: table-cell;
	width: 100%;
}

[class~=row] {
	display: table-row;
}

table {
	border-collapse: collapse;
}

table, th, td {
	border: 1px solid black;
	padding: 5px;
}

.inv_num {
	font-size: 150%;
	font-weight: bold;
	margin: 10px;
}

.inv_recv, .inv_header {
	font-size: 10pt;
}

.inv_sign {
	font-size: 10pt;
	text-align: center;
}

[class~=underlined] {
	border-bottom-style: solid;
	border-width: 1px;
}

.inv_products {
	width: 100%;
}
</style>

</head>
<body style="width: 180mm">
  <div class="inv_top">
    <div class="width30">===ЛОГОТИП===</div>
    <div class="width70">Внимание! Оплата данного счета означает
      согласие с условиями поставки товара. Уведомление об оплате
      обязательно, в противном случае не гарантируется наличие товара на
      складе. Товар отпускается по факту прихода денег на р/с
      Поставщика, самовывозом, при наличии доверенности и паспорта</div>
  </div>
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
    <div class="row">
      <div class="width10">Поставщик:</div>
      <div class="width90" id="inv_firm">ххООО Робик.РУхх</div>
    </div>
    <div class="row">
      <div class="width10">Покупатель:</div>
      <div class="width90" id="inv_cust">ххПокупательхх</div>
    </div>
  </div>

  <table class="inv_products">
    <thead>
      <tr>
        <td>№</td>
        <td>Товары (работы, услуги)</td>
        <td>К-во</td>
        <td>Цена</td>
        <td>Сумма</td>
      </tr>
    </thead>
    <tbody id="inv_products"></tbody>
  </table>

  <div class="inv_condition"></div>
  <hr noshade>
  <br>
  <div class="inv_sign">
    <div class="width30">Менеджер</div>
    <div class="width40">
      <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
      <div>подпись</div>
    </div>
    <div class="width30">
      <div id="inv_manager" class="underlined">ххМенеджерхх</div>
      <div>расшифровка</div>
    </div>
  </div>
</body>
</html>
