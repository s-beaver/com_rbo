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
