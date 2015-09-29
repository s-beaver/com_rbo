var comPath = "/components/com_rbo/";
var prd;

//===================================================================================
function RefProducts(o) {
  this.tips = o.tips;
  this.allFields = o.allFields;
}

//===================================================================================
RefProducts.prototype.attachProductModule = function() {
  var self = this;
  //подключаем форму для редакции документов
  $("#prd-form").dialog({
    autoOpen : false,
    height : 350,
    width : 800,
    modal : true,
    resizable : true
  });

  self.oTable = $('#TableProduct').dataTable({
    "bJQueryUI" : true,
    "bProcessing" : true,
    "bServerSide" : true,
    "sAjaxSource" : comPath + "ajax.php?task=get_product_list",
    "fnServerData" : function(sSource, aoData, fnCallback, oSettings) {
      oSettings.jqXHR = $.ajax({
        "dataType" : 'json',
        "type" : "POST",
        "data" : aoData,
        "url" : sSource,
        "success" : function(json) {
          fnCallback(json);
        }
      });
    },
    "aoColumns" : [ {
      "sTitle" : "Ключ",
      "sClass" : "center",
      "mData" : function(source, type, val) {
        return "<a href='javascript:prd.readProduct(" + source.productId + ")'>#" + source.productId + "</a>";
      }
    }, {
      "sTitle" : "Наименование",
      "mData" : "product_name"
    }, {
      "sTitle" : "Код",
      "mData" : "product_code"
    }, {
      "sTitle" : "Категория",
      "sClass" : "center",
      "mData" : "categoryId"
    }, {
      "sTitle" : "К-во",
      "sClass" : "center",
      "mData" : //"product_in_stock"
          function(source, type, val) {
            return source.product_type=='1'?"услуга":source.product_in_stock;
          }
    }, {
      "sTitle" : "Цена",
      "sClass" : "center",
      "mData" : "product_price"
    }, {
      "sTitle" : "Цена опт.",
      "sClass" : "center",
      "mData" : "product_price1"
    } ],
    "oLanguage" : {
      "sProcessing" : "Подождите...",
      "sLengthMenu" : "Показать _MENU_ строк",
      "sZeroRecords" : "Записи отсутствуют.",
      "sInfo" : "Записи с _START_ по _END_ (всего: _TOTAL_)",
      "sInfoEmpty" : "Записей нет",
      "sInfoFiltered" : "(отфильтровано из _MAX_ записей)",
      "sInfoPostFix" : "",
      "sSearch" : "Поиск:",
      "sUrl" : "",
      "oPaginate" : {
        "sFirst" : "В начало",
        "sPrevious" : "Предыдущие",
        "sNext" : "Следующие",
        "sLast" : "В конец"
      }
    }
  });

  $("#header_doclist_choose_list h2").html("Справочник - товары");

  //подключаем меню перехода в другой раздел
  $("#links").selectmenu({
    select: function (event, ui) {
      if (!IsEmpty($(this).val()))
        location.href = $(this).val();
    }
  });

  //обработчик нажатия кнопки добавления товара
  $("#prd_add_btn").click(function(event) {
    self.createProduct();
    return false;
  });

  //обработчик нажатия чекбокса "услуга/товар"
  $("#rbo_products\\.product_type").click(function(event) {
    self.switchInStockField();
  });

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

};

//===================================================================================
RefProducts.prototype.setRW = function(oData) {
  return false;
};

//===================================================================================
RefProducts.prototype.readProduct = function(productId) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_products" : {
        "productId" : productId
      }
    },
    url : comPath + "ajax.php?task=product_read",
    success : function(data) {
      self.showProductForm(data);
    }
  });
};

//===================================================================================
RefProducts.prototype.saveProduct = function() {
  var self = this;
  var oData = getFormData("prd-form", "rbo_products");
  var bValid = true;
  self.allFields.removeClass("ui-state-error");
  bValid = bValid && checkNotEmpty($("#rbo_products\\.product_price"), "Цена", self.tips);
  bValid = bValid && checkNotEmpty($("#rbo_products\\.product_price1"), "Цена1", self.tips);
  if (oData.rbo_products.product_type == "1")
    bValid = bValid && checkNotEmpty($("#rbo_products\\.product_in_stock"), "К-во на складе", self.tips);
  oData.rbo_products.product_in_stock = EmptyTo(oData.rbo_products.product_in_stock, 0);
  if (!bValid)
    return;

  var taskCmd = "product_create";
  if (!IsNull(oData.rbo_products.productId) && oData.rbo_products.productId > 0)
    taskCmd = "product_update";
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : oData,
    url : comPath + "ajax.php?task=" + taskCmd,
    success : function(data) {
      $("#prd-form").dialog("close");
      self.oTable.fnDraw();
    }
  });
};

//===================================================================================
RefProducts.prototype.createProduct = function() {
  var self = this;
  self.showProductForm({});
};

// ===================================================================================
RefProducts.prototype.deleteProduct = function(productId) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_products" : {
        "productId" : productId
      }
    },
    url : comPath + "ajax.php?task=product_delete",
    success : function(data) {
      self.oTable.fnDraw();
    }
  });

  $("#prd-form").dialog("close");
};

// ===================================================================================
RefProducts.prototype.showProductForm = function(i) {
  var self = this;

  setFormData("prd-form", "rbo_products", i);
  self.switchInStockField();

  var readOnly = this.setRW(i);

  var oBtns = {};
  if (!readOnly) {
    oBtns["Удалить"] = function() {
      Ask("Товар будет удален. Продолжить?", "Удалить товар", "Отмена", function() {
        self.deleteProduct(i.productId);
        $("#prd-form").dialog("close");
      }, null, "#dialog-confirm");
    }
  }

  oBtns["Сохранить"] = function() {
    self.saveProduct();
  };

  oBtns["Отмена"] = function() {
    $("#prd-form").dialog("close");
  };

  $("#prd-form").dialog({
    title : "#" + NullTo(i.productId, "новая"),
    buttons : oBtns
  });

  $("#prd-form").dialog("open");
};

//===================================================================================
RefProducts.prototype.switchInStockField = function() {
  if ($("#rbo_products\\.product_type").prop("checked")) {
    $("#label_rbo_products\\.product_in_stock").hide();
    $("#rbo_products\\.product_in_stock").hide();
  } else {
    $("#label_rbo_products\\.product_in_stock").show();
    $("#rbo_products\\.product_in_stock").show();
  }
};

// ===================================================================================
$(document).ready(function() {

  prd = new RefProducts({
    allFields : $("#rbo_products\\.product_price").add($("#rbo_products\\.product_price1")),
    tips : $(".validateTips")
  });
  prd.attachProductModule();

});
