/**
 * Объект JavaScript для работы с формой выбора товара
 */

//===================================================================================
function rboProduct(o) {
}

//===================================================================================
rboProduct.prototype.attachProductModule = function() {
  var self = this;
  //подключаем форму добавления новой позиции в документ
  $("#product-form").dialog({
    autoOpen : false,
    height : 300,
    width : 650,
    modal : true,
    resizable : true
  });

  $("#prod_search_btn").click(function(event) {
    self.productSearch();
    return false;
  });
  
  //навешиваем обработчик при выборе товара из списка найденных
  $("#prod_name").change(function(event) {
    self.setProductPrice();
    return false;
  });

  //навешиваем обработчик при изменении цены на товар 
  $("#prod_price").change(function(event) {
    self.calcSum();
    return false;
  });

  //навешиваем обработчик при изменении количества товара
  $("#prod_cnt").change(function(event) {
    self.calcSum();
    return false;
  });

}

//===================================================================================
rboProduct.prototype.showProductForm = function(o) {
  var self = this;

  $("#prodId").val(o.pData.productId);
  $("#prod_code").val(o.pData.product_code);
  $('#prod_name option').remove();
  if (!IsNull(o.pData) && IsArray(o.pData) && (o.pData.length>0)) {//if (x >= 0) {
    $('#prod_name').append('<option value="">' + o.pData.product_name + '</option>');
  }
  $("#prod_name option:first").prop("selected", "selected");
  $("#prod_price").val(o.pData.product_price);
  $("#prod_cnt").val(o.pData.product_cnt);
  $("#prod_sum").val(o.pData.product_sum);

  $("#product-form").dialog({
    title : "Позиция - " + NullTo(o.pData.product_code,""),
    buttons : {
      "Удалить" : function() {
        Ask("Удалить строку из документа?", "Удалить", "Отмена", function() {
          o.fnDelete();
          $("#product-form").dialog("close");
        }, null, "#dialog-confirm");
      },

      "Сохранить" : function() {
        var p = {};
        p.productId = $("#prodId").val();
        p.product_code = $("#prod_code").val();
        p.product_name = $('#prod_name option:selected').text();
        p.product_price = $("#prod_price").val();
        p.product_cnt = $("#prod_cnt").val();
        p.product_sum = $("#prod_sum").val();
        o.fnSave(p);
        $("#product-form").dialog("close");
      },

      "Отмена" : function() {
        $("#product-form").dialog("close");
      }
    }
  });
  $("#product-form").dialog("open");
}

//===================================================================================
rboProduct.prototype.productSearch = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#prod_search").val()
    },
    url : comPath + "ajax.php?task=product_search",
    success : function(p) {
      $('#prod_name option').remove();
      for (var i = 0; i < p.result.length; i++) {
        $('#prod_name').append(
            '<option value="' + p.result[i].productID + "|" + p.result[i].price + "|" + p.result[i].product_code + "|" + p.result[i].list_price + '">' + p.result[i].name
                + '</option>');
      }
      $("#prod_name option:first").prop("selected", "selected");
      self.setProductPrice();
      if (p.count > p.result.length) {
        $('#prod_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
      }
    }
  });
}

//===================================================================================
rboProduct.prototype.setProductPrice = function() {
  var oVal = $("#prod_name option:selected").val();
  $("#product-form").dialog("option", "title", "Позиция - " + oVal);
  var arProd = oVal.split("|");
  $("#prodId").val(arProd[0]);
  $("#prod_price").val(arProd[1]);
  $("#prod_code").val(arProd[2]);
  $("#prod_cnt").val(1);
  $("#prod_price1").html("Цена Опт.1= " + arProd[3] + "р.");
  this.calcSum();
}

//===================================================================================
rboProduct.prototype.calcSum = function() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());
}
