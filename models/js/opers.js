var comPath = "/components/com_rbo/";
var oper;

//===================================================================================
function rbOper(o) {

    this.oCust = new rboCust();
    this.tips = o.tips;
    this.allFields = o.allFields;

    this.arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
    this.arFoundProducts = new Array();
    this.bCustInput = 'select';
    this.oSavedData = {
        "rbo_opers": {}
    };
}

//===================================================================================
rbOper.prototype.attachOperModule = function () {
    var self = this;
    //подключаем форму для редакции документов
    $("#oper-form").dialog({
        autoOpen: false,
        height: 450,
        width: 900,
        modal: true,
        resizable: true
    });

    self.oTable = $('#TableOper').dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": comPath + "ajax.php?task=get_oper_list",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": 'json',
                "type": "POST",
                "data": aoData,
                "url": sSource,
                "success": function (json) {
                    fnCallback(json);
                }
            });
        },
        "aoColumns": [{
            "sTitle": "Ключ",
            "sClass": "center",
            "mData": function (source, type, val) {
                return "<a href='javascript:oper.readOper(" + source.operId + ")'>#" + source.operId + "</a>";
            }
        }, {
            "sTitle": "Дата",
            "mData": "oper_date"
        }, {
            "sTitle": "Операция",
            "mData": "oper_type"
        }, {
            "sTitle": "Покупатель",
            "mData": "cust_name"
        }, {
            "sTitle": "Сумма",
            "sClass": "center",
            "mData": "oper_sum"
        }, {
            "sTitle": "Товар/услуга",
            "sClass": "left",
            "mData": "product_name"
        }, {
            "sTitle": "Фирма",
            "sClass": "center",
            "mData": "oper_firm"
        }, {
            "sTitle": "Менеджер",
            "mData": "oper_manager"
        }],
        "oLanguage": {
            "sProcessing": "Подождите...",
            "sLengthMenu": "Показать _MENU_ строк",
            "sZeroRecords": "Записи отсутствуют.",
            "sInfo": "Операции с _START_ по _END_ (всего: _TOTAL_)",
            "sInfoEmpty": "Операций нет",
            "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
            "sInfoPostFix": "",
            "sSearch": "Поиск:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "В начало",
                "sPrevious": "Предыдущие",
                "sNext": "Следующие",
                "sLast": "В конец"
            }
        }
    });

    $("#header_doclist_choose_list h2").html("Операции");

    //подключаем меню перехода в другой раздел
    $("#links").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    //обработчик нажатия кнопки добавления документа
    $("#oper_add_btn").click(function (event) {
        self.createOper();
        return false;
    });

    $("#prod_search_btn").click(function (event) {
        self.productSearch();
        return false;
    });

    $("#prod_search_off_btn").click(function (event) {
        self.productSearchOff();
        return false;
    });

    //навешиваем обработчик при выборе товара из списка найденных
    $("#rbo_opers\\.product_name").change(function (event) {
        self.setProductPrice();
        return false;
    });

    //навешиваем обработчик при изменении цены на товар
    $("#rbo_opers\\.product_price").change(function (event) {
        self.calcSum();
        return false;
    });

    //навешиваем обработчик при изменении количества товара
    $("#rbo_opers\\.product_cnt").change(function (event) {
        self.calcSum();
        return false;
    });

    $("#rbo_opers\\.oper_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

}

//===================================================================================
rbOper.prototype.setRW = function (oData) {
    return false;
}

//===================================================================================
rbOper.prototype.readOper = function (operId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_opers": {
                "operId": operId
            }
        },
        url: comPath + "ajax.php?task=oper_read",
        success: function (oper_data) {
            self.showOperForm(oper_data);
        }
    });
}

//===================================================================================
rbOper.prototype.saveOper = function () {
    var self = this;
    var bValid = true;
    self.oSavedData = getFormData("oper-form", "rbo_opers");
    self.allFields.removeClass("ui-state-error");
    bValid = bValid && checkNotEmpty($("#rbo_opers\\.oper_date"), "Дата", self.tips);
    bValid = bValid && checkNotEmpty($("#rbo_opers\\.oper_type"), "Тип операции", self.tips);
    bValid = bValid && checkNotEmpty($("#rbo_opers\\.oper_firm"), "Фирма", self.tips);
    bValid = bValid && checkNotEmpty($("#rbo_opers\\.oper_manager"), "Менеджер", self.tips);
    if (!bValid)
        return;

    if (self.oCust.flds.cust_name == "")
        $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

    self.oSavedData["rbo_opers"]["custId"] = self.oCust.flds.custId;
    self.oSavedData["rbo_opers"]["oper_cust"] = self.oCust.flds;

    var taskCmd = "oper_create";
    if (!IsNull(self.oSavedData.rbo_opers.operId) && self.oSavedData.rbo_opers.operId > 0)
        taskCmd = "oper_update";
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: self.oSavedData,
        url: comPath + "ajax.php?task=" + taskCmd,
        success: function (doc_data) {
            $("#oper-form").dialog("close");
            self.oTable.fnDraw();
        }
    });
}

