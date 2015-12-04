'use strict';
var oper;

//===================================================================================
function RbOper(o) {
    this.docFormPrefix = "rbo_opers";
    this.oCust = new RboCust(this);
    this.tips = o.tips;
    var prefixedFldName = "";
    if (IsArray(o.checkFields)) {
        for (var i = 0; i < o.checkFields.length; i++) {
            if (o.checkFields[i].substring(0, 2) == "\\.") {
                prefixedFldName = "#" + this.docFormPrefix + o.checkFields[i];
            } else {
                prefixedFldName = "#" + o.checkFields[i];
            }
            if (IsNull(this.checkFields)) {
                this.checkFields = $(prefixedFldName);
            } else {
                this.checkFields = this.checkFields.add(prefixedFldName);
            }
        }
    }
    this.oTable = null;
    this.oTableAPI = null;

    this.arSearchedCust = []; // массив объектов содержащих поля покупателя
    this.arFoundProducts = [];
    this.oSavedData = {
        "rbo_opers": {}
    };
    this.saleTotal = 0;
    this.purchTotal = 0;
    this.expTotals = 0;
}

//===================================================================================
RbOper.prototype.attachOperModule = function () {
    var self = this;
    //подключаем форму для редакции документов
    self.oFormDlg = $("#" + self.docFormPrefix + "\\.oper-form");
    self.oFormDlg.dialog({
        autoOpen: false,
        height: 450,
        width: 900,
        modal: true,
        resizable: true
    });

    self.oTable = $('#TableOper').dataTable({
        "jQueryUI": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [50, 100, 200],
        "dom": '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr"l<"oper_filter">fr>t<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"<"oper_totals">p>',
        "ajax": {
            "type": "POST",
            "url": comPath + "ajax.php?task=get_oper_list"
        },
        "columns": [{
            "title": "Ключ",
            "className": "center",
            "data": function (source, type, val) {
                return "<a href='javascript:oper.readOper(" + source.operId + ")'>#" + source.operId + "</a>";
            }
        }, {
            "title": "Дата",
            "data": "oper_date"
        }, {
            "title": "Операция",
            "data": "oper_type"
        }, {
            "title": "Покупатель",
            "data": "cust_name"
        }, {
            "title": "Сумма",
            "className": "center",
            "data": "oper_sum"
        }, {
            "title": "Товар/услуга",
            "data": "product_name"
        }, {
            "title": "Прим.",
            "data": "oper_rem"
        }, {
            "title": "Фирма",
            "className": "center",
            "data": "oper_firm"
        }, {
            "title": "Менеджер",
            "data": "oper_manager"
        }],
        language: dataTablesLanguage
    });
    self.oTableAPI = self.oTable.api();

    self.oTableAPI.on('draw.dt', function () {
        self.calcTotals();
        $("div.oper_totals").html("Продажа=" + self.saleTotal + " Закуп=" + self.purchTotal + " Затраты=" + self.expTotals);
    });

    //добавляем фильтры и их обработчики
    $("div.oper_filter").html('&nbsp;Укажите дату:&nbsp;<input type="text" id="oper_filter_date">&nbsp;Укажите тип:&nbsp;<select id="oper_filter_type"></select>');
    $("#oper_filter_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    }).change(function (event) {
        self.oTableAPI.ajax.url(comPath + "ajax.php?task=get_oper_list&date_filter=" + $("#oper_filter_date").val()+"&type_filter=" + $("#oper_filter_type").val()).load();
    });
    refillSelect("oper_filter_type", getOperTypeList(), true);
    $("#oper_filter_type").change(function (event) {
        self.oTableAPI.ajax.url(comPath + "ajax.php?task=get_oper_list&date_filter=" + $("#oper_filter_date").val()+"&type_filter=" + $("#oper_filter_type").val()).load();
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
    $("#doc_add_btn").click(function (event) {
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

};

//===================================================================================
RbOper.prototype.setRW = function (oData) {
    var self = this;
    if (IsNull(oData.docId)) {
        this.checkFields.removeAttr("disabled");
        return false;
    } else {
        this.checkFields.attr("disabled", "disabled");
        return true;
    }
};

//===================================================================================
RbOper.prototype.readOper = function (operId) {
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
};

//===================================================================================
RbOper.prototype.saveOper = function () {
    var self = this;
    var bValid = true;
    self.oSavedData = getFormData(self.docFormPrefix + "\\.oper-form", "rbo_opers");
    self.checkFields.removeClass("ui-state-error");
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
            self.oFormDlg.dialog("close");
            self.oTableAPI.draw();
        }
    });
};

