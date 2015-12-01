/* Скрипт формы для работы с входящими накладными */
var b_bill;

// ===================================================================================
$(document).ready(function () {

    b_bill = new RbDoc({
        sDocType: 'B_BIL',
        docFormPrefix:"b_bill",
        sDocTypeListTitle: 'Накладные (закуп)',
        sDocTypeTitle: 'Накладная (закуп)',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    b_bill.attachPageElements();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    b_bill.readDocFromURLId();
});
