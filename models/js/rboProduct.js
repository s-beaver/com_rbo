/**
 * Объект JavaScript для работы с формой выбора товара
 */

//===================================================================================
function rboProduct(o) {
  this.bInputMode = 'select';
  this.arFoundProducts = new Array();
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
/* Преобразует тэг select в input для ввода там поисковой строки */
rboProduct.prototype.convertSelect2Input = function() {
  if (this.bInputMode == 'select') {
    var tag = $('#prod_name').parent().html();
    tag = tag.replace("<select", "<input type=\"text\"");
    tag = tag.replace("onChange", "onChange1");//событие првязывается через jquery, но убирать нельзя
    tag = tag.replace(">", "/>");
    tag = tag.replace("</select>", "");
    $('#prod_name').parent().html(tag);
    this.bInputMode = 'input';
  }
}

//===================================================================================
/* Преобразует тэг input в select для выбора из найденных вариантов */
rboProduct.prototype.convertInput2Select = function() {
  if (this.bInputMode == 'input') {
    var tag = $('#prod_name').parent().html();
    tag = tag.replace("<input type=\"text\"", "<select");
    tag = tag.replace("onChange1", "onChange");
    tag = tag.replace("/>", "></select>");
    $('#prod_name').parent().html(tag);
    this.bInputMode = 'select';
  }
}

//===================================================================================
rboProduct.prototype.showProductForm = function(o) {
  var self = this;
  self.convertInput2Select();

  $("#prodId").val(o.pData.productId);
  $("#prod_code").val(o.pData.product_code);
  $('#prod_name option').remove();
  if (!IsNull(o.pData) && (!IsNull(o.pData.product_name))) {//if (x >= 0) {
    $('#prod_name').append('<option value="">' + o.pData.product_name + '</option>');
  }
  $("#prod_name option:first").prop("selected", "selected");
  $("#prod_price").val(o.pData.product_price);
  $("#prod_cnt").val(o.pData.product_cnt);
  $("#prod_sum").val(o.pData.product_sum);

  $("#product-form").dialog({
    title : "Позиция - " + NullTo(o.pData.product_code, ""),
    buttons : {
      "Новый товар" : function() {
        var sStr = $('#prod_name option:selected').text();
        $('#prod_name option').remove();
        self.convertSelect2Input();
        $('#prod_name').val(sStr);
      },

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
        if (self.bInputMode == 'input') {
          p.product_name = $('#prod_name').val();
        } else {
          p.product_name = $('#prod_name option:selected').text();
        }
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
/*
 * Поиск товара по подстроке в элементе prod_search. Список найденных вариантов
 * записывается в тэг select prod_name
 */
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
      self.convertInput2Select();
      $('#prod_name option').remove();
      self.arFoundProducts = new Array();
      for (var i = 0; i < p.result.length; i++) {
        $('#prod_name').append('<option value="' + i + '">' + p.result[i].product_name + '</option>');
        self.arFoundProducts[i] = p.result[i];
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
  var self = this;
  var oVal = $("#prod_name option:selected").val();
  $("#product-form").dialog("option", "title", "Позиция - " + oVal);
  $("#prodId").val(self.arFoundProducts[oVal].productId);
  $("#prod_price").val(self.arFoundProducts[oVal].product_price);
  $("#prod_code").val(self.arFoundProducts[oVal].product_code);
  $("#prod_cnt").val(1);
  $("#prod_price1").html("Цена Опт.1= " + NullTo(self.arFoundProducts[oVal].product_price1,0) + "р. Остаток на складе=" + NullTo(self.arFoundProducts[oVal].product_in_stock, 0));
  this.calcSum();
}

//===================================================================================
rboProduct.prototype.calcSum = function() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());
}
