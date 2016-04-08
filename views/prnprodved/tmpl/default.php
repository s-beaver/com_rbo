<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PrnProdVed.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }

        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }

        #report_date {
            display: inline
        }

        .alert {
            background-color: yellow;
        }

    </style>

</head>
<body>
<a href="" id="open_dlg">
    <h2>Товарная ведомость
        <div id="report_params" style="display: inline;"></div>
    </h2>
</a>
<table id="report_table">
</table>

<div id="report_totals"></div>
<div id="report_debug"></div>

<div id="pv.rep-form" title="Введите параметры отчета">
    <form id="pv.rep-form-form" method="post" action="">
        <fieldset style="border: 0;">
            <label for="pv.date_start">Дата начала</label>
            <input type="text" name="pv.date_start" id="pv.date_start" style='text-align: left; width: 100px'/><br><br>
            <label for="pv.date_end">Дата окончания</label>
            <input type="text" name="pv.date_end" id="pv.date_end" style='text-align: left; width: 100px'/><br><br>
            <label for="pv.search">Подстрока поиска товара</label>
            <input type="text" name="pv.search" id="pv.search" style='width: 350px'/>
            <a href="" id="pv.search_btn"><img src="<?php echo JRBO_PATH ?>/images/icon-32-search-on.png"/></a>
        </fieldset>
    </form>
</div>


</body>
</html>
