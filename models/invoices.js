var oTable;
var oTableProducts;
var apiTableProducts;
var oTableTools;
var comPath = "/components/com_rbo/";
var allFields;
var tips;
var editing_lineNo;
var lines_before_update;
var arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
var oCust = {
  cust_data : {}
}; //объект, содержащий поля покупателя, пришедший из запроса к БД
var bCustInput = 'select';
var sDocTypeListTitle = 'Счета';
var sDocTypeTitle = 'Счет';
var sDocType = 'счет';

//===================================================================================
function convertSelect2Input() {
  if (bCustInput == 'select') {
    tag = $('#cust_name').parent().html();
    tag = tag.replace("<select", "<input type=\"text\"");
    tag = tag.replace("onChange", "onChange1");
    tag = tag.replace(">", "/>");
    tag = tag.replace("</select>", "");
    $('#cust_name').parent().html(tag);
    bCustInput = 'input';
  }
}

//===================================================================================
function convertInput2Select() {
  if (bCustInput == 'input') {
    tag = $('#cust_name').parent().html();
    tag = tag.replace("<input type=\"text\"", "<select");
    tag = tag.replace("onChange1", "onChange");
    tag = tag.replace("/>", "></select>");
    $('#cust_name').parent().html(tag);
    bCustInput = 'select';
  }
}

// ===================================================================================
function setRW(sStatus) {
  if (sStatus == "выставлен" || sStatus == "оплачен" || sStatus == "удален") {
    allFields.attr("disabled", "disabled");
    $("[id^='edit_product']").each(function(x, elem) {
      $(this).attr("href", "javascript:;");
    });
    return true;
  } else {
    allFields.removeAttr("disabled");
    return false;
  }
}

// ===================================================================================
function readDoc(docId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type" : sDocType
      }
    },
    url : comPath + "ajax.php?task=doc_read",
    success : function(doc_data) {
      showDocForm(doc_data);
    }
  });
}

// ===================================================================================
function saveDoc(docId) {
  var bValid = true;
  allFields.removeClass("ui-state-error");
  bValid = bValid && checkNotEmpty($("#doc_num"), "Номер", tips);
  bValid = bValid && checkNotEmpty($("#doc_date"), "Дата", tips);
  bValid = bValid && checkNotEmpty($("#doc_manager"), "Менеджер", tips);
  var p = apiTableProducts.rows().data();
  var pAr = new Array();
  for (var i = 0; i < p.length; i++)
    pAr[i] = p[i];

  if (!bValid)
    return;

  if (oCust.cust_name == "")
    $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

  var oData = {
    "rbo_docs" : {
      "docId" : docId,
      "doc_type" : sDocType,
      "doc_num" : $("#doc_num").val(),
      "doc_date" : $("#doc_date").val(),
      "doc_sum" : $("#doc_sum").val(),
      "doc_status" : $("#doc_status").val(),
      "doc_manager" : $("#doc_manager").val(),
      "custId" : $("#custId").val(),// скрытое поле в форме выбора клиента
      "doc_cust" : oCust,
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
      oTable.fnDraw();
    }
  });
}

// ===================================================================================
function createDoc() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "doc_type" : sDocType
      }
    },
    url : comPath + "ajax.php?task=get_doc_num",
    success : function(p) {
      var i = {};
      i.doc_num = p.new_num;
      i.doc_date = p.new_date;
      showDocForm(i);
    }
  });
}

// ===================================================================================
function deleteDoc(docId) {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "rbo_docs" : {
        "docId" : docId,
        "doc_type" : sDocType
      }
    },
    url : comPath + "ajax.php?task=doc_delete",
    success : function(doc_data) {
      oTable.fnDraw();
    }
  });

  $("#doc-form").dialog("close");
}

