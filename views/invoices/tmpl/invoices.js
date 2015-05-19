'use strict';
var oTable;
var oTableProducts;
var apiTableProducts;
var oTableTools;
var comPath = "/components/com_rbo/";
var allFields;
var tips;
var editing_lineNo;
var lines_before_update;

// ===================================================================================
function setRW(sStatus) {

  if (sStatus == "выставлен" || sStatus == "оплачен" || sStatus == "удален") {
    allFields.attr("disabled", "disabled");
    return true;
  } else {
    allFields.removeAttr("disabled");
    return false;
  }

}

// ===================================================================================
function readInvoice(invId) {
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
      showInvoiceForm(inv_data);
    }
  });
}

// ===================================================================================
function checkSaveInvoice(invId, inv_status) {
  var bValid = true;
  allFields.removeClass("ui-state-error");
  bValid = bValid && checkNotEmpty($("#inv_num"), "Номер", tips);
  bValid = bValid && checkNotEmpty($("#inv_date"), "Дата", tips);
  bValid = bValid && checkNotEmpty($("#inv_manager"), "Менеджер", tips);
  var p = apiTableProducts.rows().data();
  var pAr = new Array();
  for (var i = 0; i < p.length; i++)
    pAr[i] = p[i];

  if (!bValid)
    return;

  var oData = {
    "rbo_docs" : {
      "docId" : invId,
      "doc_num" : $("#inv_num").val(),
      "doc_date" : $("#inv_date").val(),
      "doc_sum" : $("#inv_sum").val(),
      "doc_status" : $("#inv_status").val(),
      "doc_manager" : $("#inv_manager").val(),
      "doc_cust" : $("#inv_cust").val(),
      "doc_firm" : $("#inv_firm").val(),
      "doc_products" : pAr
    }
  };

  var taskCmd = "invoice_create";
  if (!IsNull(invId) && invId > 0)
    taskCmd = "invoice_update";
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : oData,
    url : comPath + "ajax.php?task=" + taskCmd,
    success : function(inv_data) {
      $("#neworder-form").dialog("close");
      oTable.fnDraw();
    }
  });

}

// ===================================================================================
function createInvoice() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    // data : {},
    url : comPath + "ajax.php?task=get_inv_num",
    success : function(p) {
      $('#inv_num').val(p.new_num);
      $('#inv_date').val(p.new_date);
    }
  });

  showInvoiceForm({});

}

// ===================================================================================
function deleteInvoice(invId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : invId
      }
    },
    url : comPath + "ajax.php?task=invoice_delete",
    success : function(inv_data) {
      oTable.fnDraw();
    }
  });

  $("#neworder-form").dialog("close");
}

// ===================================================================================
function showInvoiceForm(i) {
  $("#inv_num").val(i.doc_num);
  $("#inv_date").val(i.doc_date);
  $("#inv_sum").val(i.doc_sum);
  $("#inv_status").val(i.doc_status);
  $("#inv_manager :contains('" + i.doc_manager + "')").prop("selected",
      "selected");
  $("#inv_cust").val(i.doc_cust);
  $("#inv_firm :contains('" + i.doc_firm + "')").prop("selected", "selected");
  $("#inv_rem").val(i.doc_rem);
  var readOnly = setRW(i.doc_status);

  oTableProducts.fnClearTable();
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++)
      i.doc_products[x].lineNo = x;
    oTableProducts.fnAddData(i.doc_products);
  }

  var oBtns = {};
  if (!readOnly) {
    oBtns["Удалить"] = function() {

      Ask("Счёт будет удален. Продолжить?", "Удалить счёт", "Отмена",
          function() {
            deleteInvoice(i.invId);
          }, null, "#dialog-confirm");
    }

  }

  oBtns["Печатать"] = function() {
    window.open('index.php?option=com_rbo&view=invprint&format=raw&invid='+i.invId, '_blank');
  };

  oBtns["Сохранить"] = function() {
    checkSaveInvoice(i.invId, "");
  };

  oBtns["Отмена"] = function() {
    $("#neworder-form").dialog("close");
  };

  $("#neworder-form").dialog({
    height : 550,
    width : 900,
    title : "Счёт №" + i.doc_num,
    buttons : oBtns,
    resizable : true
  });

  $("#neworder-form").dialog("open");

}

