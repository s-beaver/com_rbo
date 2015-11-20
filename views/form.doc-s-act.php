<div id="s_act.doc-form" title="Акт">
  <form id="s_act.doc-form-form" method="post" action="">
    <fieldset style='padding: 0'>
      <table style='width: 100%'>
        <tr>
          <td>
            <div style="float: left">
              <label for="s_act.doc_num">№</label>
              <input type="text" name="s_act.doc_num" id="s_act.doc_num" style='text-align: right; width: 50px' />
              <label for="s_act.doc_date">от</label>
              <input type="text" name="s_act.doc_date" id="s_act.doc_date" style='text-align: center; width: 100px' />
            </div>
            <div style="float: right">
              <label for="s_act.doc_sum">Сумма</label>
              <input disabled type="text" name="s_act.doc_sum" id="s_act.doc_sum" style='text-align: right; width: 70px' />
              <label for="s_act.doc_status">Статус</label>
              <select id="s_act.doc_status" name="s_act.doc_status" style="width: 120px"></select>
              <label for="s_act.doc_manager">Менеджер</label>
              <select id="s_act.doc_manager" name="s_act.doc_manager" style="width: 120px"></select>
            </div>
          </td>
        </tr>
        <tr>
          <td><a href="" id="s_act.prod_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
            <div style="float: right">
              <label for="s_act.doc_base">Основание</label>
              <input disabled type="text" name="s_act.doc_base" id="s_act.doc_base" style='width: 150px' />
              <button id="s_act.baseedit">..</button>
              <label for="s_act.doc_cust">Покупатель</label>
              <input disabled type="text" name="s_act.doc_cust" id="s_act.doc_cust" style='width: 200px' />
              <button id="s_act.cedit">..</button><label for="s_act.doc_firm">Фирма</label>
              <select id="s_act.doc_firm" name="s_act.doc_firm" style="width: 70px"></select>
            </div></td>
        </tr>
        <tr>
          <td>
            <table id="s_act.products-table" class="display compact"></table>
          </td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>
