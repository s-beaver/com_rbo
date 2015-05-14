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

  var f = i.inv_firm_details;
  $("#inv_firm").html(
      implode(", ", f.f_name, "ИНН " + f.f_inn, "КПП " + f.f_kpp, f.f_addr,
          f.f_phone));
  $("#f_bank").html(f.f_bank);
  $("#f_bik").html(f.f_bik);
  $("#f_rch").html(f.f_rch);
  $("#f_kch").html(f.f_kch);
  $("#f_name").html(f.f_fullname);
  $("#f_inn").html(f.f_inn);
  $("#f_kpp").html(f.f_kpp);

  $("#inv_cust").html(i.inv_cust);

  $("#inv_manager").html(i.inv_manager_details);

  var sPr="";
  if (!IsNull(i.inv_products) && i.inv_products.length > 0) {
    for (var x = 0; x < i.inv_products.length; x++) {
      sPr += "<tr>";
      sPr += "<td>"+(x+1)+"</td>";//#
      sPr += "<td>"+i.inv_products[x].product_name+"</td>";
      sPr += "<td>"+i.inv_products[x].product_cnt+"</td>";
      sPr += "<td>"+i.inv_products[x].product_price+"</td>";
      sPr += "<td>"+i.inv_products[x].product_sum+"</td>";
      sPr += "</tr>";
    }
  }
  $("#inv_products").html(sPr);

  /*
   * $("#inv_sum").val(i.inv_sum); $("#inv_manager :contains('" + i.inv_manager +
   * "')").prop("selected", "selected"); $("#inv_cust").val(i.inv_cust); var
   * 
   */

}

$(document).ready(function() {
  readInvoice(1625);
})