// ===================================================================================
function showProductForm(x) {// x-номер редактируемой строки, x=null-добавляем
  editing_lineNo = x;

  var p = oTableProducts.fnGetData(x);
  if (IsNull(p))
    lines_before_update = 0;
  else
    lines_before_update = p.length;

  if (x >= 0) {
    $("#prodId").val(p.productId);
    $("#prod_code").val(p.product_code);
    $('#prod_name option').remove();
    $('#prod_name').append('<option value="">' + p.product_name + '</option>');
    $("#prod_name :contains('" + p.product_name + "')").prop("selected",
        "selected");
    $("#prod_price").val(p.product_price);
    $("#prod_cnt").val(p.product_cnt);
    $("#prod_sum").val(p.product_sum);
  }

  $("#newline-form").dialog({
    title : "Позиция - " + p.product_code,
    buttons : {
      "Удалить" : function() {
        Ask("Удалить строку из счета?", "Удалить", "Отмена", function() {
          if (editing_lineNo >= 0) {
            oTableProducts.fnDeleteRow(editing_lineNo);
          }
          $("#newline-form").dialog("close");
        }, null, "#dialog-confirm");
      },

      "Сохранить" : function() {
        var p = {};
        p.productId = $("#prodId").val();
        p.product_code = $("#prod_code").val();
        p.product_name = $('#prod_name option:selected').text();
        p.product_price = $("#prod_price").val();
        p.product_cnt = $("#prod_cnt").val();
        p.product_sum = $("#prod_sum").val();
        if (editing_lineNo >= 0) {
          p.lineNo = editing_lineNo;
          oTableProducts.fnUpdate(p, x);
        } else {
          p.lineNo = lines_before_update;
          oTableProducts.fnAddData(p);
        }

        var pAll = oTableProducts.fnGetData();
        var iSum = 0;
        for (var x = 0; x < pAll.length; x++) {
          iSum += Number(pAll[x].product_sum);
        }
        $('#inv_sum').val(iSum);

        $("#newline-form").dialog("close");
      },

      "Отмена" : function() {
        $("#newline-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#newline-form").dialog("open");

}

// ===================================================================================
function productSearch() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#prod_search").val()
    },
    url : comPath + "ajax.php?task=product_search",
    success : function(p) {
      $('#prod_name option').remove();
      for (var i = 0; i < p.result.length; i++) {
        $('#prod_name').append(
            '<option value="' + p.result[i].productID + "|" + p.result[i].price
                + "|" + p.result[i].product_code + '">' + p.result[i].name
                + '</option>');
        if (i == 0) {
          $("#prod_name :contains('" + p.result[i].name + "')").prop(
              "selected", "selected");
        }
      }
      setProductPrice();
      if (p.count > p.result.length) {
        $('#prod_name').append(
            '<option value="-1">=== Найдено позиций:' + p.count
                + ' (уточните поиск)</option>');
      }

    }
  });
}

// ===================================================================================
function setProductPrice() {
  var oVal = $("#prod_name :selected").val();
  $("#newline-form").dialog("option", "title", "Позиция - " + oVal);
  var arProd = oVal.split("|");
  $("#prodId").val(arProd[0]);
  $("#prod_price").val(arProd[1]);
  $("#prod_code").val(arProd[2]);
  $("#prod_cnt").val(1);
  calcSum();

}

// ===================================================================================
function calcSum() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());
}

