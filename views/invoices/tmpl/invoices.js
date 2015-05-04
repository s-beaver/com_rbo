'use strict';
var oTable;
var oTableProducts;
var apiTableProducts;
var ajaxPath = "/components/com_rbo/";
var inv_num = $("#inv_num");
var inv_date = $("#inv_date");
var inv_manager = $("#inv_manager");
var inv_cust = $("#inv_cust");
var inv_firm = $("#inv_firm");
var allFields = $([]).add(inv_num).add(inv_date).add(inv_manager).add(inv_cust)
    .add(inv_firm);
var tips = $(".validateTips");

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
/**
 * Ask("123","Удалить","Отмена",function(){deleteOrder(invId)},null);
 * 
 */
function Ask(sText, okText, cancelText, fnOk, fnCancel) {
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
  /*
   * if (sStatus=="заказан") { $("#order_email").attr("disabled","disabled");
   * $("#order_type").attr("disabled","disabled");
   * $("#order_text").attr("disabled","disabled"); return false; } else {
   * $("#order_email").removeAttr("disabled");
   * $("#order_type").removeAttr("disabled");
   * $("#order_text").removeAttr("disabled"); return true; }
   */
}

// ===================================================================================
function readInvoice(invId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_invoices":"{invId:'"+invId+"'}"
    },
    url : ajaxPath + "ajax.php?task=invoice_read",
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

  if (!bValid)
    return;

  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "invId" : invId,
      "inv_num" : $("#inv_num").val(),
      "inv_date" : $("#inv_date").val(),
      "inv_manager" : $("#inv_manager").val(),
      "inv_cust" : $("#inv_cust").val(),
      "inv_firm" : $("#inv_firm").val()
    },
    url : ajaxPath + "ajax.php?task=invoice_submit",
    success : function(inv_data) {
      alert(inv_data);
      /*
       * $("#neworder-form").dialog("close"); oTable.fnDraw();
       */
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
    url : ajaxPath + "ajax.php?task=invoice_delete",
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
  // $("#inv_cust") - array
  $("#inv_sum").val(i.inv_sum);
  $("#inv_status").val(i.inv_status);
  $("#inv_rem").val(i.inv_rem);
  $("#inv_firm :contains('" + i.inv_firm + "')").prop("selected", "selected");
  $("#inv_manager :contains('" + i.inv_m + "')").prop("selected", "selected");
  var readOnly = setRW(i.inv_status) ? "" : "&read_only=1";

  oTableProducts.fnClearTable();
  oTableProducts.fnAddData(i.inv_products);

  var oBtns = {};
  if (readOnly == "" || true) {
    oBtns = {
      "Сохранить" : function() {
        checkSaveInvoice(i.invId, "")
      },

      "Отмена" : function() {
        $("#neworder-form").dialog("close")
      },

      "Удалить" : function() {
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
function setHandlers() {
  $("a[class^=aid_]").each(function(index, value) {
    $(this).click(function() {
      var invId = $(this).attr('href');
      readInvoice(invId);
      return false;
    });
  });
}

// ===================================================================================
$(document).ready(function() {

  oTable = $('#TableInv').dataTable({
    "bJQueryUI" : true,
    "bProcessing" : true,
    "bServerSide" : true,
    "aaSorting" : [ [ 1, "desc" ] ],
    "sAjaxSource" : ajaxPath + "ajax.php?task=get_invoice_list",
    "fnServerData" : function(sSource, aoData, fnCallback, oSettings) {
      oSettings.jqXHR = $.ajax({
        "dataType" : 'json',
        "type" : "POST",
        "data" : aoData,
        "url" : sSource,
        "success" : function(json) {
          fnCallback(json);
          setHandlers();
        }
      });
    },
    "aoColumns" : [ {
      "sTitle" : "Номер",
      "sWidth" : "100"
    }, {
      "sTitle" : "Дата",
      "sWidth" : "200"
    }, {
      "sTitle" : "Покупатель"
    }, {
      "sTitle" : "Сумма"
    }, {
      "sTitle" : "Статус",
      "sClass" : "center",
      "sWidth" : "100"
    }, ],
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
    modal : true,
    close : function() {
      // allFields.val( "" ).removeClass( "ui-state-error" );
    }
  });

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  oTableProducts = $('#TableProducts').dataTable({
    "bPaginate" : false,
    "searching" : false,
    "ordering" : false,
    "aoColumns" : [ {
      "sTitle" : "Код",
      "sWidth" : "100"
    }, {
      "sTitle" : "Наименование",
      "sWidth" : "450"
    }, {
      "sTitle" : "Цена",
      "sClass" : "center",
      "sWidth" : "100"
    }, {
      "sTitle" : "К-во",
      "sClass" : "center",
      "sWidth" : "100"
    }, {
      "sTitle" : "Сумма",
      "sClass" : "center",
      "sWidth" : "100"
    }, ]
  });

  apiTableProducts = oTableProducts.api();
  $('#TableProducts tbody').on('click', 'tr', function() {
    $(this).toggleClass('selected');
  });

  $("#inv_date").datepicker({
    showButtonPanel : true,
    dateFormat: "dd.mm.yy"
  });


});