//===================================================================================
RbOper.prototype.createOper = function () {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        url: comPath + "ajax.php?task=get_current_date",
        success: function (p) {
            var chosenDate = $("#oper_filter_date").val();
            if (!IsEmpty(chosenDate)) p.new_date = chosenDate;
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
};

// ===================================================================================
RbOper.prototype.deleteOper = function (operId) {
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
            self.oTableAPI.draw();
        }
    });

    self.oFormDlg.dialog("close");
};

// ===================================================================================
RbOper.prototype.showOperForm = function (oper_data) {
    var self = this;

    refillSelect("rbo_opers\\.oper_manager", getPeopleList());
    refillSelect("rbo_opers\\.oper_firm", getFirmList());
    refillSelect("rbo_opers\\.oper_type", getOperTypeList());

    setFormData(self.docFormPrefix + "\\.oper-form", "rbo_opers", oper_data);

    var pId = $('#rbo_opers\\.productId').val();
    if (!(pId > 0)) self.productSearchOff();

    //установим поля контрагента
    self.oCust.setCustFlds('saved', oper_data.oper_cust);

    var readOnly = this.setRW(oper_data);

    var oBtns = {};
    if (!readOnly) {
        oBtns["Удалить"] = function () {
            Ask("Операция будет удалена. Продолжить?", "Удалить операцию", "Отмена", function () {
                self.deleteOper(oper_data.operId);
                self.oFormDlg.dialog("close");
            }, null, "#dialog-confirm");
        }
    }

    if (!readOnly) {
        oBtns["Сохранить"] = function () {
            self.saveOper();
        };
    }

    oBtns["Отмена"] = function () {
        self.oFormDlg.dialog("close");
    };

    self.oFormDlg.dialog({
        title: NullTo(oper_data.oper_type, "") + " #" + NullTo(oper_data.operId, "новая"),
        buttons: oBtns
    });

    self.oFormDlg.dialog("open");
};

//===================================================================================
RbOper.prototype.productSearch = function () {
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
            self.arFoundProducts = [];
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
};

//===================================================================================
RbOper.prototype.productSearchOff = function () {
    $('#rbo_opers\\.product_name option').remove();
    $('#rbo_opers\\.productId').val("");
    $('#rbo_opers\\.product_code').val("");
    $('#rbo_opers\\.product_price').val("");
    $('#rbo_opers\\.product_cnt').val("");
    $('#rbo_opers\\.oper_sum').val("");
};

// ===================================================================================
RbOper.prototype.setProductPrice = function () {
    var self = this;
    var oVal = $("#rbo_opers\\.product_name option:selected").val();
    $("#rbo_opers\\.productId").val(self.arFoundProducts[oVal].productId);
    $("#rbo_opers\\.product_price").val(self.arFoundProducts[oVal].product_price);
    $("#rbo_opers\\.product_code").val(self.arFoundProducts[oVal].product_code);
    $("#rbo_opers\\.product_cnt").val(1);
    $("#prod_price1").html("Цена Опт.1= " + NullTo(self.arFoundProducts[oVal].product_price1, 0) + "р. Остаток на складе=" + NullTo(self.arFoundProducts[oVal].product_in_stock, 0));
    this.calcSum();
};

//===================================================================================
RbOper.prototype.calcSum = function () {
    $("#rbo_opers\\.oper_sum").val($("#rbo_opers\\.product_price").val() * $("#rbo_opers\\.product_cnt").val());
};

//===================================================================================
RbOper.prototype.calcTotals = function () {
    var self = this;
    var oData = self.oTableAPI.data();
    self.saleTotal = 0;
    self.purchTotal = 0;
    self.expTotals = 0;
    for (var i = 0; i < oData.length; i++) {
        if (oData[i].oper_type.indexOf("продажа") >= 0) {
            self.saleTotal += Number(oData[i].oper_sum);
        }
        if (oData[i].oper_type.indexOf("закуп") >= 0) {
            self.purchTotal += Number(oData[i].oper_sum);
        }
        if (oData[i].oper_type.indexOf("затраты") >= 0) {
            self.expTotals += Number(oData[i].oper_sum);
        }
    }
};

// ===================================================================================
$(document).ready(function () {

    oper = new RbOper({
        checkFields: ["prod_search_off_btn", "\\.oper_date", "\\.oper_type", "\\.oper_firm", "\\.oper_manager", "\\.oper_sum", "\\.oper_rem",
            "prod_search", "prod_search_btn", "\\.cedit",
            "\\.product_name", "\\.product_price", "\\.product_cnt"],
        tips: $(".validateTips")
    });
    oper.attachOperModule();
    oper.oCust.attachCustomerModule();

});
