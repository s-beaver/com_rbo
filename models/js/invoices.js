var doc;

// ===================================================================================
$(document).ready(function () {

    doc = new RboDoc({
        sDocType: 'счет',
        sDocTypeListTitle: 'Счета',
        sDocTypeTitle: 'Счет',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать", viewName: "printinv"}
        ],
        copyToList: [
            {title: "Создать акт", docType: "акт", viewName: "acts"},
            {title: "Создать накладную", docType: "накл", viewName: "shipments"}
        ],
        statusList: {
            "выставлен": "выставлен",
            "оплачен": "оплачен",
            "удален": "удален"
        }
    });
    doc.attachDocModule();
    doc.oProduct.attachProductModule();
    doc.oCust.attachCustomerModule();

    //подключаем диалог вопроса - подтверждения
    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    doc.readDocFromURLId();
});
