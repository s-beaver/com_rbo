/* Скрипт формы для работы с входящими актами*/
var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RbDoc({
        sDocType: 'B_ACT',
        docFormPrefix:"b-act",
        sDocTypeListTitle: 'Акты (входящие)',
        sDocTypeTitle: 'Акт (входящий)',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    doc.attachPageElements();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    doc.readDocFromURLId();
});