// ===================================================================================
$(document)
    .ready(
        function() {
          allFields = $("#inv_num").add($("#inv_date")).add($("#inv_manager"))
              .add($("#inv_cust")).add($("#inv_firm"));
          tips = $(".validateTips");

          oTable = $('#TableInv').dataTable(
              {
                "bJQueryUI" : true,
                "bProcessing" : true,
                "bServerSide" : true,
                // "dom": 'HFT<"toolbar"><t>lfrtip',
                "tableTools" : {
                  "sSwfPath" : "/swf/copy_csv_xls_pdf.swf"
                },
                "aaSorting" : [ [ 1, "desc" ] ],
                "sAjaxSource" : comPath + "ajax.php?task=get_invoice_list",
                "fnServerData" : function(sSource, aoData, fnCallback,
                    oSettings) {
                  oSettings.jqXHR = $.ajax({
                    "dataType" : 'json',
                    "type" : "POST",
                    "data" : aoData,
                    "url" : sSource,
                    "success" : function(json) {
                      fnCallback(json);
                    }
                  });
                },
                "aoColumns" : [
                    {
                      "sTitle" : "Номер",
                      "sClass" : "center",
                      "mData" : function(source, type, val) {
                        return "<a href='javascript:readInvoice("
                            + source.docID + ")'>" + source.doc_num + "</a>";
                      }
                    }, {
                      "sTitle" : "Дата",
                      "mData" : "doc_date"
                    }, {
                      "sTitle" : "Покупатель",
                      "mData" : "doc_cust"
                    }, {
                      "sTitle" : "Сумма",
                      "sClass" : "center",
                      "mData" : "doc_sum"
                    }, {
                      "sTitle" : "Статус",
                      "sClass" : "center",
                      "mData" : "doc_status"
                    }, {
                      "sTitle" : "Менеджер",
                      "mData" : "doc_manager"
                    } ],
                "oLanguage" : {
                  "sProcessing" : "Подождите...",
                  "sLengthMenu" : "Показать _MENU_ строк",
                  "sZeroRecords" : "Записи отсутствуют.",
                  "sInfo" : "Счета с _START_ по _END_ (всего: _TOTAL_)",
                  "sInfoEmpty" : "Счетов нет",
                  "sInfoFiltered" : "(отфильтровано из _MAX_ записей)",
                  "sInfoPostFix" : "",
                  "sSearch" : "Поиск:",
                  "sUrl" : "",
                  "oPaginate" : {
                    "sFirst" : "В начало",
                    "sPrevious" : "Предыдущие",
                    "sNext" : "Следующие",
                    "sLast" : "В конец"
                  }
                }
              });

          /*
           * oTableTools = new $.fn.dataTable.TableTools( oTable, { "buttons": [
           * "copy", "csv", "xls", "pdf", { "type": "print", "buttonText":
           * "Print me!" } ] } ); $( oTableTools.fnContainer()
           * ).insertAfter('div.info');
           */

          $("#neworder-form").dialog({
            autoOpen : false,
            height : 550,
            width : 900,
            modal : true
          });

          $("#newline-form").dialog({
            autoOpen : false,
            height : 300,
            width : 650,
            modal : true
          });

          $("#dialog-confirm").dialog({
            autoOpen : false
          });

          oTableProducts = $('#TableProducts')
              .dataTable(
                  {
                    "bPaginate" : false,
                    "searching" : false,
                    "ordering" : false,
                    "aoColumns" : [
                        {
                          "sTitle" : "Код",
                          "sWidth" : "100",
                          "mData" : "product_code"
                        },
                        {
                          "sTitle" : "Наименование",
                          "sWidth" : "450",
                          "mData" : "product_name"
                        },
                        {
                          "sTitle" : "Цена",
                          "sClass" : "center",
                          "sWidth" : "100",
                          "mData" : "product_price"
                        },
                        {
                          "sTitle" : "К-во",
                          "sClass" : "center",
                          "sWidth" : "100",
                          "mData" : "product_cnt"
                        },
                        {
                          "sTitle" : "Сумма",
                          "sClass" : "center",
                          "sWidth" : "100",
                          "mData" : "product_sum"
                        },
                        {
                          "sTitle" : "Ред.",
                          "sClass" : "center",
                          "sWidth" : "50",
                          "mData" : function(source, type, val) {
                            return "<a id='edit_product' href='javascript:showProductForm("
                                + source.lineNo
                                + ")'>"
                                + "<img src='"
                                + comPath + "images/icon-32-edit-on.png'/></a>";
                          }
                        } ],
                    "oLanguage" : {
                      "sProcessing" : "Подождите...",
                      "sLengthMenu" : "Показать _MENU_ строк",
                      "sZeroRecords" : "Записи отсутствуют.",
                      "sInfo" : "Строки с _START_ по _END_ (всего: _TOTAL_)",
                      "sInfoEmpty" : "Строк нет",
                      "sInfoFiltered" : "(отфильтровано из _MAX_ записей)",
                      "sInfoPostFix" : "",
                      "sSearch" : "Поиск:",
                      "sUrl" : "",
                      "oPaginate" : {
                        "sFirst" : "В начало",
                        "sPrevious" : "Предыдущие",
                        "sNext" : "Следующие",
                        "sLast" : "В конец"
                      }
                    }

                  });

          apiTableProducts = oTableProducts.api();
          $('#TableProducts tbody').on('dblclick', 'tr', function() {
            $(this).toggleClass('selected');
          });

          $("#inv_date").datepicker({
            showButtonPanel : true,
            dateFormat : "dd.mm.yy"
          });

        });
