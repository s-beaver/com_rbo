'use strict';
var oTable;
var oTableProducts;
var apiTableProducts;
var comPath = "/components/com_rbo/";
var allFields;
var tips;
var editing_lineNo;
var lines_before_update;

// ===================================================================================
function IsNull(vVal) {
  var und = void null;
  switch (vVal) {
  case und:
  case null:
  case undefined:
  case NaN: {
    return true;
  }
  default: {
    return false;
  }
  }
}

// ===================================================================================
function NullTo(oTest, sVoz) {
  if (IsNull(oTest))
    return sVoz;
  else
    return oTest;
}

// ===================================================================================
function updateTips(t) {
  tips.text(t).addClass("ui-state-highlight");
  setTimeout(function() {
    tips.removeClass("ui-state-highlight", 1500);
  }, 500);
}

// ===================================================================================
function checkLength(o, n, min, max) {
  if (o.val().length > max || o.val().length < min) {
    o.addClass("ui-state-error");
    updateTips("Длина поля " + n + "  должна быть не менее " + min
        + "  и не более " + max + " символов");
    return false;
  } else {
    return true;
  }
}

// ===================================================================================
function checkRegexp(o, regexp, n) {
  if (!(regexp.test(o.val()))) {
    o.addClass("ui-state-error");
    updateTips(n);
    return false;
  } else {
    return true;
  }
}

// ===================================================================================
function Ask(sText, okText, cancelText, fnOk, fnCancel) {// Ask("123","Удалить","Отмена",function(){deleteOrder(invId)},null);
  $("#dialog-confirm").html(sText);
  var bOk = {};
  bOk[okText] = function(arg) {
    $("#dialog-confirm").dialog("close");
    fnOk(arg);
  };
  bOk[cancelText] = function() {
    $("#dialog-confirm").dialog("close");
  }

  $("#dialog-confirm").dialog({
    resizable : false,
    height : 180,
    modal : true,
    buttons : bOk
  });

  $("#dialog-confirm").dialog("open");
}

// ===================================================================================
function setRW(sStatus) {

  if (sStatus == "выставлен" || sStatus == "оплачен") {
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
      "rbo_invoices" : {
        "invId" : invId
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
  // bValid = bValid && checkLength(inv_num, "Номер", 1, 3);
  var p = apiTableProducts.rows().data();
  var pAr = new Array();
  for (var i = 0; i < p.length; i++)
    pAr[i] = p[i];

  if (!bValid)
    return;

  var oData = {
    "rbo_invoices" : {
      "invId" : invId,
      "inv_num" : $("#inv_num").val(),
      "inv_date" : $("#inv_date").val(),
      "inv_sum" : $("#inv_sum").val(),
      "inv_status" : $("#inv_status").val(),
      "inv_manager" : $("#inv_manager").val(),
      "inv_cust" : $("#inv_cust").val(),
      "inv_firm" : $("#inv_firm").val(),
      "inv_products" : pAr
    }
  };

  var taskCmd = "invoice_create";
  if (invId != 0)
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
function deleteInvoice(invId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "invId" : invId
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
  $("#inv_num").val(i.inv_num);
  $("#inv_date").val(i.inv_date);
  $("#inv_sum").val(i.inv_sum);
  $("#inv_status").val(i.inv_status);
  $("#inv_manager :contains('" + i.inv_manager + "')").prop("selected",
      "selected");
  $("#inv_cust").val(i.inv_cust);
  $("#inv_firm :contains('" + i.inv_firm + "')").prop("selected", "selected");
  $("#inv_rem").val(i.inv_rem);
  var readOnly = setRW(i.inv_status);

  for (var x = 0; x < i.inv_products.length; x++)
    i.inv_products[x].lineNo = x;
  oTableProducts.fnClearTable();
  oTableProducts.fnAddData(i.inv_products);

  var oBtns = {
    "Печатать" : function() {
    },

    "Сохранить" : function() {
      checkSaveInvoice(i.invId, "")
    },

    "Отмена" : function() {
      $("#neworder-form").dialog("close");
    }
  }

  if (!readOnly) {
    oBtns["Удалить"] = function() {
      $("#dialog-confirm").html("Счёт будет удален. Продолжить?");
      $("#dialog-confirm").dialog({
        title : "Удалить счёт",
        buttons : {
          "Удалить" : function() {
            $("#dialog-confirm").dialog("close");
            deleteInvoice(i.invId);
          },
          "Отмена" : function() {
            $("#dialog-confirm").dialog("close");
          }
        }
      });
      $("#dialog-confirm").dialog("open");
    }
  }

  $("#neworder-form").dialog({
    height : 550,
    width : 900,
    title : "Счёт №" + i.inv_num,
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
        }
        else {
          p.lineNo = lines_before_update;
          oTableProducts.fnAddData(p);
        }

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
function calcSum() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());

}

// ===================================================================================
$(document)
    .ready(
        function() {
          allFields = $("#inv_num").add($("#inv_date")).add($("#inv_sum")).add(
              $("#inv_manager")).add($("#inv_cust")).add($("#inv_firm"));
          tips = $(".validateTips");

          oTable = $('#TableInv').dataTable(
              {
                "bJQueryUI" : true,
                "bProcessing" : true,
                "bServerSide" : true,
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
                            + source.invID + ")'>" + source.inv_num + "</a>";
                      }
                    }, {
                      "sTitle" : "Дата",
                      "mData" : "inv_date"
                    }, {
                      "sTitle" : "Покупатель",
                      "mData" : "inv_cust"
                    }, {
                      "sTitle" : "Сумма",
                      "sClass" : "center",
                      "mData" : "inv_sum"
                    }, {
                      "sTitle" : "Статус",
                      "sClass" : "center",
                      "mData" : "inv_status"
                    }, {
                      "sTitle" : "Менеджер",
                      "mData" : "inv_manager"
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
                        } ]
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
