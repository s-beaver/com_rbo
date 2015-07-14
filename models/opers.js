var comPath = "/components/com_rbo/";
var oper;

//===================================================================================
function rbOper(o) {

  this.oProduct = new rboProduct();
  this.oCust = new rboCust();

  this.arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
  this.bCustInput = 'select';

}

//===================================================================================
rbOper.prototype.attachOperModule = function() {
  var self = this;
  
  self.oTable = $('#TableOper').dataTable({
    "bJQueryUI" : true,
    "bProcessing" : true,
    "bServerSide" : true,
    "sAjaxSource" : comPath + "ajax.php?task=get_oper_list",
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
        return "<a href='javascript:oper.readOper(" + source.sKey + ")'>#" + source.sKey + "</a>";
      }
    }, {
      "sTitle" : "Дата",
      "mData" : "sDate"
    }, {
      "sTitle" : "Операция",
      "mData" : "sOperType"
    }, {
      "sTitle" : "Покупатель",
      "mData" : "sContragent"
    }, {
      "sTitle" : "Сумма",
      "sClass" : "center",
      "mData" : "sSum"
    }, {
      "sTitle" : "Фирма",
      "sClass" : "center",
      "mData" : "sCashPlace1"
    }, {
      "sTitle" : "Менеджер",
      "mData" : "sOperMan"
    } ],
    "oLanguage" : {
      "sProcessing" : "Подождите...",
      "sLengthMenu" : "Показать _MENU_ строк",
      "sZeroRecords" : "Записи отсутствуют.",
      "sInfo" : "Операции с _START_ по _END_ (всего: _TOTAL_)",
      "sInfoEmpty" : "Операций нет",
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
  $("#oper_add_btn").click(function(event) {
    self.createOper();
    return false;
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
rbOper.prototype.setRW = function(sStatus) {
    return false;
}

//===================================================================================
rbOper.prototype.readOper = function(sKey) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "SS_opers" : {
        "sKey" : sKey
      }
    },
    url : comPath + "ajax.php?task=oper_read",
    success : function(oper_data) {
      self.showOperForm(oper_data);
    }
  });
}

//===================================================================================
rbOper.prototype.saveOper = function(sKey) {
  var self = this;
  var bValid = true;
}

//===================================================================================
rbOper.prototype.createOper = function() {
  var self = this;
  /*$.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "SS_opers" : {
        "doc_type" : self.sDocType
      }
    },
    url : comPath + "ajax.php?task=get_doc_num",
    success : function(p) {
      var i = {};
      i.doc_num = p.new_num;
      i.doc_date = p.new_date;
      self.showDocForm(i);
    }
  });*/
}

// ===================================================================================
rbOper.prototype.deleteOper = function(sKey) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "SS_opers" : {
        "sKey" : sKey
      }
    },
    url : comPath + "ajax.php?task=oper_delete",
    success : function(oper_data) {
      self.oTable.fnDraw();
    }
  });

  $("#doc-form").dialog("close");
}

// ===================================================================================
rbOper.prototype.showOperForm = function(i) {
  var self = this;
  $("#sDate").val(i.sDate);
  $("#sSum").val(i.sSum);
  $("#sRem").html(i.sRem);
  /*Как быть если автор или операция не попадают в список select
   * Продумать общую наличную кассу и общий склад при разных банках
   * Надо ли иметь отдельную сумму для оплаты или нет?*/

  $('#sOperType option:selected').each(function() {
    this.selected = false;
  });
  if (!IsEmpty(i.sOperType))
    $("#sOperType option:contains('" + i.sOperType + "')").prop("selected", "selected");
  
  $('#sOperMan option:selected').each(function() {
    this.selected = false;
  });
  if (!IsEmpty(i.sOperMan))
    $("#sOperMan option:contains('" + i.sOperMan + "')").prop("selected", "selected");
  
  /*var sDocBase = "";
  $("#doc_baseId").val(i.doc_base);
  if ((i.doc_base > 0) && !IsNull(i.doc_base_doc)) {
    sDocBase = "Счет №" + i.doc_base_doc.doc_num + " от " + i.doc_base_doc.doc_date;
  }
  $("#doc_base").val(sDocBase);*/

  /*this.oCust = NullTo(i.doc_cust, {
    cust_data : {}
  });
  this.oCust.cust_data = NullTo(this.oCust.cust_data, {});
  this.setCustFlds('saved');
  if (!IsNull(i.doc_firm))
    $("#doc_firm option:contains('" + i.doc_firm.toUpperCase() + "')").prop("selected", "selected");
  $("#doc_rem").val(i.doc_rem);*/

  var readOnly = this.setRW(i.doc_status);

  var oBtns = {};
  if (!readOnly) {
    oBtns["Удалить"] = function() {
      Ask("Операция будет удалена. Продолжить?", "Удалить операцию", "Отмена", function() {
        self.deleteOper(i.sKey);
      }, null, "#dialog-confirm");
    }
  }

  oBtns["Сохранить"] = function() {
    self.saveOper(i.sKey);
  };

  oBtns["Отмена"] = function() {
    $("#oper-form").dialog("close");
  };

  $("#oper-form").dialog({
    title : i.sOperType + " #" + i.sKey,
    buttons : oBtns
  });

  $("#oper-form").dialog("open");
}

//===================================================================================
rbOper.prototype.productSearch = function() {
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

// ===================================================================================
rbOper.prototype.setProductPrice = function() {
  var oVal = $("#prod_name option:selected").val();
  $("#product-form").dialog("option", "title", "Позиция - " + oVal);
  var arProd = oVal.split("|");
  $("#prodId").val(arProd[0]);
  $("#prod_price").val(arProd[1]);
  $("#prod_code").val(arProd[2]);
  $("#prod_cnt").val(1);
  $("#prod_price1").html(arProd[3] + "р.");
  this.calcSum();
}

//===================================================================================
rbOper.prototype.calcSum = function() {
  $("#sSum").val($("#prod_price").val() * $("#prod_cnt").val());
}

// ===================================================================================
$(document).ready(function() {
  
  oper = new rbOper({
  });
  oper.attachOperModule();
  oper.oProduct.attachProductModule();
  oper.oCust.attachCustomerModule();

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  $("#sDate").datepicker({
    showButtonPanel : true,
    dateFormat : "dd.mm.yy"
  });

});
