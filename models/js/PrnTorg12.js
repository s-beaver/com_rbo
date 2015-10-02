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
        "doc_type" : "накл"
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

  if ((i.doc_base > 0) && !IsNull(i.doc_base_doc)) {
    $("#doc_based_on").html("по счету №" + i.doc_base_doc.doc_num + " от " + i.doc_base_doc.doc_date);
  } else {
    $("#doc_based_on").html("");
  }

  if (!IsNull(i.doc_firm_details)) {
    var f = i.doc_firm_details;
    var arFirm = [ f.f_name, "ИНН " + f.f_inn, "КПП " + f.f_kpp, f.f_addr, f.f_phone, "банк " + f.f_bank, "БИК " + f.f_bik, "р/сч " + f.f_rch, "к/сч " + f.f_kch ];
    $("[id^='doc_firm']").each(function(x, elem) {
      $(this).html(arFirm.join());
    });
    $("[id^='firm_okpo']").each(function(x, elem) {
      $(this).html(f.f_okpo);
    });
  }

  if (!IsNull(i.doc_cust)) {
    var c = i.doc_cust;
    var cd = NullTo(c.cust_data,{});
    var arCust = [ c.cust_fullname, "ИНН " + cd.cust_inn, "КПП " + cd.cust_kpp, cd.cust_addr, c.f_phone, "банк " + cd.cust_bank, "БИК " + cd.cust_bik,
        "р/сч " + cd.cust_rch, "к/сч " + cd.cust_kch ];
    $("[id^='doc_cust']").each(function(x, elem) {
      $(this).html(arCust.join());
    });
    $("[id^='cust_okpo']").each(function(x, elem) {
      $(this).html(cd.cust_okpo);
    });
  }

  $("#doc_manager").html(i.doc_manager_details);

  var sPr = "";
  var iCntSum = 0;
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++) {
      sPr += "<tr>";
      sPr += "<td style='text-align: center'>" + (x + 1) + "</td>";// #
      sPr += "<td style='text-align: left'>" + i.doc_products[x].product_name + "</td>";
      sPr += "<td style='text-align: left'>" + i.doc_products[x].product_code + "</td>";
      sPr += "<td style='text-align: center'>" + i.doc_products[x].product_uom + "</td>";
      sPr += "<td style='text-align: left'>&nbsp;</td>";// код по ОКЕИ
      sPr += "<td style='text-align: left'>-</td>";// вид упаковки
      sPr += "<td style='text-align: left'>-</td>";// к-во в одном месте
      sPr += "<td style='text-align: left'>-</td>";// к-во мест
      sPr += "<td style='text-align: left'>-</td>";// масса брутто
      sPr += "<td style='text-align: center'>" + i.doc_products[x].product_cnt + "</td>";
      sPr += "<td style='text-align: right'>" + i.doc_products[x].product_price + ",00</td>";
      sPr += "<td style='text-align: right'>" + i.doc_products[x].product_sum + ",00</td>";
      sPr += "<td style='text-align: left'>-</td>";// ставка НДС
      sPr += "<td style='text-align: center'>0,00</td>";// сумма НДС
      sPr += "<td style='text-align: right'>" + i.doc_products[x].product_sum + ",00</td>";// сумма с НДС
      sPr += "</tr>";
      iCntSum += Number(i.doc_products[x].product_cnt);
    }
  }
  $("#doc_products").html(sPr);
  $("#doc_cnt_sum").html(iCntSum);
  $("[id^='doc_sum']").each(function(x, elem) {
    $(this).html(i.doc_sum + ",00");
  });
  $("#doc_sum_words").html(number_to_string(i.doc_sum));

  $("[id^='doc_date_footer']").each(function(x, elem) {
    $(this).html(convertDate(i.doc_date));
  });

  $("#doc_cnt_words").html(number_to_string(i.doc_products.length, true, true));

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
});