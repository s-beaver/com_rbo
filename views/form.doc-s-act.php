<div id="s-act.doc-form" title="Акт">
  <form id="s-act.doc-form-form" method="post" action="">
    <fieldset style='padding: 0'>
      <table style='width: 100%'>
        <tr>
          <td>
            <div style="float: left">
              <label for="s-act.doc_num">№</label>
              <input type="text" name="s-act.doc_num" id="s-act.doc_num" style='text-align: right; width: 50px' />
              <label for="s-act.doc_date">от</label>
              <input type="text" name="s-act.doc_date" id="s-act.doc_date" style='text-align: center; width: 100px' />
            </div>
            <div style="float: right">
              <label for="s-act.doc_sum">Сумма</label>
              <input disabled type="text" name="s-act.doc_sum" id="s-act.doc_sum" style='text-align: right; width: 70px' />
              <label for="s-act.doc_status">Статус</label>
              <select id="s-act.doc_status" name="s-act.doc_status" style="width: 120px"></select>
              <label for="s-act.doc_manager">Менеджер</label>
              <select id="s-act.doc_manager" name="s-act.doc_manager" style="width: 120px"></select>
            </div>
          </td>
        </tr>
        <tr>
          <td><a href="" id="s-act.prod_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
            <div style="float: right">
              <label for="s-act.doc_base">Основание</label>
              <input disabled type="text" name="s-act.doc_base" id="s-act.doc_base" style='width: 150px' />
              <button id="s-act.baseedit">..</button>
              <label for="s-act.doc_cust">Покупатель</label>
              <input disabled type="text" name="s-act.doc_cust" id="s-act.doc_cust" style='width: 200px' />
              <button id="s-act.cedit">..</button><label for="s-act.doc_firm">Фирма</label>
              <select id="s-act.doc_firm" name="s-act.doc_firm" style="width: 70px"></select>
            </div></td>
        </tr>
        <tr>
          <td>
            <table id="s-act.products-table" class="display compact"></table>
          </td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>
