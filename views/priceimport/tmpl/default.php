<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <!--script src="<?php echo JRBO_PATH ?>/models/js/RefProducts.js"></script-->
    <script src="<?php echo JRBO_PATH ?>/models/js/RbProduct.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PriceImport.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/font-awesome-4.6.3/css/font-awesome.min.css">
    <style>
    </style>

</head>
<body>

<div style="width: 100%" id="progressbar"></div>

<div>
<!--  В кнопке можно показывать название прайса из ini-файла. При нажатии на кнопку открывается форма, содержащая редактор-->
    <!--button class="ui-button ui-widget ui-corner-all" id="import_open_settings">Настройка импорта (ini)</button-->
    <button class="ui-button ui-widget ui-corner-all" id="import_open_csv">Открыть прайс (csv)</button>
    <button class="ui-button ui-widget ui-corner-all" id="import_in_stock_csv">Импорт остатков(csv)</button>
    <!--button class="ui-button ui-widget ui-corner-all" id="import_start">Начать импорт</button-->
<!--    <button class="ui-button ui-widget ui-corner-all" id="import_save_changes">Сохранить изменения</button>-->
<!--    <button class="ui-button ui-widget ui-corner-all" id="import_cancel_changes">Отменить изменения</button>-->
</div>
<?php include RBO_PATH . '/views/header.doclist.php' ?>

<?php include RBO_PATH . '/views/form.dialog-confirm.php' ?>


<table id="TableProduct" class="display compact"></table>

<div id="priceimport.load-ini" title="Настройка импорта (ini)">
    <form id="priceimport.load-ini-form" method="post" action="">
        <fieldset>
            <input type="file" id="priceimport_ini_file" />
            <div style="width: 100%; height: 100%;">
                <textarea name="priceimport.settings" id="priceimport.settings" style="width: 100%; height: 400px;"></textarea>
            </div>
        </fieldset>
    </form>
</div>

<div id="priceimport.load-form" title="Открыть прайс (csv)">
    <form enctype="multipart/form-data" action="" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>
        <input id="priceimport_file" type="file"/>
        <span class="sr-only">Loading...</span>
    </form>
</div>

<!--?php include RBO_PATH . '/views/form.refproduct.php' ?-->

<div class="version"><?php echo RbHelper::getVersion() ?></div>
</body>
</html>
