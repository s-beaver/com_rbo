var doc;

// ===================================================================================
$(document).ready(function() {

  doc = new rboShipment({
    sDocType : 'накл',
    sDocTypeListTitle : 'Накладные',
    sDocTypeTitle : 'Накладная',
    allFields : $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit")),
    tips : $(".validateTips"),
    printList : [{title:"Печатать ТОРГ-12", viewname:"printtorg12"},{title:"Печатать накл.", viewname:"printship"},{title:"Печатать тов.чек", viewname:"printtovcheck"}]
  });

  $("#baseedit").click(function(event) {
    doc.oCust.chooseBaseDoc();
    return false;
  });
  
  $("#base-doc-cust-name").click(function(event) {
    doc.oCust.setBaseDocList();
    return false;
  });
  
  $("#base-doc-search-btn").attr("href","javascript:doc.oCust.baseDocSearch()");

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
    width : 700,
    modal : true,
    resizable : true
  });

  $("#base-doc-form").dialog({
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
