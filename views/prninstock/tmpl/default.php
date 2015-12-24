<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/PrnInStock.js"></script>

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
