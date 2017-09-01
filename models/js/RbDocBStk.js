/* Скрипт формы для работы с инвентаризационными накладными */
var b_stk;

// ===================================================================================
$(document).ready(function () {

    b_stk = new RbDoc({
        sDocType: 'B_STK',
        docFormPrefix:"b_stk",
        sDocTypeListTitle: 'Накладные (инвентаризация)',
        sDocTypeTitle: 'Накладная (инвентаризация)',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        printList: [],
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    b_stk.attachPageElements();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#b_stk\\.doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    $("#b_stk\\.pay_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    b_stk.readDocFromURLId();
});
