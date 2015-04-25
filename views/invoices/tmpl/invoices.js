$(function() {
  //'use strict';
  var oTable;

  //===================================================================================
  /** Ask("123","Удалить","Отмена",function(){deleteOrder(invId)},null);
   * 
   */
  function Ask(sText,okText,cancelText,fnOk,fnCancel) 
  {
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
      resizable: false,
      height:180,
      modal: true,
      buttons: bOk 
    });

    $("#dialog-confirm").dialog("open");
  }

  //===================================================================================
  function setHandlers() {
    $("a[class^=aid_]")
    .each(
        function(index, value) {
          $(this).click(
              function() { 
                var invId = $(this).attr('href');

                $.ajax({
                  dataType: 'json',
                  type: "POST",
                  data: {"invId":invId},
                  url: "/components/com_rbo/ajax.php?task=invoice_read",
                  success: function(order_data) {
                    /*$("#order_email").val(order_data.order_email);
                    $("#order_type :contains('"+order_data.phType+"')").prop("selected", "selected");
                    $("#order_cnt").val(order_data.phCount);
                    $("#order_text").val(order_data.phDescr);
                    var readOnly = setRW(order_data.phStatus)?"":"&read_only=1";*/

                    var oBtns = {};
                    if (readOnly=="") {
                      oBtns = {
                          "Сохранить": function() { checkSubmit(invId,"") },
                          //"Отмена": function() { $("#neworder-form").dialog("close") },
                          "Удалить": function() { 
                            $("#dialog-confirm").html("Счёт будет удален. Продолжить?");
                            $("#dialog-confirm").dialog({
                              title: "Удалить счёт",
                              buttons: {
                                "Удалить": function() { $("#dialog-confirm").dialog("close"); deleteOrder(invId); },
                                "Отмена": function() { $("#dialog-confirm").dialog("close"); }
                              }
                            });
                            $("#dialog-confirm").dialog("open");
                          } 
                      }
                    }

                    $("#neworder-form").dialog({
                      height: 550,
                      width: 700,
                      title:"Счёт №"+order_num,
                      buttons: oBtns                 
                    });
                    $("#tabs").tabs({ 
                      active: 0,
                      beforeActivate: function(event, ui) {
                        $("#order_cnt").val(countAttachedFiles()) 
                      }
                    });
                    $("#neworder-form").dialog("open");
                  }
                });
                return false;
              }
          );
        }
    );
  }

  $(document).ready(function() {
    oTable = $('#TableInv').dataTable( {
      "bJQueryUI": true,
      "bProcessing": true,
      "bServerSide": true,
      "aaSorting": [[ 1, "desc" ]],
      //"sAjaxSource": "/ajax.php?option=com_rbo&view=invoices&format=raw",
      "sAjaxSource": "/components/com_rbo/ajax.php?task=get_invoice_list",
      "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
        oSettings.jqXHR = $.ajax( {
          "dataType": 'json',
          "type": "POST",
          "data": aoData,
          "url": sSource,
          "success": function(json) {fnCallback(json); setHandlers(); }
        } );
      },
      "aoColumns": [
                    { "sTitle": "Номер", "sWidth":"100"},
                    { "sTitle": "Дата", "sWidth":"200" },
                    { "sTitle": "Покупатель" },
                    { "sTitle": "Сумма" },
                    { "sTitle": "Статус", "sClass": "center", "sWidth":"100"},
                    ],
                    "oLanguage": {
                      "sProcessing":   "Подождите...",
                      "sLengthMenu":   "Показать _MENU_ строк",
                      "sZeroRecords":  "Записи отсутствуют.",
                      "sInfo":         "Счета с _START_ по _END_ (всего: _TOTAL_)",
                      "sInfoEmpty":    "Счетов нет",
                      "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
                      "sInfoPostFix":  "",
                      "sSearch":       "Поиск:",
                      "sUrl":          "",
                      "oPaginate": {
                        "sFirst": "В начало",
                        "sPrevious": "Предыдущие",
                        "sNext": "Следующие",
                        "sLast": "В конец"
                      }
                    }
    } );   
  } );
} );