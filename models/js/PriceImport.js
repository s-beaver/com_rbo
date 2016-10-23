'use strict';
var prd;

//===================================================================================
function PriceImport(o) {
    this.loadingCSV = false;
    this.fileReader = new FileReader();
}

//===================================================================================
PriceImport.prototype.attachDocForm = function () {
    var self = this;
    this.oFormLoadDlg = $("#priceimport\\.load-form");
    this.oFormLoadDlg.dialog({
        autoOpen: false,
        height: 200,
        width: 400,
        modal: true,
        resizable: true
    });

    this.oFormINIDlg = $("#priceimport\\.load-ini");
    this.oFormINIDlg.dialog({
        autoOpen: false,
        height: 600,
        width: 700,
        modal: true,
        resizable: true
    });

};

//===================================================================================
PriceImport.prototype.attachProductModule = function () {
    var self = this;
    self.attachDocForm();

    self.oTable = $('#TableProduct').dataTable({
        "jQueryUI": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [50, 100, 200],
        "ajax": {
            "type": "POST",
            "url": comPath + "ajax.php?task=get_price_import_list"
        },
        "columns": [{
            "title": "Ключ",
            "className": "center",
            "data": function (source, type, val) {
                return "<a href='javascript:prd.readProduct(" + source.id + ")'>#" + source.id + "</a>";
            }
        }, {
            "title": "Наименование",
            "data": "product_name"
        }, {
            "title": "Код",
            "data": "product_code"
        }, {
            "title": "Цена",
            "className": "center",
            "data": function (source, type, val) {
                var v = source.product_price;
                if (IsNull(v)) return "";
                if (/\d+\s\(-.+\)/.test(v)) {
                    return "<div style='background-color: green'>" + source.product_price + "</div>";
                }
                if (/\d+\s\([^-].+/.test(v)) {
                    return "<div style='background-color: red'>" + source.product_price + "</div>";
                }
                return source.product_price;
            }
        }, {
            "title": "Цена 1",
            "className": "center",
            "data": "product_price1"
        }, {
            "title": "Цена 2",
            "className": "center",
            "data": "product_price2"
        }, {
            "title": "Цена 3",
            "className": "center",
            "data": "product_price3"
        }, {
            "title": "Цена vip",
            "className": "center",
            "data": "product_price_vip"
        }, {
            "title": "fId",
            "className": "center",
            "data": "productFoundId"
        }, {
            "title": "fCnt",
            "className": "center",
            "data": "productFoundCount"
        }, {
            "title": "*",
            "className": "center",
            "data": "imported"
        }],
        language: dataTablesLanguage
    });
    self.oTableAPI = self.oTable.api();

    $("#header_doclist_choose_list h2").html("Импорт прайсов");

    //подключаем меню перехода в другой раздел
    $("#links").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    $("#header_doclist_adddoc").html("");
    $("#import_open_settings").button();
    $("#import_open_csv").button();
    $("#import_start").button();
    $("#import_save_changes").button();
    $("#import_cancel_changes").button();

    //обработчик нажатия кнопки открытия ini файла
    $("#import_open_settings").click(function (event) {
        self.editINI();
        return false;
    });

    $("#priceimport_ini_file").change(function (event) {
        self.readINI();
        return false;
    });

    //обработчик нажатия кнопки открытия csv файла
    $("#import_open_csv").click(function (event) {
        self.openCSV();
        return false;
    });
    //обработчик нажатия кнопки открытия csv файла
    $("#import_start").click(function (event) {
        self.importPrice();
        return false;
    });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#progressbar").hide();

};

//===================================================================================
PriceImport.prototype.readProduct = function (id) {
    var self = this;
};


//===================================================================================
PriceImport.prototype.showINIForm = function () {
    var self = this;
    var oBtns = {};

    oBtns["Сохранить настройки"] = function () {
        $.ajax({
            url: comPath + "ajax.php?task=import_save_ini",
            dataType: 'json',
            type: "POST",
            data: {
                "iniFileContent": $("#priceimport\\.settings").val()
            },
            success: function (p) {
                if (!IsNull(p) && !IsNull(p.error)) {
                    alert("Статус: " + NullTo(p.error.code, "") + " Ошибка: " + NullTo(p.error.message, ""));
                }
                self.oFormINIDlg.dialog("close");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Статус: " + textStatus + " Ошибка: " + errorThrown);
            }
        });
    };

    oBtns["Отмена"] = function () {
        self.oFormINIDlg.dialog("close");
    };

    self.oFormINIDlg.dialog({
        title: "Настройка импорта (ini)",
        buttons: oBtns
    });

    self.oFormINIDlg.dialog("open");
};

//===================================================================================
PriceImport.prototype.editINI = function () {
    var self = this;
    self.fileReader.onload = function (e) {
        $("#priceimport\\.settings").val(e.target.result);
    };

    $.ajax({
        url: comPath + "ajax.php?task=import_read_ini",
        dataType: 'json',
        type: "POST",
        data: {},
        success: function (p) {
            if (IsNull(p)) return;
            if (IsNull(p.lines)) return;
            if (!IsArray(p.lines)) return;
            var iniFromServer = "";
            for (var i = 0; i < p.lines.length; i++) {
                iniFromServer += p.lines[i];
            }
            $("#priceimport\\.settings").val(iniFromServer);
            self.showINIForm();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            self.showINIForm();
        }
    });

};

//===================================================================================
PriceImport.prototype.readINI = function () {
    var self = this;
    self.fileReader.readAsText($('#priceimport_ini_file').get(0).files[0]);
};

//===================================================================================
PriceImport.prototype.openCSV = function () {
    var self = this;
    var oBtns = {};

    if (self.loadingCSV) return;
    oBtns["Открыть"] = function () {
        var formData = new FormData();
        formData.append('import_csv', $('#priceimport_file').get(0).files[0]);

        $("#progressbar").show();
        self.loadingCSV = true;
        $.ajax({
            url: comPath + "ajax.php?task=import_open_csv",
            type: 'POST',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success: function (data) {
                $("#progressbar").hide();
                self.oFormLoadDlg.dialog("close");
                self.oTableAPI.draw();
                self.loadingCSV = false;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#progressbar").hide();
                alert("Статус: " + textStatus + " Ошибка: " + errorThrown);
                self.loadingCSV = false;
            }

        });
    };

    oBtns["Отмена"] = function () {
        self.oFormLoadDlg.dialog("close");
    };

    self.oFormLoadDlg.dialog({
        title: "Открыть прайс (csv)",
        buttons: oBtns
    });

    self.oFormLoadDlg.dialog("open");
};

//===================================================================================
PriceImport.prototype.importPrice = function () {
    var self = this;

    if (self.loadingCSV) return;
    self.loadingCSV = true;
    $.ajax({
        url: comPath + "ajax.php?task=import_import_price",
        type: 'POST',
        contentType: false,  // tell jQuery not to set contentType
        success: function (data) {
            $("#progressbar").hide();
            self.oTableAPI.draw();
            self.loadingCSV = false;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#progressbar").hide();
            alert("Статус: " + textStatus + " Ошибка: " + errorThrown);
            self.oTableAPI.draw();
            self.loadingCSV = false;
        }

    });
};

// ===================================================================================
$(document).ready(function () {

    prd = new PriceImport({
        checkFields: ["product_price", "product_price1"],
        tips: $(".validateTips")
    });
    prd.attachProductModule();

});
