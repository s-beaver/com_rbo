<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/views/torg12/tmpl/torg12.js"></script>

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
<body style="width: 280mm;">
  <div style="visibility: hidden" id="invid_transfer"><?php
  $input = JFactory::getApplication ()->input;
  echo $input->getCmd ('invid');
  ?>
  </div>

  <div style="text-align: right;">
    Унифицированная форма Торг-12<br>Утверждена постановлением
    Госкомстата России от 25.12.98 № 132
  </div>

  <div>
    <div class="top_left"
      style="float: left; width: 75%; height: 100%; vertical-align: top">
      <table class="table_no_border">
        <tr>
          <td style="width: 10%">Грузоотправитель</td>
          <td style="width: 90%" id="inv_firm">ххООО Робик.РУхх</td>
        </tr>
        <tr>
          <td style="width: 10%">Грузополучатель</td>
          <td style="width: 90%" id="inv_cust">ххПокупательхх</td>
        </tr>
        <tr>
          <td style="width: 10%">Поставщик</td>
          <td style="width: 90%" id="inv_firm">ххООО Робик.РУхх</td>
        </tr>
        <tr>
          <td style="width: 10%">Плательщик</td>
          <td style="width: 90%" id="inv_cust">ххПокупательхх</td>
        </tr>
        <tr>
          <td style="width: 10%">Основание:</td>
          <td style="width: 90%" id="inv_based_on">ххОснованиехх</td>
        </tr>
      </table>
      <table class="inv_header" align="center"
        style="border: 0; vertical-align: bottom;">
        <tr>
          <td rowspan="2" style="vertical-align: bottom; border: 0;">ТОВАРНАЯ
            НАКЛАДНАЯ</td>
          <td>Номер документа</td>
          <td>Дата составления</td>
        </tr>
        <tr>
          <td id="inv_ship_num">xxНомерхх</td>
          <td id="inv_ship_date">ххДатахх</td>
        </tr>
      </table>

    </div>

    <div class="top_right"
      style="float: left; width: 25%; vertical-align: top">
      <table style="float: right">
        <tr>
          <td colspan="2">&nbsp;</td>
          <td>Коды</td>
        </tr>
        <tr>
          <td colspan="2">Форма по ОКУД</td>
          <td>хх0330212хх</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">Виды деятельности по ОКДП</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">по ОКПО</td>
          <td>хх55774024хх</td>
        </tr>
        <tr>
          <td colspan="2">по ОКПО</td>
          <td>хх65612151хх</td>
        </tr>
        <tr>
          <td colspan="2">по ОКПО</td>
          <td>хх55774024хх</td>
        </tr>
        <tr>
          <td colspan="2">номер</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">дата</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Транспортная</td>
          <td>номер</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>накладная</td>
          <td>дата</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">Вид операции</td>
          <td>&nbsp;</td>
        </tr>

      </table>
    </div>
  </div>
  <br>
  <table class="inv_products" style="width: 100%; border: 0;">
    <thead>
      <tr>
        <td rowspan="2">Номер по порядку</td>
        <td colspan="2">Товар</td>
        <td colspan="2">Ед.изм</td>
        <td rowspan="2">Вид упаковки</td>
        <td colspan="2">Количество</td>
        <td rowspan="2">Масса брутто</td>
        <td rowspan="2">Количество (масса нетто)</td>
        <td rowspan="2">Цена руб. коп.</td>
        <td rowspan="2">Сумма без учета НДС руб. коп.</td>
        <td colspan="2">НДС</td>
        <td rowspan="2">Сумма с учетом НДС руб. коп.</td>
      </tr>
      <tr>
        <td>наименование, характеристика, сорт, артикул товара</td>
        <td>код</td>
        <td>наиме-нование</td>
        <td>код по ОКЕИ</td>
        <td>в одном месте</td>
        <td>мест, штук</td>
        <td>ставка, %</td>
        <td>сумма руб. коп.</td>
      </tr>
      <tr>
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6</td>
        <td>7</td>
        <td>8</td>
        <td>9</td>
        <td>10</td>
        <td>11</td>
        <td>12</td>
        <td>13</td>
        <td>14</td>
        <td>15</td>
      </tr>
    </thead>
    <tbody id="inv_products"></tbody>
    <tfoot id="inv_products">
      <tr>
        <td colspan=7 style="text-align: right; border: 0;">Итого</td>
        <td>Х</td>
        <td>Х</td>
        <td id="inv_cnt_sum" style='text-align: center'>ххКвоИтогохх</td>
        <td>Х</td>
        <td id="inv_sum" style='text-align: right'>ххСуммаИтогохх</td>
        <td>Х</td>
        <td>0,00</td>
        <td id="inv_sum" style='text-align: right'>ххСуммаИтогохх</td>
      </tr>
      <tr>
        <td colspan=7 style="text-align: right; border: 0;">Сумма</td>
        <td>Х</td>
        <td>Х</td>
        <td id="inv_cnt_sum" style='text-align: center'>ххКвоИтогохх</td>
        <td>Х</td>
        <td id="inv_sum" style='text-align: right'>ххСуммаИтогохх</td>
        <td>Х</td>
        <td>0,00</td>
        <td id="inv_sum" style='text-align: right'>ххСуммаИтогохх</td>
      </tr>
    </tfoot>
  </table>

  <div>
    Товарная накладная имеет приложение на одном листе<br>и содержит
    один порядковых номеров записей
  </div>

  <table>
    <tr>
      <td rowspan="2">Всего мест</td>
      <td rowspan="2">_________________________</td>
      <td>Масса груза (нетто)</td>
      <td>_________________________</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td>Всего мест</td>
      <td>_________________________</td>
      <td>Масса груза (брутто)</td>
      <td>_________________________</td>
      <td>&nbsp;</td>
    </tr>
  </table>

  <div>Приложение (паспорта, сертификаты, и т. п.) на листах</div>

  <div style="float: left; width: 50%">
    <table class="table_no_border inv_sign">
      <tr>
        <td>
          Всего отпущено на сумму <span id="inv_sum_words"></span>. Без
          НДС.<br>
        </td>
      </tr>
      <tr>
        <td>
          <table>
            <tr>
              <td>Отпуск разрешил</td>
              <td>____________________</td>
              <td>____________________</td>
              <td>____________________</td>
            </tr>

            <tr>
              <td colspan="2">Главный (старший) бухгалтер</td>
              <td>____________________</td>
              <td>____________________</td>
            </tr>
            <tr>
              <td>Отпуск груза произвел</td>
              <td>____________________</td>
              <td>____________________</td>
              <td>____________________</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          М.П. <span>ххДатахх</span>
        </td>
      </tr>

    </table>
  </div>

  <div style="float: left; width: 50%">
    <div>По доверенности № от " " г.</div>
    <div>Выданной ______________________</div>
    <div>Груз принял ______________________</div>
    <div>Груз получил грузополучатель ______________________</div>
    <div>
      М.П. <span>ххДатахх</span>
    </div>
  </div>

</body>
</html>
