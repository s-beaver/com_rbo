'use strict';
var comPath = "/components/com_rbo/";
// ===================================================================================
function readDocument(docId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type": "счет"
      }
    },
    url : comPath + "ajax.php?task=doc_read",
    success : function(doc_data) {
      fillInvoicePrintForm(doc_data);
    }
  });


}

// ===================================================================================
function fillInvoicePrintForm(i) {
  $("#doc_num").html(i.doc_num);
  $("#doc_date").html(i.doc_date);

  var f;
  if (!IsNull(i.doc_firm_details)) {
    f = i.doc_firm_details;
    var arFirm = [ f.f_fullname, "ИНН " + f.f_inn, "КПП " + f.f_kpp, f.f_addr,
        f.f_phone ];
    $("#doc_firm").html(arFirm.join());
    $("#f_bank").html(f.f_bank);
    $("#f_bik").html(f.f_bik);
    $("#f_rch").html(f.f_rch);
    $("#f_kch").html(f.f_kch);
    $("#f_name").html(f.f_fullname);
    $("#f_inn").html(f.f_inn);
    $("#f_kpp").html(f.f_kpp);
  }

  if (!IsNull(i.doc_cust)) {
    var c = i.doc_cust;
    var cd = NullTo(c.cust_data,{});
    var arCust = [ c.cust_fullname, "ИНН " + cd.cust_inn, "КПП " + cd.cust_kpp, cd.cust_addr,
        c.f_phone ];
    $("#doc_cust").html(arCust.join());
  }

  $("#doc_manager").html(i.doc_manager_details);

  var sPr = "";
  var iCntSum = 0;
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

  $("#doc_products").html(sPr);
  $("#doc_cnt_sum").html(iCntSum);
  $("#doc_sum").html(i.doc_sum);
  $("#doc_sum_words").html(number_to_string(i.doc_sum));

  var iManOffset = $("#doc_sign_block").offset();
  iManOffset.top -= 50;
  iManOffset.left = 250;

  if (!IsNull(i.doc_firm_details))
    $("#img_stamp").attr("src", "components/com_rbo/images/" + f.f_stamp);
  $("#img_stamp").offset(iManOffset);

}

// ===================================================================================
$(document).ready(function() {
  readDocument($("#docid_transfer").html());
})