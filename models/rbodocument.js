var comPath = "/components/com_rbo/";

//===================================================================================
function rboDoc(o) {
  this.sDocType = o.sDocType;
  this.sDocTypeTitle = o.sDocTypeTitle;
  this.sDocTypeListTitle = o.sDocTypeListTitle;
  this.allFields = o.allFields;
  this.printList = o.printList;

  this.oTable = initTableDocList(comPath, this.sDocType);
  this.oTableProducts = initTableProducts(comPath, this.sDocType);
  this.apiTableProducts = this.oTableProducts.api();
  initHeaderDocList(this.sDocTypeListTitle);

  this.oCust = new rboCust(null,this); //объект, содержащий поля покупателя
  this.tips = {};
  this.editing_lineNo = 0;
  this.lines_before_update = 0;

}

// ===================================================================================
rboDoc.prototype.setRW = function(sStatus) {
  if (sStatus == "выставлен" || sStatus == "оплачен" || sStatus == "удален" || sStatus == "подписан") {
    this.allFields.attr("disabled", "disabled");
    $("[id^='edit_product']").each(function(x, elem) {
      $(this).attr("href", "javascript:;");
    });
    return true;
  } else {
    this.allFields.removeAttr("disabled");
    return false;
  }
}

// ===================================================================================
rboDoc.prototype.readDoc = function(docId) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type" : this.sDocType
      }
    },
    url : comPath + "ajax.php?task=doc_read",
    success : function(doc_data) {
      self.showDocForm(doc_data);
    }
  });
}

// ===================================================================================
rboDoc.prototype.saveDoc = function(docId) {
  var self = this;
  var bValid = true;
  self.allFields.removeClass("ui-state-error");
  bValid = bValid && checkNotEmpty($("#doc_num"), "Номер", self.tips);
  bValid = bValid && checkNotEmpty($("#doc_date"), "Дата", self.tips);
  bValid = bValid && checkNotEmpty($("#doc_manager"), "Менеджер", self.tips);
  var p = self.apiTableProducts.rows().data();
  var pAr = new Array();
  for (var i = 0; i < p.length; i++)
    pAr[i] = p[i];

  if (!bValid)
    return;

  if (self.oCust.flds.cust_name == "")
    $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

  var oData = {
    "rbo_docs" : {
      "docId" : docId,
      "doc_type" : self.sDocType,
      "doc_num" : $("#doc_num").val(),
      "doc_date" : $("#doc_date").val(),
      "doc_sum" : $("#doc_sum").val(),
      "doc_base" : $("#doc_baseId").val(),// скрытое поле  testit 
      "doc_status" : $("#doc_status").val(),
      "doc_manager" : $("#doc_manager").val(),
      "custId" : $("#custId").val(),// скрытое поле в форме выбора клиента
      "doc_cust" : self.oCust.flds,
      "doc_firm" : $("#doc_firm").val(),
      "doc_products" : pAr
    }
  };

  var taskCmd = "doc_create";
  if (!IsNull(docId) && docId > 0)
    taskCmd = "doc_update";
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : oData,
    url : comPath + "ajax.php?task=" + taskCmd,
    success : function(doc_data) {
      $("#doc-form").dialog("close");
      self.oTable.fnDraw();
    }
  });
}

// ===================================================================================
rboDoc.prototype.createDoc = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
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
  });
}

// ===================================================================================
rboDoc.prototype.deleteDoc = function(docId) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type" : self.sDocType
      }
    },
    url : comPath + "ajax.php?task=doc_delete",
    success : function(doc_data) {
      self.oTable.fnDraw();
    }
  });

  $("#doc-form").dialog("close");
}

// ===================================================================================
rboDoc.prototype.showDocForm = function(i) {
  var self = this;
  $("#doc_num").val(i.doc_num);
  $("#doc_date").val(i.doc_date);
  $("#doc_sum").val(i.doc_sum);
  $("#doc_status").val(i.doc_status);

  var sDocBase = "";
  $("#doc_baseId").val(i.doc_base);
  if ((i.doc_base > 0) && !IsNull(i.doc_base_doc)) {
    sDocBase = "Счет №" + i.doc_base_doc.doc_num + " от " + i.doc_base_doc.doc_date;
  }
  $("#doc_base").val(sDocBase);

  $('#doc_manager option:selected').each(function() {
    this.selected = false;
  });
  if (!IsEmpty(i.doc_manager))
    $("#doc_manager option:contains('" + i.doc_manager + "')").prop("selected", "selected");

  self.oCust = new rboCust(i.doc_cust,this);
  self.oCust.setCustFlds('saved');
  
  if (!IsNull(i.doc_firm))
    $("#doc_firm option:contains('" + i.doc_firm.toUpperCase() + "')").prop("selected", "selected");
  $("#doc_rem").val(i.doc_rem);

  self.oTableProducts.fnClearTable();
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++)
      i.doc_products[x].lineNo = x;
    self.oTableProducts.fnAddData(i.doc_products);
  }
  var readOnly = this.setRW(i.doc_status);

  var oBtns = {};
  if (!readOnly) {
    oBtns["Удалить"] = function() {
      Ask("Документ будет удален. Продолжить?", "Удалить документ", "Отмена", function() {
        self.deleteDoc(i.docId);
      }, null, "#dialog-confirm");
    }
  }

  if (!IsNull(self.printList) && self.printList.length > 0) {
    for (var x = 0; x < self.printList.length; x++) {
      oBtns[self.printList[x].title] = function(event) {
        self.showPrintView($(event.target).text(),i.docId);
      };
    }
  }

  oBtns["Сохранить"] = function() {
    self.saveDoc(i.docId);
  };

  oBtns["Отмена"] = function() {
    $("#doc-form").dialog("close");
  };

  $("#doc-form").dialog({
    title : self.sDocTypeTitle + " №" + $('#doc_num').val(),
    buttons : oBtns
  });

  $("#doc-form").dialog("open");
}

