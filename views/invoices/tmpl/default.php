<!doctype html>

<html>
<head>
<link rel="stylesheet"
  href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />
<link rel="stylesheet"
  href="libraries/DataTables-1.10.6/media/css/jquery.dataTables_themeroller.css" />

<script
  src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script
  src="libraries/DataTables-1.10.6/media/js/jquery.dataTables.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/models/invoices.js"></script>

<style>
#neworder-form * label input {
	display: block;
}

#neworder-form * label select {
	display: block;
}

#neworder-form * input.text {
	margin-bottom: 0px;
	padding: .2em;
}

#neworder-form * select {
	margin-bottom: 0px;
	padding: .2em;
}

#neworder-form * textarea {
	margin-bottom: 0px;
	padding: .2em;
}

.validateTips {
	border: 1px solid transparent;
	padding: 0.3em;
}
</style>

</head>
<body>
  <a href="javascript:createInvoice()"> <img
    src="components/com_rbo/images/icon-32-new-on.png" /></a>

  <div id="neworder-form" title="Новый счёт">
    <!--  p class="validateTips" style="visibility: hidden">Заполните обязательные поля</p-->
    <form id="neworder-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td>
              <div style="float: left">
                <label for="inv_num">№</label>
                <input type="text" name="inv_num" id="inv_num"
                  class="text ui-widget-content ui-corner-all"
                  style='text-align: right; width: 50px' value="" />
                <label for="inv_date">от</label>
                <input type="text" name="inv_date" id="inv_date"
                  class="text ui-widget-content ui-corner-all"
                  style='text-align: center; width: 100px' value="" />
              </div>
              <div style="float: right">
                <label for="inv_sum">Сумма</label>
                <input disabled type="text" name="inv_sum" id="inv_sum"
                  class="text ui-widget-content ui-corner-all"
                  style='text-align: right; width: 70px' value="" />
                <label for="inv_status">Статус</label>
                <select id="inv_status" name="inv_status"
                  class="text ui-widget-content ui-corner-all"
                  style="width: 120px">
                  <option value=""></option>
                  <option value="выставлен">выставлен</option>
                  <option value="оплачен">оплачен</option>
                  <option value="удален">удален</option>
                </select>
                <label for="inv_manager">Менеджер</label>
                <select id="inv_manager" name="inv_manager"
                  class="text ui-widget-content ui-corner-all"
                  style="width: 120px">
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
              <a href="javascript:showProductForm()"> <img
                src="components/com_rbo/images/icon-32-new-on.png" /></a>
              <div style="float: right">
                <label for="inv_cust">Покупатель</label>
                <input type="text" name="inv_cust" id="inv_cust"
                  class="text ui-widget-content ui-corner-all"
                  style='width: 450px' value="" />
                <label for="inv_firm">Фирма</label>
                <select id="inv_firm" name="inv_firm"
                  class="text ui-widget-content ui-corner-all"
                  style="width: 70px">
                  <option value="ип">ИП</option>
                  <option value="ооо">ООО</option>
                </select>
              </div>
            </td>
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

  <div id="newline-form" title="Позиция">
    <form id="newline-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td>
              <label for="prod_search">Поиск</label>
              <input type="text" name="prod_search" id="prod_search"
                class="text ui-widget-content ui-corner-all"
                style='width: 150px' value="" />
              <a href="javascript:productSearch()"> <img
                src="components/com_rbo/images/icon-32-search-on.png" /></a>
            </td>
          </tr>
          <tr>
            <td>
              <label for="prod_name">Название</label>
              <select name="prod_name" id="prod_name"
                class="text ui-widget-content ui-corner-all"
                style="text-align: right; width: 500px" value=""
                onChange=setProductPrice() />
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="prod_price">Цена</label>
              <input type="text" name="prod_price" id="prod_price"
                class="text ui-widget-content ui-corner-all"
                style="text-align: center; width: 50px" value=""
                onChange=calcSum() />
              <label for="prod_cnt">р.&nbsp;Х&nbsp;</label>
              <input type="text" name="prod_cnt" id="prod_cnt"
                class="text ui-widget-content ui-corner-all"
                style="text-align: center; width: 30px" value=""
                onChange=calcSum() />
              <label for="prod_sum">ед.&nbsp;=&nbsp;</label>
              <input disabled type="text" name="prod_sum" id="prod_sum"
                class="text ui-widget-content ui-corner-all"
                style="text-align: center; width: 50px" value="" />
              р.
              <input style="visibility: hidden" id="prodId" value="" />
              <input style="visibility: hidden" id="prod_code" value="" />
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <div id="dialog-confirm" title="Удалить счёт?">Счёт будет удален.
    Продолжить?</div>

  <table id="TableInv" class="display compact"></table>

  <div class="toolbar">!RbO!</div>
</body>
</html>
