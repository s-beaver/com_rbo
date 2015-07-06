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

  this.oCust = {
    cust_data : {}
  }; //объект, содержащий поля покупателя, пришедший из запроса к БД
  this.arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
  this.bCustInput = 'select';
  this.tips = {};
  this.editing_lineNo = 0;
  this.lines_before_update = 0;

}

//===================================================================================
rboDoc.prototype.convertSelect2Input = function() {
  if (this.bCustInput == 'select') {
    tag = $('#cust_name').parent().html();
    tag = tag.replace("<select", "<input type=\"text\"");
    tag = tag.replace("onChange", "onChange1");
    tag = tag.replace(">", "/>");
    tag = tag.replace("</select>", "");
    $('#cust_name').parent().html(tag);
    this.bCustInput = 'input';
  }
}

//===================================================================================
rboDoc.prototype.convertInput2Select = function() {
  if (this.bCustInput == 'input') {
    tag = $('#cust_name').parent().html();
    tag = tag.replace("<input type=\"text\"", "<select");
    tag = tag.replace("onChange1", "onChange");
    tag = tag.replace("/>", "></select>");
    $('#cust_name').parent().html(tag);
    this.bCustInput = 'select';
  }
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

  if (self.oCust.cust_name == "")
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
      "doc_cust" : self.oCust,
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

  //testit
  var sDocBase = "";
  $("#doc_baseId").val(i.doc_base);
  if ((i.doc_base > 0) && !IsNull(i.doc_base_doc)) {
    sDocBase = "Счет №" + i.doc_base_doc.doc_num + " от " + i.doc_base_doc.doc_date;
  }
  $("#doc_base").val(sDocBase);
  //testit

  $('#doc_manager option:selected').each(function() {
    this.selected = false;
  });
  if (!IsEmpty(i.doc_manager))
    $("#doc_manager option:contains('" + i.doc_manager + "')").prop("selected", "selected");

  this.oCust = NullTo(i.doc_cust, {
    cust_data : {}
  });
  this.oCust.cust_data = NullTo(this.oCust.cust_data, {});
  this.setCustFlds('saved');
  if (!IsNull(i.doc_firm))
    $("#doc_firm option:contains('" + i.doc_firm.toUpperCase() + "')").prop("selected", "selected");
  $("#doc_rem").val(i.doc_rem);

  this.oTableProducts.fnClearTable();
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++)
      i.doc_products[x].lineNo = x;
    this.oTableProducts.fnAddData(i.doc_products);
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
rboDoc.prototype.showCustForm = function() {
  var self = this;
  var custId = $("#custId").val();
  var custName = $("#doc_cust").val();
  self.arSearchedCust = new Array();
  if (this.bCustInput == 'select') {
    $('#cust_name option').remove();
    $('#cust_name').append('<option value="">' + custName + '</option>');
    $("#cust_name option:contains('" + custName + "')").prop("selected", "selected");
  }
  self.setCustFlds('saved');

  $("#cust-form").dialog({
    title : "Выбор покупателя",
    buttons : {
      "Новый покупатель" : function() {
        self.setCustFlds('clear');
        self.convertSelect2Input();
      },

      "Очистить" : function() {
        self.setCustFlds('clear');
        $("#cust-form").dialog("close");
      },

      "Сохранить" : function() {
        if (self.bCustInput == 'input')
          $("#doc_cust").val($("#cust_name").val());
        self.saveCustFlds();
        $("#cust-form").dialog("close");
      },

      "Отмена" : function() {
        self.setCustFlds('saved');
        $("#cust-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#cust-form").dialog("open");

  return false;
}

// ===================================================================================
rboDoc.prototype.custSearch = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#cust_search").val()
    },
    url : comPath + "ajax.php?task=cust_search",
    success : function(p) {
      self.arSearchedCust = p.result;
      self.convertInput2Select();
      $('#cust_name option').remove();
      if (p.result.length > 0) {
        for (var i = 0; i < p.result.length; i++) {
          $('#cust_name').append('<option value="' + i + '">' + p.result[i].cust_name + '</option>');
        }
        if (p.count > p.result.length) {
          $('#cust_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
        }
        $("#cust_name option:first").prop("selected", "selected");
        self.setCustFlds('selected');
      }
    }
  });
}

// ===================================================================================
/*
 * Устанавливает все поля покупателя в форму. Данные берет в зависимости от
 * параметра cmd. Если параметр равен 'saved', то берет объекта oCust, если
 * параметр равен 'selected', то берет текущую позицию в элементе cust_name
 * считает это порядовым номером в массиве arSearchedCust. Если параметр равен
 * 'clear', то очищает поля
 */
rboDoc.prototype.setCustFlds = function(cmd) {
  var self = this;
  cmd = NullTo(cmd, 'saved');
  var f = {};
  var fd = {};
  if (cmd == 'clear') {
    self.arSearchedCust = new Array();
    self.oCust = {
      cust_data : {}
    };
    if (self.bCustInput == 'select')
      $('#cust_name option').remove();
    else
      $('#cust_name').val();
  }
  if (cmd == 'saved') {
    f = self.oCust;
    fd = NullTo(f.cust_data, {});
  }
  if (cmd == 'selected') {
    var pos = $("#cust_name option:selected").val();
    if (pos >= 0) {
      f = self.arSearchedCust[pos];
      fd = f.cust_data;
    }
  }
  $("#cust-form").dialog("option", "title", f.custId + "|" + f.cust_name);
  $("#custId").val(f.custId);
  $("#doc_cust").val(f.cust_name);
  //$("#cust_name").val(f.cust_name);
  $("#cust_fullname").val(f.cust_fullname);
  $("#cust_email").val(f.cust_email);
  $("#cust_phone").val(f.cust_phone);
  $("#cust_inn").val(fd.cust_inn);
  $("#cust_kpp").val(fd.cust_kpp);
  $("#cust_okpo").val(fd.cust_okpo);
  $("#cust_addr").val(fd.cust_addr);
  $("#cust_ogrn").val(fd.cust_ogrn);
  $("#cust_bank").val(fd.cust_bank);
  $("#cust_bik").val(fd.cust_bik);
  $("#cust_rch").val(fd.cust_rch);
  $("#cust_kch").val(fd.cust_kch);
  $("#cust_nonds").prop("checked", (fd.cust_nonds == "1"));
}

//===================================================================================
rboDoc.prototype.saveCustFlds = function() {
  this.oCust.custId = $("#custId").val();
  this.oCust.cust_name = $("#doc_cust").val();
  this.oCust.cust_fullname = $("#cust_fullname").val();
  this.oCust.cust_email = $("#cust_email").val();
  this.oCust.cust_phone = $("#cust_phone").val();
  this.oCust.cust_data.cust_inn = $("#cust_inn").val();
  this.oCust.cust_data.cust_kpp = $("#cust_kpp").val();
  this.oCust.cust_data.cust_okpo = $("#cust_okpo").val();
  this.oCust.cust_data.cust_addr = $("#cust_addr").val();
  this.oCust.cust_data.cust_ogrn = $("#cust_ogrn").val();
  this.oCust.cust_data.cust_bank = $("#cust_bank").val();
  this.oCust.cust_data.cust_bik = $("#cust_bik").val();
  this.oCust.cust_data.cust_rch = $("#cust_rch").val();
  this.oCust.cust_data.cust_kch = $("#cust_kch").val();
  this.oCust.cust_data.cust_nonds = $("#cust_nonds").prop("checked") ? "1" : "0";
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

//===================================================================================
rboShipment.prototype.chooseBaseDoc = function() {
  var self = this;
  var custId = $("#custId").val();
  var custName = $("#doc_cust").val();
  self.arSearchedCust = new Array();
  if (self.bCustInput == 'select') {
    $('#cust_name option').remove();
    //$('#cust_name').append('<option value="">' + custName + '</option>');
    //$("#cust_name option:contains('" + custName + "')").prop("selected", "selected");
    $('#cust_base_doc option').remove();
  }
  self.setCustFlds('saved');

  $("#cust-form").dialog({
    title : "Выбор документа-основания",
    buttons : {
      "Сохранить" : function() {
        var invId = $("#cust_base_doc option:selected").val();
        //$("#doc_baseId").val(invId);
        $.ajax({
          dataType : 'json',
          type : "POST",
          data : {
            "rbo_docs" : {
              "docId" : invId,
              "doc_type" : 'счет'//это правильно
            }
          },
          url : comPath + "ajax.php?task=doc_read",
          success : function(doc_data) {
            doc_data.doc_base = invId;
            doc_data.doc_base_doc.doc_num = doc_data.doc_num;
            doc_data.doc_base_doc.doc_date = doc_data.doc_date;

            doc_data.docId = self.oDoc.doctId;
            doc_data.doc_num = self.oDoc.doc_num;
            doc_data.doc_date = self.oDoc.doc_date;
            doc_data.doc_status = self.oDoc.doc_status;
            self.showDocForm(doc_data);
          }
        });

        $("#cust-form").dialog("close");
      },

      "Отмена" : function() {
        self.setCustFlds('saved');
        $("#cust-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#cust-form").dialog("open");

  return false;
}

// ===================================================================================
rboShipment.prototype.setBaseDocList = function() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "sSearch" : $("#cust_name option:selected").html()
    },
    url : comPath + "ajax.php?task=get_doc_list&doc_type=счет",//счет - это правильно!
    success : function(s) {
      $('#cust_base_doc option').remove();
      p = s.aaData;
      if (p.length > 0) {
        for (var i = 0; i < p.length; i++) {
          $('#cust_base_doc').append('<option value="' + p[i].docId + '">Счет №' + p[i].doc_num + " от " + p[i].doc_date + " (" + p[i].doc_sum + '=)</option>');
        }
        $("#cust_base_doc option:first").prop("selected", "selected");
      }
    }
  });
}

// ===================================================================================
rboShipment.prototype.custSearch = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#cust_search").val()
    },
    url : comPath + "ajax.php?task=cust_search",
    success : function(p) {
      self.arSearchedCust = p.result;
      self.convertInput2Select();
      $('#cust_name option').remove();
      if (p.result.length > 0) {
        for (var i = 0; i < p.result.length; i++) {
          $('#cust_name').append('<option value="' + i + '">' + p.result[i].cust_name + '</option>');
        }
        if (p.count > p.result.length) {
          $('#cust_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
        }
        $("#cust_name option:first").prop("selected", "selected");
        self.setCustFlds('selected');
        self.setBaseDocList();
      }
    }
  });
}
