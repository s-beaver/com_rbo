var inv, s_act, s_bill;

// ===================================================================================
$(document).ready(function () {

    inv = new RbDoc({
        docFormPrefix:"inv",
        sDocType: 'счет',
        sDocTypeListTitle: 'Счета',
        sDocTypeTitle: 'Счет',
        checkFields: ["doc_num","doc_date","doc_manager","doc_firm","doc_rem"],
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
    inv.attachPageElements();

    s_bill = new RbDoc({
        docFormPrefix:"s_bill",
        oTable:inv.oTable,
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

    s_act = new RbDoc({
        docFormPrefix:"s_act",
        oTable:inv.oTable,
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


    //подключаем диалог вопроса - подтверждения
    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    inv.readDocFromURLId();
});
