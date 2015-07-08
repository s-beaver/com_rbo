var comPath = "/components/com_rbo/";
var doc;

// ===================================================================================
$(document).ready(function() {

  var oTable = $('#TableOper').dataTable({
    "bJQueryUI" : true,
    "bProcessing" : true,
    "bServerSide" : true,
    "sAjaxSource" : comPath + "ajax.php?task=get_oper_list",
    "fnServerData" : function(sSource, aoData, fnCallback, oSettings) {
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
    "aoColumns" : [ {
      "sTitle" : "Ключ",
      "sClass" : "center",
      "mData" : "sKey"
    }, {
      "sTitle" : "Дата",
      "mData" : "sDate"
    }, {
      "sTitle" : "Операция",
      "mData" : "sOperType"
    }, {
      "sTitle" : "Покупатель",
      "mData" : "sContragent"
    }, {
      "sTitle" : "Сумма",
      "sClass" : "center",
      "mData" : "sSum"
    }, {
      "sTitle" : "Фирма",
      "sClass" : "center",
      "mData" : "sCashPlace1"
    }, {
      "sTitle" : "Менеджер",
      "mData" : "sOperMan"
    } ],
    "oLanguage" : {
      "sProcessing" : "Подождите...",
      "sLengthMenu" : "Показать _MENU_ строк",
      "sZeroRecords" : "Записи отсутствуют.",
      "sInfo" : "Операции с _START_ по _END_ (всего: _TOTAL_)",
      "sInfoEmpty" : "Операций нет",
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

  $("#cedit").click(function(event) {
    doc.showCustForm();
    return false;
  });

  $("#oper-form").dialog({
    autoOpen : false,
    height : 550,
    width : 900,
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

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  $("#doc_date").datepicker({
    showButtonPanel : true,
    dateFormat : "dd.mm.yy"
  });

});
