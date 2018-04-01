<div id="product-form" title="Позиция">
    <form id="product-form-form" method="post" action="">
        <fieldset style='padding: 0'>
            <table>
                <tr>
                    <td><label for="prod_search">Поиск</label> <input type="text" name="prod_search" id="prod_search"
                                                                      style='width: 350px'/> <a
                                href="" id="prod_search_btn"> <img
                                    src="<?php echo JRBO_PATH ?>/images/icon-32-search-on.png"/>
                        </a></td>
                </tr>
                <tr>
                    <td>
                        <label for="prod_name">Название</label>
                        <select name="prod_name" id="prod_name"
                                style="width: 500px">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="prod_price">Цена</label>
                        <input type="text" name="prod_price" id="prod_price"
                               style="text-align: center; width: 50px"/>
                        <label for="prod_cnt">р.&nbsp;Х&nbsp;</label>
                        <input type="text" name="prod_cnt" id="prod_cnt" style="text-align: center; width: 30px"/>
                        <label for="prod_sum">ед.&nbsp;=&nbsp;</label>
                        <input disabled type="text" name="prod_sum" id="prod_sum"
                               style="text-align: center; width: 50px"/> р.
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="prod_price1">Цена опт.</label>
                        <input disabled type="text" name="prod_price1" id="prod_price1"
                               style="text-align: right; width: 50px"/> р.
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="product_in_stock">Остаток</label>
                        <input disabled type="text" name="product_in_stock" id="product_in_stock"
                               style="text-align: right; width: 80px"/>
                        <input style="visibility: hidden" id="prodId"/>
                        <input style="visibility: hidden" id="prod_code"/>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
