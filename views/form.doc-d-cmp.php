  <div id="d_cmp.doc-form" title="Накладная">
    <form id="d_cmp.doc-d-cmp-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
              <div style="float: left">
                <label for="d_cmp.doc_num">№</label>
                <input type="text" name="d_cmp.doc_num" id="d_cmp.doc_num" style='text-align: right; width: 50px' />
                <label for="d_cmp.doc_date">от</label>
                <input type="text" name="d_cmp.doc_date" id="d_cmp.doc_date" style='text-align: center; width: 100px' />
              </div>
              <div style="float: right">
                <label for="d_cmp.doc_sum">Сумма</label>
                <input disabled type="text" name="d_cmp.doc_sum" id="d_cmp.doc_sum" style='text-align: right; width: 70px' />
                <label for="d_cmp.doc_status">Статус</label>
                <select id="d_cmp.doc_status" name="d_cmp.doc_status" style="width: 120px"></select>
                <label for="d_cmp.doc_manager">Менеджер</label>
                <select id="d_cmp.doc_manager" name="d_cmp.doc_manager" style="width: 120px"></select>
              </div>
            </td>
          </tr>
          <tr>
            <td><a href="" id="d_cmp.prod_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
              <div style="float: right">
                <label for="product_to_decomplect">Товар</label>
                <input disabled type="text" name="product_to_decomplect" id="product_to_decomplect" style='width: 550px' />
                <button id="d_cmp.pedit">..</button><label for="d_cmp.doc_firm">Фирма</label>
                <select id="d_cmp.doc_firm" name="d_cmp.doc_firm" style="width: 70px"></select>
              </div></td>
          </tr>
          <tr>
            <td>
              <table id="d_cmp.products-table" class="display compact"></table>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>
