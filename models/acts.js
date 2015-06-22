var doc;

// ===================================================================================
$(document).ready(function() {

  doc = new rboShipment({
    sDocType : 'акт',
    sDocTypeListTitle : 'Акты',
    sDocTypeTitle : 'Акт',
    allFields : $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
    tips : $(".validateTips"),
    printList : [{title:"Печатать счет", viewname:"printact"}]
  });

  $("#baseedit").click(function(event) {
    doc.chooseBaseDoc();
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
    height : 300,
    width : 700,
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
