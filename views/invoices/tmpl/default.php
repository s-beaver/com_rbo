<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbForm.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbDoc.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbProduct.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbCust.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/invoices.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

  <div id="inv.doc-form" title="Счета">
    <form id="inv.doc-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="inv.doc_num">№</label>
                <input type="text" name="inv.doc_num" id="inv.doc_num" style='text-align: right; width: 50px' />
                <label for="inv.doc_date">от</label>
                <input type="text" name="inv.doc_date" id="inv.doc_date" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="inv.doc_sum">Сумма</label>
                <input disabled type="text" name="inv.doc_sum" id="inv.doc_sum" style='text-align: right; width: 70px' />
                <label for="inv.doc_status">Статус</label>
                <select id="inv.doc_status" name="inv.doc_status" style="width: 120px"></select>
                <label for="inv.doc_manager">Менеджер</label>
                <select id="inv.doc_manager" name="inv.doc_manager" style="width: 120px"></select>
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="" id="inv.prod_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
              <div style="float: right">
                <label for="inv.doc_cust">Покупатель</label>
                <input disabled type="text" name="inv.doc_cust" id="inv.doc_cust" style='width: 450px' />
                <button id="inv.cedit">..</button>
                <label for="inv.doc_firm">Фирма</label>
                <select id="inv.doc_firm" name="inv.doc_firm" style="width: 70px"></select>
              </div></td>
          </tr>
          <tr>
            <td>
              <table id="inv.products-table" class="display compact"></table>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <?php include RBO_PATH . '/views/form.doc-s-bill.php' ?>

  <?php include RBO_PATH . '/views/form.doc-s-act.php' ?>

  <?php include RBO_PATH.'/views/form.product.php'?>

  <?php include RBO_PATH.'/views/form.customer.php'?>

  <?php include RBO_PATH.'/views/form.del-doc.php'?>

  <table id="TableDoc" class="display compact"></table>

  <div class="version"><?php echo RbHelper::getVersion()?></div>
</body>
</html>
