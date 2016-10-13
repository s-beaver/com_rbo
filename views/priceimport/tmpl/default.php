<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PriceImport.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/font-awesome-4.6.3/css/font-awesome.min.css">
    <style>
    </style>

</head>
<body>
<div>
    <button class="ui-button ui-widget ui-corner-all" id="import_open_csv">Открыть прайс (csv)</button>
    <button class="ui-button ui-widget ui-corner-all" id="import_open_settings">Настройка импорта (json)</button>
    <button class="ui-button ui-widget ui-corner-all" id="import_start">Начать импорт</button>
    <button class="ui-button ui-widget ui-corner-all" id="import_save_changes">Сохранить изменения</button>
    <button class="ui-button ui-widget ui-corner-all" id="import_cancel_changes">Отменить изменения</button>
</div>
<?php include RBO_PATH . '/views/header.doclist.php' ?>

<?php include RBO_PATH . '/views/form.dialog-confirm.php' ?>

<table id="TableProduct" class="display compact"></table>

<div id="priceimport.load-form" title="Открыть прайс (csv)">
    <form enctype="multipart/form-data" action="" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>
        <input id="priceimport_file" type="file"/>
        <div style="text-align: center"><i id="progressbar" class="fa fa-cog fa-spin fa-2x fa-fw"></i></div>
        <span class="sr-only">Loading...</span>
    </form>
</div>

<div class="version"><?php echo RbHelper::getVersion() ?></div>
</body>
</html>
