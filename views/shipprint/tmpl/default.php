<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/views/shipprint/tmpl/shipprint.js"></script>

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
  <div style="visibility: hidden" id="invid_transfer"><?php
  $input = JFactory::getApplication ()->input;
  echo $input->getCmd ('invid');
  ?>
  </div>

  <table class="table_no_border">
    <tr>
      <td style="width: 10%">Поставщик:</td>
      <td style="width: 90%" id="inv_firm">ххООО Робик.РУхх</td>
    </tr>
    <tr>
      <td style="width: 10%">Покупатель:</td>
      <td style="width: 90%" id="inv_cust">ххПокупательхх</td>
    </tr>
    <tr>
      <td style="width: 10%">Основание:</td>
      <td style="width: 90%" id="inv_based_on">ххОснованиехх</td>
    </tr>
  </table>

  <div class="inv_header">
    <div class="inv_num">
      Накладная <span id="inv_ship_num"></span> от <span
        id="inv_ship_date"></span>
    </div>
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

  <table class="table_no_border inv_sign">
    <tr>
      <td id="stamp_anchor">Отпустил</td>
      <td>
        <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
      </td>
      <td>Получил</td>
      <td>
        <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
      </td>
    </tr>
  </table>

  <!-- img id="img_sign" class="img_sign" src=""/-->
  <img id="img_stamp" class="img_stamp" src="" />

</body>
</html>
