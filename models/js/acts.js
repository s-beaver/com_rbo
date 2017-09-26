var s_act;

// ===================================================================================
$(document).ready(function () {

    s_act = new RbDoc({
        docFormPrefix:"s_act",
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
    s_act.attachPageElements();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#s_act\\.doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    s_act.readDocFromURLId();
});
