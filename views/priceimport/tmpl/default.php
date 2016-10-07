<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PriceImport.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>

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

<div class="version"><?php echo RbHelper::getVersion()?></div>
</body>
</html>
