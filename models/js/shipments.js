var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RbDoc({
        docFormPrefix:"s-bill",
        sDocType: 'накл',
        sDocTypeListTitle: 'Накладные',
        sDocTypeTitle: 'Накладная',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm"],
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать ТОРГ-12", viewName: "PrnTorg12"},
            {title: "Печатать накл.", viewName: "PrnShip"},
            {title: "Печатать тов.чек", viewName: "PrnTvCheck"}
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
