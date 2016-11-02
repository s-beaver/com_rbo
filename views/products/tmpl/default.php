<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RefProducts.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>

    <style>
    </style>

</head>
<body>
<?php include RBO_PATH . '/views/header.doclist.php' ?>

<?php include RBO_PATH . '/views/form.refproduct.php' ?>

<?php include RBO_PATH . '/views/form.dialog-confirm.php' ?>

<table id="TableProduct" class="display compact"></table>

<!--div class="toolbar">!RbO!</div-->
</body>
</html>
