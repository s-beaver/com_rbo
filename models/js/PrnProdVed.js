'use strict';
var oFormDlg;
var cookieName = "prn_prod_ved";
// ===================================================================================
function getReportData(params) {
    if (IsNull(params)) params = getObjCookie(cookieName);
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: params,
        url: comPath + "ajax.php?task=report_prod_ved",
        success: function (rep_data) {
            if (!IsNull(params)) {
                $("#report_params").html("за " + params.date_start + " - " + params.date_end);
            }
            fillReport(rep_data);
            setObjCookie(cookieName, params);
        }
    });
}

// ===================================================================================
function readDocument() {

    getReportData();

    var oBtns = {};
    oBtns["Построить отчет"] = function () {
        var params = getFormData("pv\\.rep-form", "pv");
        getReportData(params.pv);
        oFormDlg.dialog("close");
    };

    oBtns["Отмена"] = function () {
        oFormDlg.dialog("close");
    };

    oFormDlg = $("#pv\\.rep-form");
    oFormDlg.dialog({
        autoOpen: false,
        height: 300,
        width: 600,
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
}

// ===================================================================================
function OpenDlg() {
    var params = getObjCookie(cookieName);
    if (!IsNull(params))
        setFormData("pv\\.rep-form", "pv", params);
    oFormDlg.dialog("open");
}

// ===================================================================================
function fillReport(rep_data) {
    rep_data = NullTo(rep_data, {});
    var opers = rep_data.data;
    var report = "", totalsMinus = 0, totalsPlus = 0;
    var operId, oper_type, oper_date, custId, docId, doc_type, doc_num, doc_link, oper_sum, oper_firm, oper_rem;
    var productId, product_code, product_name, product_price, product_cnt;

    report = "<thead>";
    report += "<td>#</td>";
    report += "<td>Тип</td>";
    report += "<td>Дата</td>";
    report += "<td>Контрагент</td>";
    report += "<td>Документ</td>";
    report += "<td>Код товара</td>";
    report += "<td>Товар</td>";
    report += "<td>Цена</td>";
    report += "<td>К-во</td>";
    report += "<td>Сумма</td>";
    report += "<td>Фирма</td>";
    report += "<td>Прим.</td>";
    report += "</thead>";
    for (var i = 0; i < opers.length; i++) {
        oper_type = NullTo(opers[i].oper_type, "");
        custId = NullTo(Number(opers[i].custId), 0);
        docId = NullTo(Number(opers[i].docId), 0);
        doc_type = NullTo(opers[i].doc_type,"");
        doc_num = NullTo(opers[i].doc_num,"");
        doc_link = docId==0?"":"<a href='"+getPrintLinkByDoc(docId,doc_type)+"'>№"+doc_num+" ("+doc_type+")</a>";

        productId = NullTo(Number(opers[i].productId), 0);

        product_price = NullTo(Number(opers[i].product_price), 0);
        product_cnt = NullTo(Number(opers[i].product_cnt), 0);
        oper_sum = NullTo(Number(opers[i].oper_sum), 0);

        switch (oper_type) {
            case 'закуп':
            {
                totalsMinus += product_cnt * product_price;
                break;
            }
            case 'продажа':
            {
                totalsPlus += product_cnt * product_price;
                break;
            }
            case 'списание':
            {
                totalsMinus += product_cnt * product_price;
                break;
            }
        }

        report += "<tr>";
        report += "<td>" + NullTo(opers[i].operId, "") + "</td>";
        report += "<td>" + oper_type + "</td>";
        report += "<td>" + NullTo(opers[i].oper_date, "") + "</td>";
        report += "<td>" + NullTo(opers[i].cust_name, "") + "</td>";
        report += "<td>" + doc_link + "</td>";
        report += "<td>" + NullTo(opers[i].product_code, "") + "</td>";
        report += "<td>" + NullTo(opers[i].product_name, "") + "</td>";
        report += "<td align='right'>" + product_price + "</td>";
        report += "<td align='center'>" + product_cnt + "</td>";
        report += "<td align='right'>" + oper_sum + "</td>";
        report += "<td>" + NullTo(opers[i].oper_firm, "") + "</td>";
        report += "<td>" + NullTo(opers[i].oper_rem, "") + "</td>";
        report += "</tr>";
    }

    totalsMinus = Math.round(totalsMinus,1);
    totalsPlus = Math.round(totalsPlus,1);
    var total = Math.round(totalsPlus-totalsMinus,1);
    $("#report_date").html(rep_data.date);
    $("#report_table").html(report);
    $("#report_totals").html("<br><b>Итого затрат "+totalsMinus+" руб.<br>Итого поступлений "+totalsPlus+" руб.<br>Итого " + total + " руб.</b>");

    //$("#report_debug").html("");
}

// ===================================================================================
$(document).ready(function () {
    readDocument();
});