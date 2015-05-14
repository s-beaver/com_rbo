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

  if (!IsNull(i.inv_firm_details)) {
    var f = i.inv_firm_details;
    var arFirm = [ f.f_name, "ИНН " + f.f_inn, "КПП " + f.f_kpp, f.f_addr,
        f.f_phone ];
    $("#inv_firm").html(arFirm.join());
    $("#f_bank").html(f.f_bank);
    $("#f_bik").html(f.f_bik);
    $("#f_rch").html(f.f_rch);
    $("#f_kch").html(f.f_kch);
    $("#f_name").html(f.f_fullname);
    $("#f_inn").html(f.f_inn);
    $("#f_kpp").html(f.f_kpp);
  }

  $("#inv_cust").html(i.inv_cust);

  $("#inv_manager").html(i.inv_manager_details);

  var sPr = "";
  if (!IsNull(i.inv_products) && i.inv_products.length > 0) {
    for (var x = 0; x < i.inv_products.length; x++) {
      sPr += "<tr>";
      sPr += "<td style='text-align: center'>" + (x + 1) + "</td>";// #
      sPr += "<td style='text-align: left'>" + i.inv_products[x].product_name
          + "</td>";
      sPr += "<td style='text-align: center'>" + i.inv_products[x].product_cnt
          + "</td>";
      sPr += "<td style='text-align: right'>" + i.inv_products[x].product_price
          + "</td>";
      sPr += "<td style='text-align: right'>" + i.inv_products[x].product_sum
          + "</td>";
      sPr += "</tr>";
    }
  }
  $("#inv_products").html(sPr);
  $("#inv_sum").html(i.inv_sum);
  $("#inv_sum_words").html(number_to_string(i.inv_sum));

  var iManOffset = $("#inv_manager").offset();
  iManOffset.top -= 50;
  iManOffset.left -= 50;

  if (!IsNull(i.inv_firm_details))
    $("#img_stamp").attr("src", "components/com_rbo/images/" + f.f_stamp);
  $("#img_stamp").offset(iManOffset);

}

// ===================================================================================
$(document).ready(function() {
  readInvoice($("#invid_transfer").html());
})