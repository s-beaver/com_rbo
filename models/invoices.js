var doc;

// ===================================================================================
$(document).ready(function() {

  doc = new rboDoc({
    sDocType : 'счет',
    sDocTypeListTitle : 'Счета',
    sDocTypeTitle : 'Счет',
    allFields : $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
    tips : $(".validateTips"),
    printList : [{title:"Печатать", viewname:"printinv"}]
  });

  $("#cust_name").click(function(event) {
    doc.oCust.setCustFlds('selected');
    return false;
  });
  
  $("#cedit").click(function(event) {
    doc.oCust.showCustForm();
    return false;
  });

  $("#doc-form").dialog({
    autoOpen : false,
    height : 550,
    width : 900,
    modal : true,
    resizable : true
  });

  $("#newline-form").dialog({
    autoOpen : false,
    height : 300,
    width : 650,
    modal : true,
    resizable : true
  });

  $("#cust-form").dialog({
    autoOpen : false,
    height : 550,
    width : 750,
    modal : true,
    resizable : true
  });

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  $("#doc_date").datepicker({
    showButtonPanel : true,
    dateFormat : "dd.mm.yy"
  });

});
