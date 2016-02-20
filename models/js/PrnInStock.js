'use strict';
// ===================================================================================
function readDocument() {
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {},
        url: comPath + "ajax.php?task=report_in_stock",
        success: function (rep_data) {
            fillReport(rep_data);
        }
    });
}

// ===================================================================================
function fillReport(rep_data) {
    rep_data = NullTo(rep_data, {});
    var prd = rep_data.products;
    var report = "", totals = 0, rowTotal, product_code, price_name, product_name, product_in_stock, product_price;
    report = "<thead>";
    report += "<td>Код</td>";
    report += "<td>Прайс</td>";
    report += "<td>Наименование</td>";
    report += "<td>К-во</td>";
    report += "<td>Цена</td>";
    report += "<td>Сумма</td>";
    report += "</thead>";
    for (var i = 0; i < prd.length; i++) {
        product_code = NullTo(prd[i].product_code, "");
        price_name = NullTo(prd[i].price_name, "");
        product_name = NullTo(prd[i].product_name, "");
        product_in_stock = NullTo(Number(prd[i].product_in_stock), 0);
        product_price = NullTo(Number(prd[i].product_price), 0);
        rowTotal = product_in_stock > 0 ? product_in_stock * product_price : 0;
        report += "<tr>";
        report += "<td>" + product_code + "</td>";
        report += "<td>" + price_name + "</td>";
        report += "<td>" + product_name + "</td>";
        report += "<td align='center'  "+(product_in_stock > 0 ?"":"class='alert'")+">" + product_in_stock + "</td>";
        report += "<td align='right'>" + product_price + "</td>";
        report += "<td align='right'>" + rowTotal + "</td>";
        report += "</tr>";
        totals += rowTotal;
    }
    $("#report_date").html(rep_data.date);
    $("#report_table").html(report);
    $("#report_totals").html("<br><b>Остатков товаров на сумму " + totals + " руб.</b>");
}

// ===================================================================================
$(document).ready(function () {
    readDocument();
});