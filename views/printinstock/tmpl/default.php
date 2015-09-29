<!doctype html>

<html>
<head>
    <link rel="stylesheet" href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css"/>

    <script src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
    <script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/printinstock.js"></script>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }

        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }

        #report_date {
            display: inline
        }

    </style>

</head>
<body>
<h2>Товарные остатки на дату
    <div id="report_date"></div>
</h2>
<table id="report_table">
</table>

<div id="report_totals"></div>
<div id="report_debug"></div>
</body>
</html>
