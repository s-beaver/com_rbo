var doc;

// ===================================================================================
$(document).ready(function() {

  doc = new RboShipment({
    sDocType : 'накл',
    sDocTypeListTitle : 'Накладные',
    sDocTypeTitle : 'Накладная',
    allFields : $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
    tips : $(".validateTips"),
    printList : [{title:"Печатать ТОРГ-12", viewname:"printtorg12"},{title:"Печатать накл.", viewname:"printship"},{title:"Печатать тов.чек", viewname:"printtovcheck"}],
    statusList : {
      "подписан" : "подписан",
      "удален" : "удален"
    }
  });

  doc.attachDocModule();
  doc.oProduct.attachProductModule();
  doc.oCust.attachCustomerModule();

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  $("#doc_date").datepicker({
    showButtonPanel : true,
    dateFormat : "dd.mm.yy"
  });

});