// ===================================================================================
function showDocForm(i) {
  $("#doc_num").val(i.doc_num);
  $("#doc_date").val(i.doc_date);
  $("#doc_sum").val(i.doc_sum);
  $("#doc_status").val(i.doc_status);

  $('#doc_manager option:selected').each(function() {
    this.selected = false;
  });
  if (!IsEmpty(i.doc_manager))
    $("#doc_manager option:contains('" + i.doc_manager + "')").prop("selected", "selected");

  oCust = NullTo(i.doc_cust, {
    cust_data : {}
  });
  oCust.cust_data = NullTo(oCust.cust_data, {});
  setCustFlds('saved');
  if (!IsNull(i.doc_firm))
    $("#doc_firm option:contains('" + i.doc_firm.toUpperCase() + "')").prop("selected", "selected");
  $("#doc_rem").val(i.doc_rem);

  oTableProducts.fnClearTable();
  if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
    for (var x = 0; x < i.doc_products.length; x++)
      i.doc_products[x].lineNo = x;
    oTableProducts.fnAddData(i.doc_products);
  }
  var readOnly = setRW(i.doc_status);

  var oBtns = {};
  if (!readOnly) {
    oBtns["Удалить"] = function() {

      Ask("Документ будет удален. Продолжить?", "Удалить документ", "Отмена", function() {
        deleteDoc(i.docId);
      }, null, "#dialog-confirm");
    }

  }

  oBtns["Печатать"] = function() {
    window.open('index.php?option=com_rbo&view=printinv&format=raw&docid=' + i.docId, '_blank');
  };

  oBtns["Сохранить"] = function() {
    saveDoc(i.docId);
  };

  oBtns["Отмена"] = function() {
    $("#doc-form").dialog("close");
  };

  $("#doc-form").dialog({
    title : sDocTypeTitle + " №" + $('#doc_num').val(),
    buttons : oBtns
  });

  $("#doc-form").dialog("open");
}

// ===================================================================================
function showCustForm() {
  var custId = $("#custId").val();
  var custName = $("#doc_cust").val();
  arSearchedCust = new Array();
  if (bCustInput == 'select') {
    $('#cust_name option').remove();
    $('#cust_name').append('<option value="">' + custName + '</option>');
    $("#cust_name option:contains('" + custName + "')").prop("selected", "selected");
  }
  setCustFlds('saved');

  $("#cust-form").dialog({
    title : "Выбор покупателя",
    buttons : {
      "Новый покупатель" : function() {
        setCustFlds('clear');
        convertSelect2Input();
      },

      "Очистить" : function() {
        setCustFlds('clear');
        $("#cust-form").dialog("close");
      },

      "Сохранить" : function() {
        if (bCustInput == 'input')
          $("#doc_cust").val($("#cust_name").val());
        saveCustFlds();
        $("#cust-form").dialog("close");
      },

      "Отмена" : function() {
        setCustFlds('saved');
        $("#cust-form").dialog("close");
      }
    },
    resizable : true
  });

  $("#cust-form").dialog("open");

  return false;
}

