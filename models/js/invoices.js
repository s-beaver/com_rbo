var doc, docSAct, docSBill;

// ===================================================================================
$(document).ready(function () {

    doc = new RbDoc({
        docFormPrefix:"inv",
        sDocType: 'счет',
        sDocTypeListTitle: 'Счета',
        sDocTypeTitle: 'Счет',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать", viewName: "PrnInv"}
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
    doc.attachPageElements();

    docSBill = new RbDoc({
        docFormPrefix:"s-bill",
        oTable:doc.oTable,
        sDocType: 'накл',
        sDocTypeListTitle: 'Накладные',
        sDocTypeTitle: 'Накладная',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
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

    docSAct = new RbDoc({
        docFormPrefix:"s-act",
        oTable:doc.oTable,
        sDocType: 'акт',
        sDocTypeListTitle: 'Акты',
        sDocTypeTitle: 'Акт',
        allFields: $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать", viewName: "PrnAct"}
        ],
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });


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
