<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rbohelper.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rbodocument.js"></script>
<script src="<?php echo JRBO_PATH?>/models/invoices.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

  <div id="doc-form" title="Документ">
    <!--  p class="validateTips" style="visibility: hidden">Заполните обязательные поля</p-->
    <form id="doc-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="doc_num">№</label>
                <input type="text" name="doc_num" id="doc_num" style='text-align: right; width: 50px' />
                <label for="doc_date">от</label>
                <input type="text" name="doc_date" id="doc_date" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="doc_sum">Сумма</label>
                <input disabled type="text" name="doc_sum" id="doc_sum" style='text-align: right; width: 70px' />
                <label for="doc_status">Статус</label>
                <select id="doc_status" name="doc_status" style="width: 120px">
                  <option value=""></option>
                  <option value="выставлен">выставлен</option>
                  <option value="оплачен">оплачен</option>
                  <option value="удален">удален</option>
                </select>
                <label for="doc_manager">Менеджер</label>
                <select id="doc_manager" name="doc_manager" style="width: 120px">
                  <option value=""></option>
                  <option value="Алексей">Алексей</option>
                  <option value="Аня">Аня</option>
                  <option value="Володя">Володя</option>
                  <option value="Николай">Николай</option>
                </select>
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="javascript:doc.showProductForm()"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
              <div style="float: right">
                <label for="doc_cust">Покупатель</label>
                <input disabled type="text" name="doc_cust" id="doc_cust" style='width: 450px' />
                <button id="cedit">..</button>
                <label for="doc_firm">Фирма</label>
                <select id="doc_firm" name="doc_firm" style="width: 70px">
                  <option value="ип">ИП</option>
                  <option value="ооо">ООО</option>
                </select>
              </div></td>
          </tr>
          <tr>
            <td>
              <table id="TableProducts" class="display compact"></table>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <?php include RBO_PATH.'/views/form.newline.php'?>

  <?php include RBO_PATH.'/views/form.customer.php'?>

  <?php include RBO_PATH.'/views/form.del-doc.php'?>

  <table id="TableDoc" class="display compact"></table>

  <!--div class="toolbar">!RbO!</div-->
</body>
</html>
