/**
 * Объект JavaScript для работы со справочником контрагентов
 */

var comPath = "/components/com_rbo/";
var cst;

//===================================================================================
function RefCustomers(o) {
    this.tips = o.tips;
    this.allFields = o.allFields;
}

//===================================================================================
RefCustomers.prototype.attachCustomerModule = function () {
    var self = this;
    //подключаем форму для редакции контрагентов
    $("#cst-form").dialog({
        autoOpen: false,
        height: 550,
        width: 750,
        modal: true,
        resizable: true
    });

    self.oTable = $('#TableCustomer').dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": comPath + "ajax.php?task=get_cust_list",
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
                return "<a href='javascript:cst.readCustomer(" + source.custId + ")'>#" + source.custId + "</a>";
            }
        }, {
            "sTitle": "Название",
            "mData": "cust_name"
        }, {
            "sTitle": "Название полное",
            "mData": "cust_fullname"
        }],
        "oLanguage": {
            "sProcessing": "Подождите...",
            "sLengthMenu": "Показать _MENU_ строк",
            "sZeroRecords": "Записи отсутствуют.",
            "sInfo": "Записи с _START_ по _END_ (всего: _TOTAL_)",
            "sInfoEmpty": "Записей нет",
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

    $("#header_doclist_choose_list h2").html("Справочник - контрагенты");

    //подключаем меню перехода в другой раздел
    $("#links").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    //обработчик нажатия кнопки добавления
    $("#doc_add_btn").click(function (event) {
        self.createCustomer();
        return false;
    });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

};

//===================================================================================
RefCustomers.prototype.setRW = function (oData) {
    return false;
};

//===================================================================================
RefCustomers.prototype.readCustomer = function (custId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_cust": {
                "custId": custId
            }
        },
        url: comPath + "ajax.php?task=cust_read",
        success: function (data) {
            self.showCustomerForm(data);
        }
    });
};

//===================================================================================
RefCustomers.prototype.saveCustomer = function () {
    var self = this;
    var oData = getFormData("cst-form", "rbo_cust");
    var cust_data = getFormData("cst-form", "cust_data");
    oData.rbo_cust.cust_data = JSON.stringify(cust_data.cust_data);

    var bValid = true;
    self.allFields.removeClass("ui-state-error");
    bValid = bValid && checkNotEmpty($("#rbo_cust\\.cust_name"), "Название", self.tips);
    bValid = bValid && checkNotEmpty($("#rbo_cust\\.cust_fullname"), "Название полное", self.tips);
    if (!bValid)
        return;

    var taskCmd = "cust_create";
    if (!IsNull(oData.rbo_cust.custId) && oData.rbo_cust.custId > 0)
        taskCmd = "cust_update";
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: oData,
        url: comPath + "ajax.php?task=" + taskCmd,
        success: function (data) {
            $("#cst-form").dialog("close");
            self.oTable.fnDraw();
        }
    });
};

//===================================================================================
RefCustomers.prototype.createCustomer = function () {
    var self = this;
    self.showCustomerForm({});
};

// ===================================================================================
RefCustomers.prototype.deleteCustomer = function (custId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_cust": {
                "custId": custId
            }
        },
        url: comPath + "ajax.php?task=cust_delete",
        success: function (data) {
            self.oTable.fnDraw();
        }
    });

    $("#cst-form").dialog("close");
};

// ===================================================================================
RefCustomers.prototype.showCustomerForm = function (i) {
    var self = this;

    setFormData("cst-form", "rbo_cust", i);
    setFormData("cst-form", "cust_data", JSON.parse(i.cust_data));

    var rbo_cust_data = i.cust_data;
    setFormData("cst-form", "rbo_cust_flds", rbo_cust_data);

    var readOnly = this.setRW(i);

    var oBtns = {};
    if (!readOnly) {
        oBtns["Удалить"] = function () {
            Ask("Контрагент будет удален. Продолжить?", "Удалить контрагента", "Отмена", function () {
                self.deleteCustomer(i.custId);
                $("#cst-form").dialog("close");
            }, null, "#dialog-confirm");
        }
    }

    oBtns["Сохранить"] = function () {
        self.saveCustomer();
    };

    oBtns["Отмена"] = function () {
        $("#cst-form").dialog("close");
    };

    $("#cst-form").dialog({
        title: "#" + NullTo(i.custId, "новая"),
        buttons: oBtns
    });

    $("#cst-form").dialog("open");
};

// ===================================================================================
$(document).ready(function () {

    cst = new RefCustomers({
        allFields: $("#rbo_cust\\.cust_name").add($("#rbo_cust\\.cust_fullname")),
        tips: $(".validateTips")
    });
    cst.attachCustomerModule();

});
