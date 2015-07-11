<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rbohelper.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rbocust.js"></script>
<script src="<?php echo JRBO_PATH?>/models/opers.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <div>
    <div style="float: left" id="header_doclist_adddoc">
      <a href="javascript:doc.createOper()"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
    </div>
    <div id="header_doclist_choose_list">
      <a href='zakrytye-materialy/rbo'><h2>Операции</h2></a>
    </div>
  </div>

  <div id="oper-form" title="Операция">
    <form id="oper-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="sDate">Дата</label>
                <input type="text" name="sDate" id="sDate" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="sOperType">Операция</label>
                <select id="sOperType" name="sOperType" style="width: 200px" autofocus>
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
            <td>
              <div style="float: right">
                <label for="doc_cust">Покупатель</label>
                <input disabled type="text" name="doc_cust" id="doc_cust" style='width: 450px' />
                <button id="cedit">..</button>
                <label for="doc_firm">Фирма</label>
                <select id="doc_firm" name="doc_firm" style="width: 70px">
                  <option value="ип">ИП</option>
                  <option value="ооо">ООО</option>
                </select>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <table>
                <tr>
                  <td><label for="prod_search">Поиск</label> <input type="text" name="prod_search" id="prod_search" style='width: 350px' /> <a
                    href="javascript:oper.productSearch()"> <img src="<?php echo JRBO_PATH?>/images/icon-32-search-on.png" />
                  </a></td>
                </tr>
                <tr>
                  <td><label for="prod_name">Название</label> <select name="prod_name" id="prod_name" style="width: 500px" onChange="oper.setProductPrice()">
                    </select>&nbsp;(Цена Опт.1&nbsp;<label id="prod_price1"></label>)</td>
                </tr>
                <tr>
                  <td><label for="prod_price">Цена</label> <input type="text" name="prod_price" id="prod_price" 
                    style="text-align: center; width: 50px" onChange="oper.calcSum()" /> <label for="prod_cnt">р.&nbsp;Х&nbsp;</label> <input type="text" name="prod_cnt"
                    id="prod_cnt" style="text-align: center; width: 30px" onChange="oper.calcSum()" /> <label for="sSum">ед.&nbsp;=&nbsp;</label> <input type="text"
                    name="sSum" id="sSum" style="text-align: center; width: 50px" />&nbsp;р.<input style="visibility: hidden"
                    id="prodId" /> <input style="visibility: hidden" id="prod_code" /></td>
                </tr>
                <td><label for="sRem" style="vertical-align: top;">Примечание</label>
                  <textarea name="sRem" id="sRem" rows="3" cols="70"></textarea></td>
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
