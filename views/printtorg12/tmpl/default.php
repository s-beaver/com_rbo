<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/models/printtorg12.js"></script>

<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
}

table {
	border-collapse: collapse;
}

sup {
	font-size: 4pt;
	position: relative;
	top: -6px;
}

.inv_num {
	font-size: 150%;
	font-weight: bold;
	margin: 10px;
}

.inv_header {
	font-size: 8pt;
}

.inv_sum_words {
	font-size: 10pt;
}

.inv_products {
	width: 100%;
	padding: 2px;
	border: 1px solid;
	margin-top: 5px;
	margin-bottom: 5px;
}

.inv_products * {
	text-align: center;
	font-weight: normal;
	border: 1px solid;
}

.footer {
	border-collapse: separate;
	border-spacing: 6px;
}

.footer td {
	padding-left: 5px; /* Поля в ячейках */
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
  <div style="visibility: hidden" id="docid_transfer"><?php
  $input = JFactory::getApplication ()->input;
  echo $input->getCmd ('docid');
  ?>
  </div>

  <div style="text-align: right; font-size:6pt">
    Унифицированная форма Торг-12<br>Утверждена постановлением
    Госкомстата России от 25.12.98 № 132
  </div>

  <table style="width: 100%; vertical-align: top">
    <tr>
      <td colspan="4" style="width: 90%; text-align: right;">&nbsp;</td>
      <td style="width: 10%; border: 1px solid; text-align: center;">Коды</td>
    </tr>
    <tr>
      <td rowspan="2" style="width: 10%">Грузоотправитель</td>
      <td rowspan="2" style="width: 70%; border-bottom: 1px solid;"
        id="inv_firm">ххООО Робик.РУхх</td>
      <td colspan="2" style="width: 10%; text-align: right;">Форма по
        ОКУД</td>
      <td style="width: 10%; border: 1px solid; text-align: center;">0330212</td>
    </tr>
    <tr>
      <td colspan="2"
        style="width: 10%; border-bottom: 1px solid; text-align: right;">по
        ОКПО</td>
      <td id="firm_okpo" style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" style="width: 10%; text-align: right;">Виды
        деятельности по ОКДП</td>
      <td style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width: 10%">Грузополучатель</td>
      <td id="inv_cust" style="width: 70%; border-bottom: 1px solid">ххПокупательхх</td>
      <td colspan="2"
        style="width: 10%; border-bottom: 1px solid; text-align: right;">по
        ОКПО</td>
      <td id="cust_okpo" style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width: 10%">Поставщик</td>
      <td id="inv_firm" style="width: 70%; border-bottom: 1px solid;" id="inv_firm">ххООО
        Робик.РУхх</td>
      <td colspan="2"
        style="width: 10%; border-bottom: 1px solid; text-align: right;">по
        ОКПО</td>
      <td id="firm_okpo" style="width: 10%; border: 1px solid; text-align: center;">хх55774024хх</td>
    </tr>
    <tr>
      <td style="width: 10%">Плательщик</td>
      <td id="inv_cust" style="width: 70%; border-bottom: 1px solid">ххПокупательхх</td>
      <td colspan="2"
        style="width: 10%; border-bottom: 1px solid; text-align: right;">по
        ОКПО</td>
      <td id="cust_okpo" style="width: 10%; border: 1px solid; text-align: center;">хх55774024хх</td>
    </tr>
    <tr>
      <td style="width: 10%">Основание:</td>
      <td style="width: 70%; border-bottom: 1px solid;"
        id="inv_based_on">ххОснованиехх</td>
      <td colspan="2"
        style="width: 10%; border-bottom: 1px solid; text-align: right;">номер</td>
      <td style="width: 10%; border: 1px solid; text-align: center;">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" style="width: 10%; text-align: right;">дата</td>
      <td style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" rowspan="3" style="width: 60%; text-align: right;">

        <table class="inv_header" align="center"
          style="vertical-align: bottom; border: 0">
          <tr>
            <td rowspan="2" style="vertical-align: bottom; border: 0; font-size: 10pt">ТОВАРНАЯ
              НАКЛАДНАЯ</td>
            <td style="border: 1px solid">Номер документа</td>
            <td style="border: 1px solid">Дата составления</td>
          </tr>
          <tr>
            <td id="inv_ship_num" style="border: 1px solid; text-align:center">xxНомерхх</td>
            <td id="inv_ship_date" style="border: 1px solid; text-align:center">ххДатахх</td>
          </tr>
        </table>

      </td>
      <td style="width: 10%; text-align: right;">Транспортная</td>
      <td style="width: 10%; text-align: right;">номер</td>
      <td style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td style="width: 10%; text-align: right;">накладая</td>
      <td style="width: 10%; text-align: right;">дата</td>
      <td style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" style="width: 10%; text-align: right;">Вид
        операции</td>
      <td style="width: 10%; border: 1px solid;">&nbsp;</td>
    </tr>
  </table>

  <table class="inv_products">
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
        <td colspan=7 style="text-align: right; border: 0;">Всего по
          накладной</td>
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
    Товарная накладная имеет приложение на <span
      style="border-bottom: 2px solid;">&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    листах и содержит <span id="doc_cnt_words"
      style="border-bottom: 2px solid;"></span> порядковых номеров
    записей
  </div>

  <table style="width: 95%; border: 0">
    <tr>
      <td>Всего мест</td>
      <td style="width: 10cm; border-bottom: 1px solid">&nbsp;</td>
      <td>&nbsp;Масса груза (нетто)</td>
      <td style="width: 7cm; border-bottom: 1px solid">&nbsp;</td>
      <td style="width: 4cm; border: 2px solid">&nbsp;</td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;Масса груза (брутто)</td>
      <td style="border-bottom: 1px solid">&nbsp;</td>
      <td style="border: 2px solid">&nbsp;</td>
    </tr>
  </table>

  <div>Приложение (паспорта, сертификаты, и т. п.) на листах</div>

  <div
    style="float: left; width: 49%; border-right: 1px solid; margin-right: 5px; padding-right: 5px">
    <table class="footer">
      <tr>
        <td colspan="4" style="text-align: left">	
          Всего отпущено на сумму <span id="inv_sum_words" class="underlined"></span><span class="underlined">. Без
          НДС.</span>
        </td>
      </tr>
      <tr>
        <td style="text-align: left; width: 25%">Отпуск разрешил</td>
        <td style="width: 25%; border-bottom: 1px solid;">Руководитель
          организации</td>
        <td style="width: 25%; border-bottom: 1px solid;">&nbsp;</td>
        <td style="width: 25%; border-bottom: 1px solid;">&nbsp;</td>
      </tr>

      <tr>
        <td colspan="2" style="text-align: left;">Главный (старший)
          бухгалтер</td>
        <td
          style="width: 25%; text-align: center; border-bottom: 1px solid;">
          <sup>подпись</sup>
        </td>
        <td
          style="width: 25%; text-align: center; border-bottom: 1px solid;">
          <sup>расшифровка</sup>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;">Отпуск груза произвел</td>
        <td style="width: 25%; border-bottom: 1px solid;">&nbsp;</td>
        <td
          style="width: 25%; text-align: center; border-bottom: 1px solid;">
          <sup>подпись</sup>
        </td>
        <td
          style="width: 25%; text-align: center; border-bottom: 1px solid;">
          <sup>расшифровка</sup>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="text-align: center">
          <sup>подпись</sup>
        </td>
        <td style="text-align: center">
          <sup>расшифровка</sup>
        </td>
      </tr>
      <tr>
        <td colspan="4" style="text-align: center">
          М.П. <span id="doc_date_footer">ххДатахх</span>
        </td>
      </tr>
    </table>
  </div>

  <div style="float: left; width: 49%">
    <table class="footer">
      <tr>
        <td colspan="4" style="text-align: left">
          По доверенности №<span id="inv_sum_words"></span>. Без НДС.<br>выданной
        </td>
      </tr>
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align: left;">Груз принял</td>
        <td style="width: 22%; border-bottom: 1px solid;">&nbsp;</td>
        <td
          style="width: 22%; text-align: center; border-bottom: 1px solid;">&nbsp;</td>
        <td
          style="width: 22%; text-align: center; border-bottom: 1px solid;">&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align: left;">Груз получил грузополучатель</td>
        <td style="border-bottom: 1px solid;">&nbsp;</td>
        <td style="text-align: center; border-bottom: 1px solid;">
          <sup>подпись</sup>
        </td>
        <td style="text-align: center; border-bottom: 1px solid;">
          <sup>расшифровка</sup>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;">&nbsp;</td>
        <td>&nbsp;</td>

        <td style="text-align: center">
          <sup>подпись</sup>
        </td>
        <td style="text-align: center">
          <sup>расшифровка</sup>
        </td>
      </tr>
      <tr>
        <td colspan="4" style="text-align: center">
          М.П. <span id="doc_date_footer">ххДатахх</span>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
