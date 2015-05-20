'use strict';
var comPath = "/components/com_rbo/";
// ===================================================================================
function readDocument(invId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : invId
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
  $("#inv_num").html(i.doc_num);
  $("#inv_date").html(i.doc_date);
  $("#inv_based_on").html("По счету №" + i.doc_num + " от " + i.doc_date);

  $("#inv_ship_num").html(i.doc_ship_num);
  $("#inv_ship_date").html(i.doc_ship_date);

  if (!IsNull(i.doc_firm_details)) {
    var f = i.doc_firm_details;
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

  $("#inv_cust").html(i.doc_cust);

  $("#inv_manager").html(i.doc_manager_details);

  var sPr = "";
  var iCntSum=0;
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++) {
      sPr += "<tr>";
      sPr += "<td style='text-align: center'>" + (x + 1) + "</td>";// #
      sPr += "<td style='text-align: left'>" + i.doc_products[x].product_name
          + "</td>";
      sPr += "<td style='text-align: center'>" + i.doc_products[x].product_uom
          + "</td>";
      sPr += "<td style='text-align: center'>" + i.doc_products[x].product_cnt
          + "</td>";
      sPr += "<td style='text-align: right'>" + i.doc_products[x].product_price
          + "</td>";
      sPr += "<td style='text-align: right'>" + i.doc_products[x].product_sum
          + "</td>";
      sPr += "</tr>";
      iCntSum += Number(i.doc_products[x].product_cnt);
    }
  }
  $("#inv_products").html(sPr);
  $("#inv_cnt_sum").html(iCntSum);
  $("#inv_sum").html(i.doc_sum);
  $("#inv_sum_words").html(number_to_string(i.doc_sum));

  var iManOffset = $("#stamp_anchor").offset();
  iManOffset.top -= 10;
  iManOffset.left += 100;

  if (!IsNull(i.doc_firm_details))
    $("#img_stamp").attr("src", "components/com_rbo/images/" + f.f_stamp);
  $("#img_stamp").offset(iManOffset);

}

// ===================================================================================
$(document).ready(function() {
  readDocument($("#docid_transfer").html());
})