/* Скрипт формы для работы с входящими накладными */
var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RbDoc({
        sDocType: 'B_BIL',
        sDocTypeListTitle: 'Накладные (закуп)',
        sDocTypeTitle: 'Накладная (закуп)',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    doc.readDocFromURLId();
});