//===================================================================================
rbOper.prototype.createOper = function () {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        url: comPath + "ajax.php?task=get_current_date",
        success: function (p) {
            self.oSavedData["rbo_opers"]["oper_date"] = p.new_date;
            self.oSavedData["rbo_opers"]["productId"] = "";
            self.oSavedData["rbo_opers"]["product_code"] = "";
            self.oSavedData["rbo_opers"]["product_price"] = "";
            self.oSavedData["rbo_opers"]["product_cnt"] = "";
            self.oSavedData["rbo_opers"]["oper_sum"] = "";
            self.oSavedData["rbo_opers"]["operId"] = "";
            self.showOperForm(self.oSavedData.rbo_opers);
        }
    });
}

// ===================================================================================
rbOper.prototype.deleteOper = function (operId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_opers": {
                "operId": operId
            }
        },
        url: comPath + "ajax.php?task=oper_delete",
        success: function (oper_data) {
            self.oTable.fnDraw();
        }
    });

    $("#oper-form").dialog("close");
}

// ===================================================================================
rbOper.prototype.showOperForm = function (i) {
    var self = this;

    refillSelect("rbo_opers\\.oper_manager", getPeopleList());
    refillSelect("rbo_opers\\.oper_firm", getFirmList());
    refillSelect("rbo_opers\\.oper_type", getOperTypeList());

    setFormData("oper-form", "rbo_opers", i);

    var pId = $('#rbo_opers\\.productId').val();
    if (!(pId > 0)) self.productSearchOff();

    //установим поля контрагента
    self.oCust.setCustFlds('saved', i.oper_cust);

    var readOnly = this.setRW(i);

    var oBtns = {};
    if (!readOnly) {
        oBtns["Удалить"] = function () {
            Ask("Операция будет удалена. Продолжить?", "Удалить операцию", "Отмена", function () {
                self.deleteOper(i.operId);
                $("#oper-form").dialog("close");
            }, null, "#dialog-confirm");
        }
    }

    oBtns["Сохранить"] = function () {
        self.saveOper();
    };

    oBtns["Отмена"] = function () {
        $("#oper-form").dialog("close");
    };

    $("#oper-form").dialog({
        title: NullTo(i.oper_type, "") + " #" + NullTo(i.operId, "новая"),
        buttons: oBtns
    });

    $("#oper-form").dialog("open");
}

//===================================================================================
rbOper.prototype.productSearch = function () {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "search": $("#prod_search").val()
        },
        url: comPath + "ajax.php?task=product_search",
        success: function (p) {
            var oProd = {};
            self.arFoundProducts = new Array();
            for (var i = 0; i < p.result.length; i++) {
                oProd[i] = p.result[i].product_name;
                self.arFoundProducts[i] = p.result[i];
            }
            refillSelect("rbo_opers\\.product_name", oProd);
            self.setProductPrice();
            if (p.count > p.result.length) {
                $('#rbo_opers\\.product_name').append('<option value="-1">=== Найдено позиций:' + p.count + ' (уточните поиск)</option>');
            }

        }
    });
}

//===================================================================================
rbOper.prototype.productSearchOff = function () {
    $('#rbo_opers\\.product_name option').remove();
    $('#rbo_opers\\.productId').val("");
    $('#rbo_opers\\.product_code').val("");
    $('#rbo_opers\\.product_price').val("");
    $('#rbo_opers\\.product_cnt').val("");
    $('#rbo_opers\\.oper_sum').val("");
}

// ===================================================================================
rbOper.prototype.setProductPrice = function () {
    var self = this;
    var oVal = $("#rbo_opers\\.product_name option:selected").val();
    $("#rbo_opers\\.productId").val(self.arFoundProducts[oVal].productId);
    $("#rbo_opers\\.product_price").val(self.arFoundProducts[oVal].product_price);
    $("#rbo_opers\\.product_code").val(self.arFoundProducts[oVal].product_code);
    $("#rbo_opers\\.product_cnt").val(1);
    $("#prod_price1").html("Цена Опт.1= " + NullTo(self.arFoundProducts[oVal].product_price1, 0) + "р. Остаток на складе=" + NullTo(self.arFoundProducts[oVal].product_in_stock, 0));
    this.calcSum();
}

//===================================================================================
rbOper.prototype.calcSum = function () {
    $("#rbo_opers\\.oper_sum").val($("#rbo_opers\\.product_price").val() * $("#rbo_opers\\.product_cnt").val());
}

// ===================================================================================
$(document).ready(function () {

    oper = new rbOper({
        allFields : $("#rbo_opers\\.oper_date").add($("#rbo_opers\\.oper_type")).add($("#rbo_opers\\.oper_firm")).add($("#rbo_opers\\.oper_manager")),
        tips : $(".validateTips")});
    oper.attachOperModule();
    oper.oCust.attachCustomerModule();

});
