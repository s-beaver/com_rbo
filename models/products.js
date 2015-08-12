var comPath = "/components/com_rbo/";
var prd;

//===================================================================================
function rboProduct(o) {

}

//===================================================================================
rboProduct.prototype.attachProductModule = function() {
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
      "sTitle" : "Цена",
      "sClass" : "left",
      "mData" : "product_price"
    }, {
      "sTitle" : "Цена опт.",
      "sClass" : "left",
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

  //обработчик нажатия кнопки добавления документа
  $("#prd_add_btn").click(function(event) {
    self.createProduct();
    return false;
  });

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

}

//===================================================================================
rboProduct.prototype.setRW = function(oData) {
  return false;
}

//===================================================================================
rboProduct.prototype.readProduct = function(productId) {
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
}

//===================================================================================
rboProduct.prototype.saveProduct = function() {
  var self = this;
  var bValid = true;
  var oData = getFormData("prd-form", "rbo_products");
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
}

//===================================================================================
rboProduct.prototype.createProduct = function() {
  var self = this;
  self.showProductForm({});
}

// ===================================================================================
rboProduct.prototype.deleteProduct = function(productId) {
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
}

// ===================================================================================
rboProduct.prototype.showProductForm = function(i) {
  var self = this;

  setFormData("prd-form", "rbo_products", i);

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
}

// ===================================================================================
$(document).ready(function() {

  prd = new rboProduct({});
  prd.attachProductModule();
  prd.oCust.attachCustomerModule();

});
