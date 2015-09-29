var comPath = "/components/com_rbo/";
var oper;

//===================================================================================
function rbOper(o) {

    this.oCust = new rboCust();
    this.tips = o.tips;
    this.allFields = o.allFields;
    this.oTable = null;
    this.oTableAPI = null;

    this.arSearchedCust = new Array(); // массив объектов содержащих поля покупателя
    this.arFoundProducts = new Array();
    this.bCustInput = 'select';
    this.oSavedData = {
        "rbo_opers": {}
    };
    this.saleTotal = 0;
    this.purchTotal = 0;
    this.expTotals = 0;

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
        jQueryUI: true,
        processing: true,
        serverSide: true,
        lengthMenu: [50, 100, 200],
        dom: '<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-tl ui-corner-tr"l<"oper_filter">fr>t<"fg-toolbar ui-toolbar ui-widget-header ui-helper-clearfix ui-corner-bl ui-corner-br"<"oper_totals">p>',
        ajax: {
            type: "POST",
            url: comPath + "ajax.php?task=get_oper_list"
        },
        columns: [{
            title: "Ключ",
            className: "center",
            data: function (source, type, val) {
                return "<a href='javascript:oper.readOper(" + source.operId + ")'>#" + source.operId + "</a>";
            }
        }, {
            title: "Дата",
            data: "oper_date"
        }, {
            title: "Операция",
            data: "oper_type"
        }, {
            title: "Покупатель",
            data: "cust_name"
        }, {
            title: "Сумма",
            className: "center",
            data: "oper_sum"
        }, {
            title: "Товар/услуга",
            className: "left",
            data: "product_name"
        }, {
            title: "Фирма",
            className: "center",
            data: "oper_firm"
        }, {
            title: "Менеджер",
            data: "oper_manager"
        }],
        language: {
            processing: "Подождите...",
            lengthMenu: "Показать _MENU_ строк",
            zeroRecords: "Записи отсутствуют.",
            info: "Операции с _START_ по _END_ (всего: _TOTAL_)",
            infoEmpty: "Операций нет",
            infoFiltered: "(отфильтровано из _MAX_ записей)",
            infoPostFix: "",
            search: "Поиск:",
            paginate: {
                first: "В начало",
                previous: "Предыдущие",
                next: "Следующие",
                last: "В конец"
            }
        }
    });
    self.oTableAPI = self.oTable.api();

    self.oTableAPI.on('draw.dt', function () {
        self.calcTotals();
        $("div.oper_totals").html("Продажа=" + self.saleTotal + " Закуп=" + self.purchTotal + " Затраты=" + self.expTotals);
    });

    $("div.oper_filter").html('&nbsp;Укажите дату:&nbsp;<input type="text" id="oper_filter_date">');
    $("#oper_filter_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    }).change(function (event) {
        self.oTableAPI.ajax.url(comPath + "ajax.php?task=get_oper_list&date_filter=" + $(this).val()).load();
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

//===================================================================================
rbOper.prototype.calcTotals = function () {
    var self = this;
    var oData = self.oTableAPI.data();
    self.saleTotal = 0, self.purchTotal = 0, self.expTotals = 0;
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
}

// ===================================================================================
$(document).ready(function () {

    oper = new rbOper({
        allFields: $("#rbo_opers\\.oper_date").add($("#rbo_opers\\.oper_type")).add($("#rbo_opers\\.oper_firm")).add($("#rbo_opers\\.oper_manager")),
        tips: $(".validateTips")
    });
    oper.attachOperModule();
    oper.oCust.attachCustomerModule();

});
