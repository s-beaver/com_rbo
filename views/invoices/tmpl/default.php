<!doctype html>

<html>
<head>
<link rel="stylesheet" href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />
<link rel="stylesheet" href="libraries/DataTables-1.10.6/media/css/jquery.dataTables_themeroller.css" />

<script src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="libraries/DataTables-1.10.6/media/js/jquery.dataTables.min.js"></script>

<script src="components/com_rbo/library/lib.js"></script>
<script src="components/com_rbo/models/rbohelper.js"></script>
<script src="components/com_rbo/models/invoices.js"></script>
<link rel="stylesheet" href="components/com_rbo/css/rbo.css" />

<style>
</style>

</head>
<body>
  <?php include 'components/com_rbo/views/header.doclist.php';?>

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
            <td><a href="javascript:showProductForm()"> <img src="components/com_rbo/images/icon-32-new-on.png" /></a>
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

  <div id="newline-form" title="Позиция">
    <form id="newline-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td><label for="prod_search">Поиск</label> <input type="text" name="prod_search" id="prod_search" style='width: 150px' /> <a href="javascript:productSearch()">
                <img src="components/com_rbo/images/icon-32-search-on.png" />
            </a></td>
          </tr>
          <tr>
            <td><label for="prod_name">Название</label> <select name="prod_name" id="prod_name" style="width: 500px" onChange="setProductPrice()">
              </select></td>
          </tr>
          <tr>
            <td><label for="prod_price">Цена</label> <input type="text" name="prod_price" id="prod_price" class="ui-widget-content ui-corner-all"
              style="text-align: center; width: 50px" onChange="calcSum()" /> <label for="prod_cnt">р.&nbsp;Х&nbsp;</label> <input type="text" name="prod_cnt" id="prod_cnt"
              style="text-align: center; width: 30px" onChange="calcSum()" /> <label for="prod_sum">ед.&nbsp;=&nbsp;</label> <input disabled type="text" name="prod_sum"
              id="prod_sum" style="text-align: center; width: 50px" /> р. <input style="visibility: hidden" id="prodId" /> <input style="visibility: hidden" id="prod_code" /></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <div id="cust-form" title="Позиция">
    <form id="cust-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td><label for="cust_search">Поиск</label></td>
            <td><input type="text" name="cust_search" id="cust_search" style='width: 150px' /> <a href="javascript:custSearch()"> <img
                src="components/com_rbo/images/icon-32-search-on.png" /></a><input style="visibility: hidden" id="custId" /></td>
          </tr>
          <tr>
            <td><label for="cust_name">Название</label></td>
            <td><select name="cust_name" id="cust_name" style="width: 500px" onChange="setCustFlds('selected')"></select></td>
          </tr>
        </table>
        <table>
          <tr>
            <td><label for="cust_fullname">
                Название<br>полностью
              </label></td>
            <td><input type="text" name="cust_fullname" id="cust_fullname" style='width: 500px' /></td>
          </tr>
          <tr>
            <td><label for="cust_addr">Адрес</label></td>
            <td><input type="text" name="cust_addr" id="cust_addr" style='width: 500px' /></td>
          </tr>
          <tr>
            <td><label for="cust_nonds">Без НДС</label></td>
            <td><input type="checkbox" name="cust_nonds" id="cust_nonds"></td>
          </tr>
        </table>
        <table>
          <tr>
            <td colspan="2"><label for="cust_inn">&nbsp;ИНН</label><input type="text" name="cust_inn" id="cust_inn" style='width: 150px' /> <label for="cust_kpp">&nbsp;КПП</label><input
              type="text" name="cust_kpp" id="cust_kpp" style='width: 150px' /></td>
          </tr>
          <tr>
            <td colspan="2"><label for="cust_okpo">ОКПО</label><input type="text" name="cust_okpo" id="cust_okpo" style='width: 150px' /> <label for="cust_ogrn">&nbsp;ОГРН</label>
              <input type="text" name="cust_ogrn" id="cust_ogrn" style='width: 150px' /></td>
          </tr>
          <tr>
            <td><label for="cust_bank">Банк</label></td>
            <td><input type="text" name="cust_bank" id="cust_bank" style='width: 500px' /></td>
          </tr>
          <tr>
            <td><label for="cust_bik">БИК</label></td>
            <td><input type="text" name="cust_bik" id="cust_bik" style='width: 150px' /></td>
          </tr>
          <tr>
            <td colspan="2"><label for="cust_rch">р/сч</label><input type="text" name="cust_rch" id="cust_rch" style='width: 250px' /> <label for="cust_kch">к/сч</label><input
              type="text" name="cust_kch" id="cust_kch" style='width: 250px' /></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>

  <div id="dialog-confirm" title="Удалить счёт?">Счёт будет удален. Продолжить?</div>

  <table id="TableDoc" class="display compact"></table>

  <!--div class="toolbar">!RbO!</div-->
</body>
</html>