// ===================================================================================
function custSearch() {
  $.ajax({
    dataType : 'json',
    type : "POST",
    data : {
      "search" : $("#cust_search").val()
    },
    url : comPath + "ajax.php?task=cust_search",
    success : function(p) {
      arSearchedCust = p.result;
      convertInput2Select();
      $('#cust_name option').remove();
      if (p.result.length > 0) {
        for (var i = 0; i < p.result.length; i++) {
          $('#cust_name').append('<option value="' + i + '">' + p.result[i].cust_name + '</option>');
        }
        if (p.count > p.result.length) {
          $('#cust_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
        }
        $("#cust_name option:first").prop("selected", "selected");
        setCustFlds('selected');
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
function setCustFlds(cmd) {
  cmd = NullTo(cmd, 'saved');
  var f = {};
  var fd = {};
  if (cmd == 'clear') {
    arSearchedCust = new Array();
    oCust = {
      cust_data : {}
    };
    if (bCustInput == 'select')
      $('#cust_name option').remove();
    else
      $('#cust_name').val();
  }
  if (cmd == 'saved') {
    f = oCust;
    fd = NullTo(f.cust_data, {});
  }
  if (cmd == 'selected') {
    var pos = $("#cust_name option:selected").val();
    if (pos >= 0) {
      f = arSearchedCust[pos];
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
function saveCustFlds() {
  oCust.custId = $("#custId").val();
  oCust.cust_name = $("#doc_cust").val();
  oCust.cust_fullname = $("#cust_fullname").val();
  oCust.cust_email = $("#cust_email").val();
  oCust.cust_phone = $("#cust_phone").val();
  oCust.cust_data.cust_inn = $("#cust_inn").val();
  oCust.cust_data.cust_kpp = $("#cust_kpp").val();
  oCust.cust_data.cust_okpo = $("#cust_okpo").val();
  oCust.cust_data.cust_addr = $("#cust_addr").val();
  oCust.cust_data.cust_ogrn = $("#cust_ogrn").val();
  oCust.cust_data.cust_bank = $("#cust_bank").val();
  oCust.cust_data.cust_bik = $("#cust_bik").val();
  oCust.cust_data.cust_rch = $("#cust_rch").val();
  oCust.cust_data.cust_kch = $("#cust_kch").val();
  oCust.cust_data.cust_nonds = $("#cust_nonds").prop("checked") ? "1" : "0";
}

// ===================================================================================
function showProductForm(x) {// x-номер редактируемой строки, x=null-добавляем
  editing_lineNo = x;

  var p = oTableProducts.fnGetData(x);
  if (IsNull(p))
    lines_before_update = 0;
  else
    lines_before_update = p.length;

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
          if (editing_lineNo >= 0) {
            oTableProducts.fnDeleteRow(editing_lineNo);
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
        if (editing_lineNo >= 0) {
          p.lineNo = editing_lineNo;
          oTableProducts.fnUpdate(p, x);
        } else {
          p.lineNo = lines_before_update;
          oTableProducts.fnAddData(p);
        }

        var pAll = oTableProducts.fnGetData();
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
function productSearch() {
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
            '<option value="' + p.result[i].productID + "|" + p.result[i].price + "|" + p.result[i].product_code + '">' + p.result[i].name + '</option>');
      }
      $("#prod_name option:first").prop("selected", "selected");
      setProductPrice();
      if (p.count > p.result.length) {
        $('#prod_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
      }

    }
  });
}

// ===================================================================================
function setProductPrice() {
  var oVal = $("#prod_name option:selected").val();
  $("#newline-form").dialog("option", "title", "Позиция - " + oVal);
  var arProd = oVal.split("|");
  $("#prodId").val(arProd[0]);
  $("#prod_price").val(arProd[1]);
  $("#prod_code").val(arProd[2]);
  $("#prod_cnt").val(1);
  calcSum();
}

// ===================================================================================
function calcSum() {
  $("#prod_sum").val($("#prod_price").val() * $("#prod_cnt").val());
}

// ===================================================================================
$(document).ready(function() {

  $("#cedit").click(function(event) {
    showCustForm();
    return false;
  });

  allFields = $("#doc_num").add($("#doc_date")).add($("#doc_manager")).add($("#doc_firm")).add($("#cedit"));
  tips = $(".validateTips");

  oTable = initTableDocList(comPath, sDocType);
  oTableProducts = initTableProducts(comPath, sDocType);
  apiTableProducts = oTableProducts.api();

  $("#doc-form").dialog({
    autoOpen : false,
    height : 550,
    width : 900,
    modal : true,
    resizable : true
  });

  $("#newline-form").dialog({
    autoOpen : false,
    height : 300,
    width : 650,
    modal : true,
    resizable : true
  });

  $("#cust-form").dialog({
    autoOpen : false,
    height : 550,
    width : 700,
    modal : true,
    resizable : true
  });

  $("#dialog-confirm").dialog({
    autoOpen : false
  });

  $("#doc_date").datepicker({
    showButtonPanel : true,
    dateFormat : "dd.mm.yy"
  });

  initHeaderDocList(sDocTypeListTitle);

});
