<!doctype html>

<html>
<head>
    <link rel="stylesheet" href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css"/>
    <!--id="theme"-->
    <link rel="stylesheet" href="libraries/DataTables-1.10.9/media/css/jquery.dataTables_themeroller.css"/>

    <script src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
    <script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="libraries/DataTables-1.10.9/media/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>
    <style>
        h2 {
            text-align: left
        }
    </style>

</head>
<body>
<div style="width: 100%">
    <div style="display: inline-block; width: 30%; vertical-align: top;">
        <h2>Операции</h2>
        <ul id="selectable">
            <li><a href='/index.php?option=com_rbo&view=opers'>Операции</a></li>
        </ul>
        <h2>Продажи</h2>
        <ul id="selectable">
            <li><a href='/index.php?option=com_rbo&view=invoices'>Счета</a></li>
            <li><a href='/index.php?option=com_rbo&view=shipments'>Накладные</a></li>
            <li><a href='/index.php?option=com_rbo&view=acts'>Акты</a></li>
        </ul>

        <h2>Закупки</h2>
        <ul id="selectable">
            <li><a href='/index.php?option=com_rbo&view=docbbill'>Накладные</a></li>
            <li><a href='/index.php?option=com_rbo&view=docbact'>Акты</a></li>
        </ul>

    </div>
    <div style="display: inline-block; vertical-align: top;">
        <h2>Справочники</h2>
        <ul id="selectable">
            <li><a href='/index.php?option=com_rbo&view=products'>Справочник - товары</a></li>
            <li><a href='/index.php?option=com_rbo&view=customers'>Справочник - контрагенты</a></li>
        </ul>

        <h2>Отчеты</h2>
        <ul id="selectable">
            <li><a href='/index.php?option=com_rbo&view=PrnInStock&format=raw'>Товарные остатки</a></li>
        </ul>

        <h2>Справка</h2>
        <ul id="selectable">
            <li><a href='zakrytye-materialy/39-rabota-v-uchetnoj-sisteme-robika'>Описание
                    работы в учетной системе</a></li>
            <li><a href='zakrytye-materialy/40-tipovye-operatsii'>Описание типовых
                    операций</a></li>
        </ul>
    </div>
</div>
<div class="version"><?php echo RbHelper::getVersion() ?></div>
</body>
</html>
