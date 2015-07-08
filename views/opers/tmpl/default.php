<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rbohelper.js"></script>
<script src="<?php echo JRBO_PATH?>/models/opers.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

  <div id="oper-form" title="Документ">
    <form id="oper-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="sKey">Ключ</label>
                <input type="text" name="sKey" id="sKey" style='text-align: right; width: 50px' />
                <label for="sDate">от</label>
                <input type="text" name="sDate" id="sDate" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="sSum">Сумма</label>
                <input disabled type="text" name="sSum" id="sSum" style='text-align: right; width: 70px' />
                <label for="sOperType">Статус</label>
                <select id="sOperType" name="sOperType" style="width: 120px">
                  <option value="продажа">продажа</option>       
                  <option value="закуп">закуп</option>         
                  <option value="затраты-аренда">затраты-аренда</option>
                  <option value="затраты-коммун">затраты-коммун</option>
                  <option value="затраты-произв">затраты-произв</option>
                  <option value="затраты-банков">затраты-банков</option>
                  <option value="затраты-прочие">затраты-прочие</option>
                  <option value="затраты-связь">затраты-связь</option>
                  <option value="затраты-налоги">затраты-налоги</option>
                  <option value="затраты-бухгал">затраты-бухгал</option>
                </select>
                <label for="sOperMan">Менеджер</label>
                <select id="sOperMan" name="sOperMan" style="width: 120px">
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

  <?php include RBO_PATH.'/views/form.customer.php'?>

  <?php include RBO_PATH.'/views/form.del-doc.php'?>

  <table id="TableOper" class="display compact"></table>

  <!--div class="toolbar">!RbO!</div-->
</body>
</html>
