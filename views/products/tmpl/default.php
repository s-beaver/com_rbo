<!doctype html>

<html>
<head>
<?php include RBO_PATH.'/views/header.head.links.php'?>
<script src="<?php echo JRBO_PATH?>/library/lib.js"></script>
<script src="<?php echo JRBO_PATH?>/models/rboform.js"></script>
<script src="<?php echo JRBO_PATH?>/models/products.js"></script>
<link rel="stylesheet" href="<?php echo JRBO_PATH?>/css/rbo.css" />

<style>
</style>

</head>
<body>
  <div>
    <div style="float: left" id="header_doclist_adddoc">
      <a href="" id="prd_add_btn"> <img src="<?php echo JRBO_PATH?>/images/icon-32-new-on.png" /></a>
    </div>
    <div id="header_doclist_choose_list">
      <a href='zakrytye-materialy/rbo'><h2>Справочник-товары</h2></a>
    </div>
  </div>

  <div id="prd-form" title="Справочник-товары">
    <form id="prd-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table style='width: 100%'>
          <tr>
            <td>
                <label for="rbo_products.product_code">Код</label>
                <input type="text" name="rbo_products.product_code" id="rbo_products.product_code" style='text-align: center; width: 100px' />
                <label id="rbo_products.productId" style="visibility: hidden"></label>
            </td>
          </tr>
          <tr>
            <td>
                <label for="rbo_products.categoryId">Категория</label>
                <select id="rbo_products.categoryId" name="rbo_products.categoryId" style="width: 200px" autofocus></select>
            </td>
          </tr>
          <tr>
            <td>
              <label for="rbo_products.product_name">Название</label> 
              <input type="text" name="rbo_products.product_name" id="rbo_products.product_name" style='width: 700px' />
            </td>
          </tr>
          <tr>
            <td>
              <div style="float: left">
               <label for="rbo_products.product_price">Цена</label> 
               <input type="text" name="rbo_products.product_price" id="rbo_products.product_price" style="text-align: right; width: 50px" />&nbsp;
              </div>
              <div style="float: left">
               &nbsp;<label for="rbo_products.product_price1">Цена опт.</label> 
               <input type="text" name="rbo_products.product_price1" id="rbo_products.product_price1" style="text-align: right; width: 50px" /> 
              </div>
            </td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>
  
  <?php include RBO_PATH.'/views/form.del-doc.php'?>

  <table id="TableProduct" class="display compact"></table>

  <!--div class="toolbar">!RbO!</div-->
</body>
</html>