//===================================================================================
rboDoc.prototype.showPrintView = function(title,docId) {
  var self = this;
  var viewname = "";
  if (!IsNull(self.printList) && self.printList.length > 0) {
    for (var x = 0; x < self.printList.length; x++) {
      if (self.printList[x].title == title) {
        viewname = self.printList[x].viewname;
        break;
      }
    }
  }
  if (viewname != "")
    window.open('index.php?option=com_rbo&view=' + viewname + '&format=raw&docid=' + docId, '_blank');
}

//===================================================================================
rboDoc.prototype.custSearch = function() {
  this.oCust.custSearch();
}

// ===================================================================================
rboDoc.prototype.showProductForm = function(x) {// x-номер редактируемой строки, x=null-добавляем
  var self = this;
  self.editing_lineNo = x;

  var p = self.oTableProducts.fnGetData(x);
  if (IsNull(p))
    self.lines_before_update = 0;
  else
    self.lines_before_update = p.length;

  $("#prodId").val(p.productId);
  $("#prod_code").val(p.product_code);
  $('#prod_name option').remove();
  if (x >= 0) {
    $('#prod_name').append('<option value="">' + p.product_name + '</option>');
  }
  $("#prod_name option:first").prop("selected", "selected");
  $("#prod_price").val(p.product_price);
  $("#prod_cnt").val(p.product_cnt);
  $("#prod_sum").val(p.product_sum);

  $("#newline-form").dialog({
    title : "Позиция - " + p.product_code,
    buttons : {
      "Удалить" : function() {
        Ask("Удалить строку из документа?", "Удалить", "Отмена", function() {
          if (self.editing_lineNo >= 0) {
            self.oTableProducts.fnDeleteRow(self.editing_lineNo);
          }
          $("#newline-form").dialog("close");
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
        if (self.editing_lineNo >= 0) {
          p.lineNo = self.editing_lineNo;
          self.oTableProducts.fnUpdate(p, x);
        } else {
          p.lineNo = self.lines_before_update;
          self.oTableProducts.fnAddData(p);
        }

        var pAll = self.oTableProducts.fnGetData();
        var iSum = 0;
        for (var x = 0; x < pAll.length; x++) {
          iSum += Number(pAll[x].product_sum);
        }
        $('#doc_sum').val(iSum);

        $("#newline-form").dialog("close");
      },

      "Отмена" : function() {
        $("#newline-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#newline-form").dialog("open");

}

// ===================================================================================
rboDoc.prototype.productSearch = function() {
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
rboDoc.prototype.setProductPrice = function() {
  var oVal = $("#prod_name option:selected").val();
  $("#newline-form").dialog("option", "title", "Позиция - " + oVal);
  var arProd = oVal.split("|");
  $("#prodId").val(arProd[0]);
  $("#prod_price").val(arProd[1]);
  $("#prod_code").val(arProd[2]);
  $("#prod_cnt").val(1);
  $("#prod_price1").html("Цена Опт.1= " + arProd[3] + "р.");
  this.calcSum();
}

// ===================================================================================

//===================================================================================
rboDoc.prototype.calcSum = function() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());
}

//===================================================================================
function rboShipment(o) {
  rboShipment.superclass.constructor.apply(this, arguments);
  this.oDoc = {
    doctId : 0,
    doc_num : 0,
    doc_date : 0,
    doc_status : ""
  }

}
//===================================================================================
extendObject(rboShipment, rboDoc);
//===================================================================================

// ===================================================================================
rboShipment.prototype.readDoc = function(docId) {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type" : self.sDocType
      }
    },
    url : comPath + "ajax.php?task=doc_read",
    success : function(doc_data) {
      self.oDoc.doctId = doc_data.docId;
      self.oDoc.doc_num = doc_data.doc_num;
      self.oDoc.doc_date = doc_data.doc_date;
      self.oDoc.doc_status = doc_data.doc_status;
      self.showDocForm(doc_data);
    }
  });
}

// ===================================================================================
rboShipment.prototype.createDoc = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "doc_type" : self.sDocType
      }
    },
    url : comPath + "ajax.php?task=get_doc_num",
    success : function(p) {
      self.oDoc.doctId = 0;
      self.oDoc.doc_num = p.new_num;
      self.oDoc.doc_date = p.new_date;
      self.oDoc.doc_status = "";
      var i = {};
      i.doc_num = p.new_num;
      i.doc_date = p.new_date;
      self.showDocForm(i);
    }
  });
}


