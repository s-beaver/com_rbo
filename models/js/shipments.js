var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RboShipment({
        sDocType: 'накл',
        sDocTypeListTitle: 'Накладные',
        sDocTypeTitle: 'Накладная',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать ТОРГ-12", viewName: "printtorg12"},
            {title: "Печатать накл.", viewName: "printship"},
            {title: "Печатать тов.чек", viewName: "printtovcheck"}
        ],
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });

    doc.attachDocModule();
    doc.oProduct.attachProductModule();
    doc.oCust.attachCustomerModule();

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    doc.readDocFromURLId();
});
