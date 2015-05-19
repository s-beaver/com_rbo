'use strict';
var comPath = "/components/com_rbo/";
// ===================================================================================
function readInvoice(invId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_invoices" : {
        "invId" : invId
      }
    },
    url : comPath + "ajax.php?task=invoice_read",
    success : function(inv_data) {
      fillInvoicePrintForm(inv_data);
    }
  });
}

// ===================================================================================
function fillInvoicePrintForm(i) {
  $("#inv_num").html(i.inv_num);
  $("#inv_date").html(i.inv_date);
  $("#inv_based_on").html("По счету №" + i.inv_num + " от " + i.inv_date);

  $("#inv_ship_num").html(i.inv_ship_num);
  $("#inv_ship_date").html(i.inv_ship_date);

  if (!IsNull(i.inv_firm_details)) {
    var f = i.inv_firm_details;
    var arFirm = [ f.f_name, "ИНН " + f.f_inn, "КПП " + f.f_kpp, f.f_addr,
        f.f_phone, "банк "+f.f_bank, "БИК "+f.f_bik, "р/сч "+f.f_rch, "к/сч "+f.f_kch];
    $("[id^='inv_firm']").each(function(x, elem) {
      $(this).html(arFirm.join());
    });
    $("[id^='firm_okpo']").each(function(x, elem) {
      $(this).html(f.f_okpo);
    });
  }

  $("[id^='inv_cust']").each(function(x, elem) {
    $(this).html(i.inv_cust);
  });
  $("[id^='cust_okpo']").each(function(x, elem) {
    $(this).html("&nbsp;");
  });

  $("#inv_manager").html(i.inv_manager_details);

  var sPr = "";
  var iCntSum = 0;
  if (!IsNull(i.inv_products) && i.inv_products.length > 0) {
    for (var x = 0; x < i.inv_products.length; x++) {
      sPr += "<tr>";
      sPr += "<td style='text-align: center'>" + (x + 1) + "</td>";// #
      sPr += "<td style='text-align: left'>" + i.inv_products[x].product_name
          + "</td>";
      sPr += "<td style='text-align: left'>" + i.inv_products[x].product_code
          + "</td>";
      sPr += "<td style='text-align: center'>" + i.inv_products[x].product_uom
          + "</td>";
      sPr += "<td style='text-align: left'>&nbsp;</td>";// код по ОКЕИ
      sPr += "<td style='text-align: left'>-</td>";// вид упаковки
      sPr += "<td style='text-align: left'>-</td>";// к-во в одном месте
      sPr += "<td style='text-align: left'>-</td>";// к-во мест
      sPr += "<td style='text-align: left'>-</td>";// масса брутто
      sPr += "<td style='text-align: center'>" + i.inv_products[x].product_cnt
          + "</td>";
      sPr += "<td style='text-align: right'>" + i.inv_products[x].product_price
          + ",00</td>";
      sPr += "<td style='text-align: right'>" + i.inv_products[x].product_sum
          + ",00</td>";
      sPr += "<td style='text-align: left'>-</td>";// ставка НДС
      sPr += "<td style='text-align: center'>0,00</td>";// сумма НДС
      sPr += "<td style='text-align: right'>" + i.inv_products[x].product_sum
          + ",00</td>";// сумма с НДС
      sPr += "</tr>";
      iCntSum += Number(i.inv_products[x].product_cnt);
    }
  }
  $("#inv_products").html(sPr);
  $("#inv_cnt_sum").html(iCntSum);
  $("[id^='inv_sum']").each(function(x, elem) {
    $(this).html(i.inv_sum + ",00");
  });
  $("#inv_sum_words").html(number_to_string(i.inv_sum));

  $("[id^='doc_date_footer']").each(function(x, elem) {
    $(this).html(convertDate(i.inv_ship_date));
  });
  
  $("#doc_cnt_words").html(number_to_string(i.inv_products.length, true, true));
  

  var iManOffset = $("#stamp_anchor").offset();
  iManOffset.top -= 10;
  iManOffset.left += 100;

  if (!IsNull(i.inv_firm_details))
    $("#img_stamp").attr("src", "components/com_rbo/images/" + f.f_stamp);
  $("#img_stamp").offset(iManOffset);

}

// ===================================================================================
$(document).ready(function() {
  readInvoice($("#invid_transfer").html());
})