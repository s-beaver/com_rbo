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
$(function() {
    $( "#selectable1" ).selectable();
  });

</script>
<body>
<ol id="selectable">
  <li class="ui-widget-content1"><a href='/index.php?option=com_rbo&view=invoices'>Документы</a></li>
  <li class="ui-widget-content1">Операции</li>
  <li class="ui-widget-content1">Отчеты</li>
</ol>

</body>
</html>
