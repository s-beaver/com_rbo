<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
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
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=opers')?>'>Операции</a></li>
        </ul>
        <h2>Продажи</h2>
        <ul id="selectable">
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=invoices')?>'>Счета</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=shipments')?>'>Накладные</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=acts')?>'>Акты</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=docdcmp')?>'>Разбивки комплектов</a></li>
        </ul>

        <h2>Закупки</h2>
        <ul id="selectable">
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=docbbill')?>'>Накладные</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=docbact')?>'>Акты</a></li>
        </ul>

    </div>
    <div style="display: inline-block; vertical-align: top;">
        <h2>Справочники</h2>
        <ul id="selectable">
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=products')?>'>Справочник - товары</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=priceimport')?>'>Импорт прайсов</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=docbstk')?>'>Накладные инвентаризации</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=customers')?>'>Справочник - контрагенты</a></li>
        </ul>

        <h2>Отчеты</h2>
        <ul id="selectable">
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=PrnInStock&format=raw')?>'>Товарные остатки</a></li>
            <li><a href='<?php echo JRoute::_('index.php?option=com_rbo&view=PrnProdVed&format=raw')?>'>Товарная ведомость</a></li>
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
