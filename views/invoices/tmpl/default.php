<!doctype html>
 
<html>
<head>
  <link rel="stylesheet" href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" /> <!--id="theme"-->
  <link rel="stylesheet" href="libraries/DataTables-1.10.6/media/css/jquery.dataTables_themeroller.css" /> 

  <script src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
  <script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
  <script src="libraries/DataTables-1.10.6/media/js/jquery.dataTables.min.js"></script>
</head>
<script>
$(document).ready(function() {

	oTable = $('#TableInv').dataTable( {
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "aaSorting": [[ 1, "desc" ]],
        //"sAjaxSource": "/ajax.php?option=com_rbo&view=invoices&format=raw",
        "sAjaxSource": "/components/com_rbo/ajax.php?command=get_invoice_list",
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
</script>
<body>
<table id="TableInv" class="display" cellspacing="0" width="100%">
</table>
</body>
</html>