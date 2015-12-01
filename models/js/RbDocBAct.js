/* Скрипт формы для работы с входящими актами*/
var b_act;

// ===================================================================================
$(document).ready(function () {

    b_act = new RbDoc({
        sDocType: 'B_ACT',
        docFormPrefix:"b_act",
        sDocTypeListTitle: 'Акты (входящие)',
        sDocTypeTitle: 'Акт (входящий)',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    b_act.attachPageElements();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    b_act.readDocFromURLId();
});
