<div id="b_bill.doc-form" title="Накладная (входящая)">
    <form id="b_bill.doc-form-form" method="post" action="">
        <fieldset style='padding: 0'>
            <table style='width: 100%'>
                <tr>
                    <td>
                        <div style="float: left">
                            <label for="b_bill.doc_num">№</label>
                            <input type="text" name="b_bill.doc_num" id="b_bill.doc_num"
                                   style='text-align: right; width: 50px'/>
                            <label for="b_bill.doc_date">от</label>
                            <input type="text" name="b_bill.doc_date" id="b_bill.doc_date"
                                   style='text-align: center; width: 100px'/>
                        </div>
                        <div style="float: right">
                            <label for="b_bill.doc_sum">Сумма</label>
                            <input disabled type="text" name="b_bill.doc_sum" id="b_bill.doc_sum"
                                   style='text-align: right; width: 70px'/>
                            <label for="b_bill.doc_status">Статус</label>
                            <select id="b_bill.doc_status" name="b_bill.doc_status" style="width: 120px"></select>
                            <label for="b_bill.doc_manager">Менеджер</label>
                            <select id="b_bill.doc_manager" name="b_bill.doc_manager" style="width: 120px"></select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><a href="" id="b_bill.prod_add_btn"> <img
                                src="<?php echo JRBO_PATH ?>/images/icon-32-new-on.png"/></a>

                        <div style="float: right">
                            <label for="b_bill.pay_date">Оплата</label>
                            <input type="text" name="b_bill.pay_date" id="b_bill.pay_date" style='text-align: center; width: 100px' />
                            <label for="b_bill.doc_cust">Поставщик</label>
                            <input disabled type="text" name="b_bill.doc_cust" id="b_bill.doc_cust"
                                   style='width: 200px'/>
                            <button id="b_bill.cedit">..</button>
                            <label for="b_bill.doc_firm">Фирма</label>
                            <select id="b_bill.doc_firm" name="b_bill.doc_firm" style="width: 70px"></select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="b_bill.products-table" class="display compact"></table>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
