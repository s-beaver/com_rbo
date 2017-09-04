'use strict';
var oFormDlg;
var cookieName = "prn_prod_ved";
var s_bill, b_bill, d_cmp;

// ===================================================================================
function getReportData(params) {
    $("#progressbar").show();
    $("#report_table").html("");
    $("#report_totals").html("");

    if (getURLParam("pv_prodId") != "") {
        params = NullTo(params, {});
        params.date_end = getURLParam("pv_date");
        params.prodId = getURLParam("pv_prodId");
    } else {
        params = NullTo(params, getObjCookie(cookieName));
    }

    $.ajax({
        dataType: 'json',
        type: "POST",
        data: params,
        url: comPath + "ajax.php?task=report_prod_ved",
        success: function (rep_data) {
            $("#progressbar").hide();
            if (!IsNull(params)) {
                var s = "";
                params.date_start = NullTo(params.date_start, "");
                params.date_end = NullTo(params.date_end, "");
                s += "за " + params.date_start + " - " + params.date_end;

                if (NullTo(params.prodId, 0) > 0) {
                    s += ", по товару &quot;" + params.product_name + "&quot;";
                } else {
                    if (NullTo(params.search, "") != "")
                        s += ", по товару &quot;" + params.search + "&quot;";
                }

                if (NullTo(params.custId, 0) > 0)
                    s += ", по контрагенту &quot;" + params.cust_name + "&quot;";

                if (NullTo(params.firm, "") != "")
                    s += ", по фирме &quot;" + params.firm + "&quot;";

                if (NullTo(params.manager, "") != "")
                    s += ", по менеджеру &quot;" + params.manager + "&quot;";

                $("#report_params").html(s);
            }
            if (rep_data != {})
                fillReport(rep_data);
            else
                $("#report_totals").html("По запросу ничего не найдено");
            setObjCookie(cookieName, params);
        },
        error: function (err) {
            $("#progressbar").hide();
        }
    });
}

// ===================================================================================
function readDocument() {

    getReportData();

    var oBtns = {};
    oBtns["Построить отчет"] = function () {
        var params = getFormData("pv\\.rep-form", "pv");

        params.pv.prodId = 0;
        var oVal = $("#pv\\.product_name option:selected").val();
        if (Number(oVal) > 0) params.pv.prodId = Number(oVal);

        params.pv.custId = 0;
        var oVal = $("#pv\\.cust_name option:selected").val();
        if (Number(oVal) > 0) params.pv.custId = Number(oVal);

        getReportData(params.pv);
        oFormDlg.dialog("close");
    };

    oBtns["Отмена"] = function () {
        oFormDlg.dialog("close");
    };

    oFormDlg = $("#pv\\.rep-form");
    oFormDlg.dialog({
        autoOpen: false,
        height: 400,
        width: 650,
        modal: true,
        resizable: true,
        title: "Введите параметры отчета",
        buttons: oBtns
    });

    $("#open_dlg").click(function (event) {
        OpenDlg();
        return false;
    });

    $("#pv\\.date_start").datepicker({
        showButtonPanel: true,
        showOn: "button",
        dateFormat: "dd.mm.yy"
    });

    $("#pv\\.date_end").datepicker({
        showButtonPanel: true,
        showOn: "button",
        dateFormat: "dd.mm.yy"
    });

    refillSelect("pv\\.firm", getFirmList(), true);
    refillSelect("pv\\.manager", getPeopleList(), true);

    $("#_pv\\.prod_search_btn").click(function (event) {
        productSearch();
        return false;
    });

    $("#_pv\\.cust_search_btn").click(function (event) {
        custSearch();
        return false;
    });

    $("#_pv\\.prod_search_delete_btn").click(function (event) {
        $('#pv\\.product_name option').remove();
        return false;
    });

    $("#_pv\\.cust_search_delete_btn").click(function (event) {
        $('#pv\\.cust_name option').remove();
        return false;
    });

    $("#progressbar").progressbar({
        value: false
    });
}

