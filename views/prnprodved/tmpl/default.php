<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbDoc.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbProduct.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbCust.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PrnProdVed.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }

        #report_table {
            border-collapse: collapse;
        }

        #report_table td {
            border: 1px solid black;
            padding: 5px;
        }

        fieldset {
            border-width: 0;
        }

        .alert {
            background-color: yellow;
        }

        fieldset * {
            margin-top: 4px;
        }

    </style>

</head>
<body>
<a href="" id="open_dlg">
    <h2>Товарная ведомость
        <div id="report_params" style="display: inline;"></div>
    </h2>
</a>

<div id="progressbar"></div>
<table id="report_table">
</table>

<div id="report_totals"></div>
<div id="report_debug"></div>

<div id="pv.rep-form" title="Введите параметры отчета">
    <form id="pv.rep-form-form" method="post" action="">
        <fieldset style="border: 0;">
            <div>
                <label for="pv.date_start">Период от</label>
                <input type="text" name="pv.date_start" id="pv.date_start" style='text-align: left; width: 100px'/>
                <label for="pv.date_end">&nbsp;до</label>
                <input type="text" name="pv.date_end" id="pv.date_end" style='text-align: left; width: 100px'/>
            </div>
            <div>
                <label for="pv.firm">&nbsp;Фирма</label>
                <select id="pv.firm" name="pv.firm" style="width: 120px"></select>
                <label for="pv.manager">&nbsp;Менеджер</label>
                <select id="pv.manager" name="pv.manager" style="width: 120px"></select>
            </div>
            <br>
            <hr>
            <div>
                <label for="pv.search">Поиск</label>
                <input type="text" name="pv.search" id="pv.search" style='width: 300px'/>
                <a href="" id="_pv.prod_search_btn">
                    <img src="<?php echo JRBO_PATH ?>/images/icon-32-search-on.png" width="32" height="32"/>
                </a>
                <a href="" id="_pv.prod_search_delete_btn">
                    <img src="<?php echo JRBO_PATH ?>/images/icon-32-delete-on.png" width="32" height="32"/>
                </a>
            </div>
            <div>
                <label for="pv.product_name">Товар</label>
                <select id="pv.product_name" name="pv.product_name" style="width: 400px"></select>
            </div>
            <br>
            <hr>
            <div>
                <label for="pv.cust">Поиск</label>
                <input type="text" name="pv.cust" id="pv.cust" style='width: 300px'/>
                <a href="" id="_pv.cust_search_btn">
                    <img src="<?php echo JRBO_PATH ?>/images/icon-32-search-on.png" width="32" height="32"/>
                </a>
                <a href="" id="_pv.cust_search_delete_btn">
                    <img src="<?php echo JRBO_PATH ?>/images/icon-32-delete-on.png" width="32" height="32"/>
                </a>
            </div>
            <div>
                <label for="pv.cust_name">Контрагент</label>
                <select id="pv.cust_name" name="pv.cust_name" style="width: 400px"></select>
            </div>
        </fieldset>
    </form>
</div>

<?php include RBO_PATH.'/views/form.doc-s-bill.php'?>
<?php include RBO_PATH.'/views/form.doc-b-bill.php'?>
<?php include RBO_PATH.'/views/form.product.php'?>
<?php include RBO_PATH.'/views/form.customer.php'?>
<?php include RBO_PATH.'/views/form.base-doc.php'?>
<?php include RBO_PATH.'/views/form.dialog-confirm.php'?>


</body>
</html>
