<div id="prd-form" title="Справочник-товары">
  <form id="prd-form-form" method="post" action="">
    <fieldset style='padding: 0'>
      <table style='width: 100%'>
        <tr>
          <td>
            <label for="rbo_products.product_code">Код</label>
            <input type="text" name="rbo_products.product_code" id="rbo_products.product_code"
                   style='text-align: center; width: 100px'/>
            <label id="rbo_products.productId" style="visibility: hidden"></label>
          </td>
        </tr>
        <tr>
          <td>
            <label for="rbo_products.categoryId">Категория</label>
            <select id="rbo_products.categoryId" name="rbo_products.categoryId" style="width: 200px"
                    autofocus></select>
          </td>
        </tr>
        <tr>
          <td>
            <label for="rbo_products.product_name">Название</label>
            <input type="text" name="rbo_products.product_name" id="rbo_products.product_name"
                   style='width: 700px'/>
          </td>
        </tr>
        <tr>
          <td>
            <div style="float: left">
              <label for="rbo_products.product_price">Цена</label>
              <input type="text" name="rbo_products.product_price" id="rbo_products.product_price"
                     style="text-align: right; width: 50px"/>&nbsp;
            </div>
            <div style="float: left">
              &nbsp;<label for="rbo_products.product_price1">Цена опт.</label>
              <input type="text" name="rbo_products.product_price1" id="rbo_products.product_price1"
                     style="text-align: right; width: 50px"/>
            </div>
            <div style="float: left">
              &nbsp;<label for="rbo_products.product_in_stock" id="label_rbo_products.product_in_stock">Количество
                на складе</label>
              <input type="text" name="rbo_products.product_in_stock" id="rbo_products.product_in_stock"
                     style="text-align: right; width: 50px"/>&nbsp;
            </div>
        <tr>
          <td>
            <label for="rbo_products.product_type">Товар (не услуга)</label>
            <input type="checkbox" name="rbo_products.product_type" id="rbo_products.product_type">
          </td>
        </tr>
        </td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>
