<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbForm.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbDoc.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbProduct.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbCust.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbDocBAct.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

<div id="b_act.doc-form" title="Акт (входящий)">
    <form id="b_act.doc-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="b_act.doc_num">№</label>
                <input type="text" name="b_act.doc_num" id="b_act.doc_num" style='text-align: right; width: 50px' />
                <label for="b_act.doc_date">от</label>
                <input type="text" name="b_act.doc_date" id="b_act.doc_date" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="b_act.doc_sum">Сумма</label>
                <input disabled type="text" name="b_act.doc_sum" id="b_act.doc_sum" style='text-align: right; width: 70px' />
                <label for="b_act.doc_status">Статус</label>
                <select id="b_act.doc_status" name="b_act.doc_status" style="width: 120px"></select>
                <label for="b_act.doc_manager">Менеджер</label>
                <select id="b_act.doc_manager" name="b_act.doc_manager" style="width: 120px"></select>
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="" id="b_act.prod_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
              <div style="float: right">
                <label for="b_act.doc_cust">Исполнитель</label>
                <input disabled type="text" name="b_act.doc_cust" id="b_act.doc_cust" style='width: 200px' />
                <button id="b_act.cedit">..</button><label for="b_act.doc_firm">Фирма</label>
                <select id="b_act.doc_firm" name="b_act.doc_firm" style="width: 70px"></select>
              </div></td>
          </tr>
          <tr>
            <td>
              <table id="b_act.products-table" class="display compact"></table>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <?php include RBO_PATH.'/views/form.product.php'?>

  <?php include RBO_PATH.'/views/form.customer.php'?>

  <?php include RBO_PATH.'/views/form.base-doc.php'?>

  <?php include RBO_PATH.'/views/form.del-doc.php'?>

  <table id="TableDoc" class="display compact"></table>

  <div class="version"><?php echo RbHelper::getVersion()?></div>
</body>
</html>
