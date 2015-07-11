  <div id="newline-form" title="Позиция">
    <form id="newline-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td><label for="prod_search">Поиск</label> <input type="text" name="prod_search" id="prod_search" style='width: 350px' /> <a
              href="javascript:doc.productSearch()"> <img src="<?php echo JRBO_PATH?>/images/icon-32-search-on.png" />
            </a></td>
          </tr>
          <tr>
            <td><label for="prod_name">Название</label> <select name="prod_name" id="prod_name" style="width: 500px" onChange="doc.setProductPrice()">
              </select></td>
          </tr>
          <tr>
            <td><label for="prod_price">Цена</label> <input type="text" name="prod_price" id="prod_price"
              style="text-align: center; width: 50px" onChange="doc.calcSum()" /> <label for="prod_cnt">р.&nbsp;Х&nbsp;</label> <input type="text" name="prod_cnt"
              id="prod_cnt" style="text-align: center; width: 30px" onChange="doc.calcSum()" /> <label for="prod_sum">ед.&nbsp;=&nbsp;</label> <input disabled type="text"
              name="prod_sum" id="prod_sum" style="text-align: center; width: 50px" /> р. <input style="visibility: hidden" id="prodId" /> <input style="visibility: hidden"
              id="prod_code" />
              <div id="prod_price1">Цена Опт.1</div></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>
