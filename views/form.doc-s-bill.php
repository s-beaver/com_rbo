<div id="s_bill.doc-form" title="Накладная">
    <form id="doc-s-bill-form-form" method="post" action="">
        <fieldset style='padding: 0'>
            <table style='width: 100%'>
                <tr>
                    <td>
                        <div style="float: left">
                            <label for="s_bill.doc_num">№</label>
                            <input type="text" name="s_bill.doc_num" id="s_bill.doc_num"
                                   style='text-align: right; width: 50px'/>
                            <label for="s_bill.doc_date">от</label>
                            <input type="text" name="s_bill.doc_date" id="s_bill.doc_date"
                                   style='text-align: center; width: 100px'/>
                        </div>
                        <div style="float: right">
                            <label for="s_bill.doc_sum">Сумма</label>
                            <input disabled type="text" name="s_bill.doc_sum" id="s_bill.doc_sum"
                                   style='text-align: right; width: 70px'/>
                            <label for="s_bill.doc_status">Статус</label>
                            <select id="s_bill.doc_status" name="s_bill.doc_status" style="width: 120px"></select>
                            <label for="s_bill.doc_manager">Менеджер</label>
                            <select id="s_bill.doc_manager" name="s_bill.doc_manager" style="width: 120px"></select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><a href="" id="s_bill.prod_add_btn"> <img
                                    src="<?php echo JRBO_PATH ?>/images/icon-32-new-on.png"/></a>
                        <div style="float: right">
                            <label for="s_bill.pay_date">Оплата</label>
                            <input type="text" name="s_bill.pay_date" id="s_bill.pay_date"
                                   style='text-align: center; width: 100px'/>
                            <label for="s_bill.doc_base">Осн.</label>
                            <input disabled type="text" name="s_bill.doc_base" id="s_bill.doc_base"
                                   style='width: 150px'/>
                            <button id="s_bill.baseedit">..</button>
                            <label for="s_bill.doc_cust">Пок-ль</label>
                            <input disabled type="text" name="s_bill.doc_cust" id="s_bill.doc_cust"
                                   style='width: 200px'/>
                            <button id="s_bill.cedit">..</button>
                            <label for="s_bill.doc_firm">Фирма</label>
                            <select id="s_bill.doc_firm" name="s_bill.doc_firm" style="width: 70px"></select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="float: right">
                            <label for="s_bill.doc_rem" style="vertical-align: top;">Прим.</label>
                            <textarea name="s_bill.doc_rem" id="s_bill.doc_rem"
                                      style="width: 700px; height: 50px;"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="s_bill.products-table" class="display compact"></table>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
