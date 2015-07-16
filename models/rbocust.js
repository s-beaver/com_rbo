/**
 * Объект JavaScript для работы с формой выбора контрагента, добавления нового
 * контрагента
 */

//===================================================================================
function rboCust(parentDoc) {
  this.flds = {
    cust_data : {}
  };
  this.flds.cust_data = NullTo(this.flds.cust_data, {});
  this.arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
  this.bCustInput = 'select';
  this.parentDoc = parentDoc;
}

//===================================================================================
rboCust.prototype.attachCustomerModule = function() {
  var self = this;
  //подключаем форму выбора контрагента
  $("#cust-form").dialog({
    autoOpen : false,
    height : 550,
    width : 750,
    modal : true,
    resizable : true
  });

  //подключаем форму выбора документа-основания
  $("#base-doc-form").dialog({
    autoOpen : false,
    height : 300,
    width : 700,
    modal : true,
    resizable : true
  });

  //нажатие кнопки выбора контрагента
  $("#cedit").click(function(event) {
    self.chooseCustomer();
    return false;
  });

  //нажатие кнопки поиска контрагентов по подстроке
  $("#cust_search_btn").click(function(event) {
    self.custSearch();
    return false;
  });

  //смена текущего контрагента в списке найденных в поиске
  $("#cust_name").change(function(event) {
    self.setCustFlds('selected');
    return false;
  });

  //вызов формы выбора документа-основания
  $("#baseedit").click(function(event) {
    self.chooseBaseDoc(doc);
    return false;
  });

  //нажатие кнопки поиска документов оснований по подстроке контрагента
  $("#base-doc-search-btn").click(function(event) {
    self.baseDocSearch();
    return false;
  });

  //смена текущего контрагента в списке найденных в поиске
  $("#base-doc-cust-name").change(function(event) {
    self.setBaseDocList();
    return false;
  });

}

//===================================================================================
/* Преобразует тэг select в input для ввода там поисковой строки */
rboCust.prototype.convertSelect2Input = function() {
  if (this.bCustInput == 'select') {
    tag = $('#cust_name').parent().html();
    tag = tag.replace("<select", "<input type=\"text\"");
    tag = tag.replace("onChange", "onChange1");//а ведь этого сейчас нет. событие првязывается через jquery
    tag = tag.replace(">", "/>");
    tag = tag.replace("</select>", "");
    $('#cust_name').parent().html(tag);
    this.bCustInput = 'input';
  }
}

