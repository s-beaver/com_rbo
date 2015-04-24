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
    $( "#selectable" ).selectable();
  });
</script>
<body>
<ol id="selectable">
  <li class="ui-widget-content">Счета</li>
  <li class="ui-widget-content">Накладные</li>
  <li class="ui-widget-content">Операции</li>
  <li class="ui-widget-content">Отчеты</li>
</ol>
</body>
</html>
