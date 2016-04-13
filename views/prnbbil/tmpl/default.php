<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PrnBBill.js"></script>

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

        .doc_num {
            font-size: 150%;
            font-weight: bold;
            margin: 10px;
        }

        .doc_recv, .doc_header, .doc_sum_words {
            font-size: 10pt;
        }

        .doc_products {
            width: 100%;
            padding: 2px;
        }

        .doc_products thead * {
            text-align: center;
            font-weight: bold;
        }

        [class~=doc_sign] {
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
    $input = JFactory::getApplication()->input;
    echo $input->getCmd('docid');
    ?>
</div>

<table class="table_no_border">
    <tr>
        <td style="width: 10%">Поставщик:</td>
        <td style="width: 90%" id="doc_cust">ххПоставщикхх</td>
    </tr>
    <tr>
        <td style="width: 10%">Покупатель:</td>
        <td style="width: 90%" id="doc_firm">ххООО Робик.РУхх</td>
    </tr>
    <tr>
        <td style="width: 10%">Основание:</td>
        <td style="width: 90%" id="doc_based_on">ххОснованиехх</td>
    </tr>
</table>

<div class="doc_header">
    <div class="doc_num">
        Накладная <span id="doc_num"></span> от <span id="doc_date"></span>
    </div>
</div>

<table class="doc_products">
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
    <tbody id="doc_products"></tbody>
    <tfoot id="doc_products">
    <tr>
        <td colspan=3>Итого</td>
        <td id="doc_cnt_sum" style='text-align: center'>ххКвоИтогохх</td>
        <td>&nbsp;</td>
        <td id="doc_sum" style='text-align: right'>ххСуммаИтогохх</td>
    </tr>
    </tfoot>
</table>

<div class="doc_sum_words">
    Сумма прописью: <span id="doc_sum_words"></span>. Без НДС.
</div>

<table class="table_no_border doc_sign">
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
<!-- img id="img_stamp" class="img_stamp" src="" /-->

</body>
</html>
