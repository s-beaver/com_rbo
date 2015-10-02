<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbForm.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbProduct.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/RbCust.js"></script>
<script src="<?php echo JRBO_PATH?>/models/js/opers.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include RBO_PATH.'/views/header.doclist.php'?>

  <div id="oper-form" title="Операция">
    <form id="oper-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="rbo_opers.oper_date">Дата</label>
                <input type="text" name="rbo_opers.oper_date" id="rbo_opers.oper_date" style='text-align: center; width: 100px' />
                <label id="rbo_opers.operId" style="visibility: hidden"></label>
              </div>
              <div style="float: right">
                <label for="rbo_opers.oper_type">Операция</label>
                <select id="rbo_opers.oper_type" name="rbo_opers.oper_type" style="width: 200px" autofocus>
                </select>
                <label for="rbo_opers.oper_manager">Менеджер</label>
                <select id="rbo_opers.oper_manager" name="rbo_opers.oper_manager" style="width: 120px">
                </select>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div style="float: right">
                <label for="doc_cust">Покупатель</label>
                <input disabled type="text" name="doc_cust" id="doc_cust" style='width: 450px' />
                <button id="cedit">..</button>
                <label for="rbo_opers.oper_firm">Фирма</label>
                <select id="rbo_opers.oper_firm" name="rbo_opers.oper_firm" style="width: 120px">
                </select>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <table>
                <tr>
                  <td><label for="prod_search">Поиск</label> <input type="text" name="prod_search" id="prod_search" style='width: 350px' /> 
                    <a href="" id="prod_search_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-search-on.png" /></a>
                    <a href="" id="prod_search_off_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-delete-on.png" /></a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label for="rbo_opers.product_name">Название</label>
                    <select name="rbo_opers.product_name" id="rbo_opers.product_name" style="width: 500px"></select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label for="rbo_opers.product_price">Цена</label>
                    <input type="text" name="rbo_opers.product_price" id="rbo_opers.product_price" style="text-align: center; width: 50px" /> 
                    <label for="rbo_opers.product_cnt">р.&nbsp;Х&nbsp;</label>
                    <input type="text" name="rbo_opers.product_cnt" id="rbo_opers.product_cnt" style="text-align: center; width: 30px" /> 
                    <label for="rbo_opers.oper_sum">ед.&nbsp;=&nbsp;</label>
                    <input type="text" name="rbo_opers.oper_sum" id="rbo_opers.oper_sum" style="text-align: center; width: 50px" />&nbsp;р.
                    <label id="prod_price1">Цена Опт.1</label>
                    <input style="visibility: hidden; width: 5px" id="rbo_opers.productId" /> 
                    <input style="visibility: hidden; width: 5px" id="rbo_opers.product_code" />
                  </td>
                </tr>
                <td><label for="rbo_opers.oper_rem" style="vertical-align: top;">Примечание</label>
                  <textarea name="rbo_opers.oper_rem" id="rbo_opers.oper_rem" rows="3" cols="70"></textarea></td>
                <tr>
                </tr>
              </table>
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
