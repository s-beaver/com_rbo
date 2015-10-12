var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RbDoc({
        docFormPrefix:"s-act",
        sDocType: 'акт',
        sDocTypeListTitle: 'Акты',
        sDocTypeTitle: 'Акт',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать", viewName: "PrnAct"}
        ],
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