// ===================================================================================
function OpenDlg() {
    var params = getObjCookie(cookieName);
    if (!IsNull(params))
        setFormData("pv\\.rep-form", "pv", params);
    oFormDlg.dialog("open");
}

//===================================================================================
function productSearch() {
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "search": $("#pv\\.search").val(),
            "filter": false
        },
        url: comPath + "ajax.php?task=product_search",
        success: function (p) {
            var oProd = {};
            for (var i = 0; i < p.result.length; i++) {
                oProd[p.result[i].productId] = p.result[i].product_name;
            }
            refillSelect("pv\\.product_name", oProd);
            if (p.count > p.result.length) {
                $('#pv\\.product_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
            }

        }
    });
}

//===================================================================================
function custSearch() {
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "search": $("#pv\\.cust").val()
        },
        url: comPath + "ajax.php?task=cust_search",
        success: function (p) {
            var oCust = {};
            for (var i = 0; i < p.result.length; i++) {
                oCust[p.result[i].custId] = p.result[i].cust_name;
            }
            refillSelect("pv\\.cust_name", oCust);
            if (p.count > p.result.length) {
                $('#pv\\.cust_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
            }

        }
    });
}

// ===================================================================================
function fillReport(rep_data) {
    rep_data = NullTo(rep_data, {});
    var opers = rep_data.data;
    var report = "", totalsMinus = 0, totalsPlus = 0, totalsMinusZ = 0, totalsPlusZ = 0;
    var operId, oper_type, oper_date, custId, docId, doc_type, doc_num, doc_date, doc_link, oper_sum, oper_firm, oper_manager, oper_rem;
    var productId, productTitle, product_code, product_name;
    var product_price, product_cnt, buy_price, buy_docId, doc_buy_link, buy_sum, buy_oper_type;

    report = "<thead>";
    report += "<td>#</td>";
    report += "<td>Тип</td>";
    report += "<td>Дата</td>";
    report += "<td>Контрагент</td>";
    report += "<td>Документ</td>";
    report += "<td>Товар</td>";
    report += "<td>Цена</td>";
    report += "<td>К-во</td>";
    report += "<td>Сумма</td>";
    report += "<td>Закуп</td>";
    report += "<td>Сумма закуп</td>";
    report += "<td>Фирма</td>";
    report += "<td>Мен.</td>";
    report += "<td>Прим.</td>";
    report += "</thead>";
    for (var i = 0; i < opers.length; i++) {
        oper_type = NullTo(opers[i].oper_type, "");
        custId = NullTo(Number(opers[i].custId), 0);
        docId = NullTo(Number(opers[i].docId), 0);
        doc_type = NullTo(opers[i].doc_type, "");
        doc_num = NullTo(opers[i].doc_num, "");
        doc_date = NullTo(opers[i].doc_date, "");
        doc_link = "";
        if (docId > 0 && getPrintLinkByDoc(docId, doc_type) != "") {
            //doc_link = "<a target='blank' href='" + getPrintLinkByDoc(docId, doc_type) + "'>№" + doc_num + " / " + doc_date + "</a>";
            if (doc_type == "накл")
                doc_link = "<a href='javascript:s_bill.readDoc(" + docId + ")'>№" + doc_num + " / " + doc_date + "</a>";
        }

        productId = NullTo(Number(opers[i].productId), 0);
        //productTitle = NullTo(opers[i].product_name, "") + ((NullTo(opers[i].product_code, "")!="")?" ("+opers[i].product_code+")":"");
        productTitle = NullTo(opers[i].product_name, "");

        product_price = NullTo(Number(opers[i].product_price), 0);
        product_cnt = NullTo(Number(opers[i].product_cnt), 0);
        oper_sum = NullTo(Number(opers[i].oper_sum), 0);
        buy_oper_type = NullTo(opers[i].buyOperType, "");
        buy_price = NullTo(opers[i].buyPrice, 0);
        buy_docId = NullTo(opers[i].buyDocId, 0);
        buy_sum = buy_price * product_cnt;
        doc_buy_link = "";
        if (buy_docId > 0 && getDocNameByOperType(buy_oper_type) != "")
            doc_buy_link = "<a href='javascript:" + getDocNameByOperType(buy_oper_type) + ".readDoc(" + buy_docId + ")'>" + buy_price + "</a>";

        if (oper_type == "продажа" && buy_price > 0) {
            totalsPlus += product_cnt * product_price;
            totalsMinus += product_cnt * buy_price;
        }
        if (oper_type == "закуп" && buy_price > 0) {
            totalsPlusZ += product_cnt * product_price;
            totalsMinusZ += product_cnt * buy_price;
        }

        report += "<tr>";
        report += "<td>" + NullTo(opers[i].operId, "") + "</td>";
        report += "<td>" + oper_type + "</td>";
        report += "<td>" + NullTo(opers[i].oper_date, "") + "</td>";
        report += "<td>" + NullTo(opers[i].cust_name, "") + "</td>";
        report += "<td>" + doc_link + "</td>";
        report += "<td>" + productTitle + "</td>";
        report += "<td align='right'>" + product_price + "</td>";
        report += "<td align='center'>" + product_cnt + "</td>";
        report += "<td align='right'>" + oper_sum + "</td>";
        report += "<td align='right'>" + doc_buy_link + "</td>";
        report += "<td align='right'>" + Round(buy_sum, 2) + "</td>";
        report += "<td>" + NullTo(opers[i].oper_firm, "") + "</td>";
        report += "<td>" + NullTo(opers[i].oper_manager, "") + "</td>";
        report += "<td>" + NullTo(opers[i].oper_rem, "") + "</td>";
        report += "</tr>";
    }

    totalsMinus = Round(totalsMinus, 2);
    totalsPlus = Round(totalsPlus, 2);
    var total = Round(totalsPlus - totalsMinus, 2);
    $("#report_date").html(rep_data.date);
    $("#report_table").html(report);
    var s = "<br><b>";
    s += "Итого продаж " + totalsPlus + " руб., за вычетом закупа " + totalsMinus + " руб. = " + total + " руб.</b><br>";
    s += "Из расчета исключены строки со стоимостью закупа 0 руб<br>";
    s += "Строки закупа без продаж " + Round(totalsMinusZ, 2) + " руб.</b><br><br>";
    s += "Строк в таблице:" + opers.length + " шт.<br>";
    $("#report_totals").html(s);

    //$("#report_debug").html("");
}

// ===================================================================================
$(document).ready(function () {
    s_bill = new RbDoc({
        docFormPrefix: "s_bill",
        sDocType: 'накл',
        sDocTypeListTitle: 'Накладные',
        sDocTypeTitle: 'Накладная',
        checkFields: ["doc_num", "doc_date", "doc_manager", "doc_firm"],
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
    //s_bill.attachPageElements();

    b_bill = new RbDoc({
        sDocType: 'B_BIL',
        docFormPrefix: "b_bill",
        sDocTypeListTitle: 'Накладные (закуп)',
        sDocTypeTitle: 'Накладная (закуп)',
        checkFields: ["doc_num", "doc_date", "doc_manager", "doc_firm"],
        tips: $(".validateTips"),
        printList: [
            {title: "Печатать", viewName: "PrnBBil"}
        ],
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });

    d_cmp = new RbDocDCmp({
        sDocType: 'D_CMP',
        docFormPrefix: "d_cmp",
        sDocTypeListTitle: 'Разбивки комплектов',
        sDocTypeTitle: 'Разбивка комплектов',
        checkFields: ["doc_num", "doc_date", "doc_manager", "doc_firm"],
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    readDocument();
});