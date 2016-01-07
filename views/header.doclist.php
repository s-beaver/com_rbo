<div>
    <div style="float: left" id="header_doclist_adddoc">
        <a href="" id="doc_add_btn"><img src="<?php echo JRBO_PATH ?>/images/icon-32-new-on.png"/></a>
    </div>

    <div style="float: right">
        <select name="files" id="links">
            <option selected value="">перейти в раздел ...</option>
            <option value="/index.php?option=com_rbo&view=opers">Операции</option>
            <optgroup label="Продажи">
                <option value="/index.php?option=com_rbo&view=invoices">Счета</option>
                <option value="/index.php?option=com_rbo&view=shipments">Накладные</option>
                <option value="/index.php?option=com_rbo&view=acts">Акты</option>
            </optgroup>
            <optgroup label="Закупки">
                <option value="/index.php?option=com_rbo&view=docbbill">Накладные (закуп)</option>
                <option value="/index.php?option=com_rbo&view=docbact">Акты (закуп)</option>
            </optgroup>
            <optgroup label="Справочники">
                <option value="/index.php?option=com_rbo&view=products">Справочник - товары</option>
                <option value="/index.php?option=com_rbo&view=customers">Справочник - контрагенты</option>
            </optgroup>
            <optgroup label="Отчеты">
                <option value="/index.php?option=com_rbo&view=PrnInStock&format=raw">Товарные остатки</option>
            </optgroup>
        </select>
    </div>

    <div id="header_doclist_choose_list">
        <h2>Заголовок для списка документов</h2>
    </div>
</div>