//===================================================================================
/* Преобразует тэг input в select для выбора из найденных вариантов */
rboCust.prototype.convertInput2Select = function() {
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
/*
 * Устанавливает все поля покупателя в форму. Данные берет в зависимости от
 * параметра cmd. Если параметр равен 'saved', то берет объекта flds, если
 * параметр равен 'selected', то берет текущую позицию в элементе cust_name
 * считает это порядовым номером в массиве arSearchedCust. Если параметр равен
 * 'clear', то очищает поля
 */
rboCust.prototype.setCustFlds = function(cmd, o) {
  var self = this;
  cmd = NullTo(cmd, 'saved');
  var f = {};
  var fd = {};
  if (cmd == 'clear') {
    self.arSearchedCust = new Array();
    self.flds = {
      cust_data : {}
    };
    if (self.bCustInput == 'select')
      $('#cust_name option').remove();
    else
      $('#cust_name').val();
  }
  if (cmd == 'saved') {
    if (!IsNull(o) && IsObject(o)) {
      this.flds = NullTo(o, {
        cust_data : {}
      });
      this.flds.cust_data = NullTo(this.flds.cust_data, {});
      this.arSearchedCust = new Array(); //????
    }

    f = self.flds;
    fd = NullTo(f.cust_data, {});
  }
  if (cmd == 'selected') {
    var pos = $("#cust_name option:selected").val();
    if (pos >= 0) {
      f = self.arSearchedCust[pos];
      fd = f.cust_data;
    }
  }
  $("#cust-form").dialog("option", "title", NullTo(f.custId, "") + "|" + NullTo(f.cust_name, "Заполните поля"));
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
rboCust.prototype.saveCustFlds = function() {
  this.flds.custId = $("#custId").val();
  this.flds.cust_name = $("#doc_cust").val();
  this.flds.cust_fullname = $("#cust_fullname").val();
  this.flds.cust_email = $("#cust_email").val();
  this.flds.cust_phone = $("#cust_phone").val();
  this.flds.cust_data.cust_inn = $("#cust_inn").val();
  this.flds.cust_data.cust_kpp = $("#cust_kpp").val();
  this.flds.cust_data.cust_okpo = $("#cust_okpo").val();
  this.flds.cust_data.cust_addr = $("#cust_addr").val();
  this.flds.cust_data.cust_ogrn = $("#cust_ogrn").val();
  this.flds.cust_data.cust_bank = $("#cust_bank").val();
  this.flds.cust_data.cust_bik = $("#cust_bik").val();
  this.flds.cust_data.cust_rch = $("#cust_rch").val();
  this.flds.cust_data.cust_kch = $("#cust_kch").val();
  this.flds.cust_data.cust_nonds = $("#cust_nonds").prop("checked") ? "1" : "0";
}

//===================================================================================
rboCust.prototype.chooseCustomer = function() {
  var self = this;
  var custId = $("#custId").val();
  var custName = $("#doc_cust").val();
  self.convertInput2Select();
  self.arSearchedCust = new Array();

  $('#cust_name option').remove();
  $('#cust_name').append('<option value="">' + custName + '</option>');
  $("#cust_name option:first").prop("selected", "selected");

  self.setCustFlds('saved');//установим поля в форме из текущего выбранного контрагента (объект)

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
        var saveCust = self.flds.cust_name;
        if (self.bCustInput == 'input')
          $("#doc_cust").val($("#cust_name").val());
        self.saveCustFlds();

        if (saveCust != $("#doc_cust").val()) {//Если мы выбрали контрагента и он отличается от контрагента в документе-основании, то надо очистить документ-основание
          $("#doc_baseId").val("0");
          $("#doc_base").val("");
        }

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

//===================================================================================
/*
 * Поиск покупателя по подстроке в элементе cust_search. Список найденных
 * вариантов записывается в тэг select cust_name
 */
rboCust.prototype.custSearch = function() {
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

//===================================================================================
rboCust.prototype.chooseBaseDoc = function() {
  var self = this;
  self.arSearchedCust = new Array();
  $('#base-doc-cust-name option').remove();
  $('#base-doc-doc option').remove();
  self.setCustFlds('saved');

  $("#base-doc-form").dialog({
    title : "Выбор документа-основания",
    buttons : {
      "Сохранить" : function() {
        var invId = $("#base-doc-doc option:selected").val();
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

            doc_data.docId = self.parentDoc.doctId;//self.parentDoc.oDoc.doctId;
            doc_data.doc_num = $("#doc_num").val();//self.parentDoc.oDoc.doc_num;
            doc_data.doc_date = $("#doc_date").val();//self.parentDoc.oDoc.doc_date;
            doc_data.doc_status = $("#doc_status").val();//self.parentDoc.oDoc.doc_status;
            self.parentDoc.showDocForm(doc_data);
          }
        });

        $("#base-doc-form").dialog("close");
      },

      "Отмена" : function() {
        self.setCustFlds('saved');
        $("#base-doc-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#base-doc-form").dialog("open");
  return false;

}

//===================================================================================
rboCust.prototype.baseDocSearch = function() {
  var self = this;
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#base-doc-search").val()
    },
    url : comPath + "ajax.php?task=cust_search",
    success : function(p) {
      self.arSearchedCust = p.result;
      $('#base-doc-cust-name option').remove();
      if (p.result.length > 0) {
        for (var i = 0; i < p.result.length; i++) {
          $('#base-doc-cust-name').append('<option value="' + i + '">' + p.result[i].cust_name + '</option>');
        }
        if (p.count > p.result.length) {
          $('#base-doc-cust-name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
        }
        $("#base-doc-cust-name option:first").prop("selected", "selected");
        self.setCustFlds('selected');
        self.setBaseDocList();
      }
    }
  });
}

// ===================================================================================
rboCust.prototype.setBaseDocList = function() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "sSearch" : $("#base-doc-cust-name option:selected").html()
    },
    url : comPath + "ajax.php?task=get_doc_list&doc_type=счет",//счет - это правильно!
    success : function(s) {
      $('#base-doc-doc option').remove();
      p = s.aaData;
      if (p.length > 0) {
        for (var i = 0; i < p.length; i++) {
          $('#base-doc-doc').append('<option value="' + p[i].docId + '">Счет №' + p[i].doc_num + " от " + p[i].doc_date + " (" + p[i].doc_sum + '=)</option>');
        }
        $("#base-doc-doc option:first").prop("selected", "selected");
      }
    }
  });
}
