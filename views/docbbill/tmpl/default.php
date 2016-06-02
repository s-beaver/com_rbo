<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbForm.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbDoc.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbProduct.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbCust.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbDocBBill.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

  <?php include RBO_PATH.'/views/form.doc-b-bill.php' ?>

  <?php include RBO_PATH.'/views/form.product.php'?>

  <?php include RBO_PATH.'/views/form.customer.php'?>

  <?php include RBO_PATH.'/views/form.base-doc.php'?>

  <?php include RBO_PATH . '/views/form.dialog-confirm.php' ?>

  <table id="TableDoc" class="display compact"></table>

  <div class="version"><?php echo RbHelper::getVersion()?></div>
</body>
</html>
