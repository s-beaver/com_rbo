'use strict';
var prd;

//===================================================================================
function PriceImport(o) {
}

//===================================================================================
PriceImport.prototype.attachProductModule = function () {
    var self = this;

    //подключаем форму для редакции документов
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
            "data": "product_price"
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
    $("#import_open_csv").button();
    $("#import_open_settings").button();
    $("#import_start").button();
    $("#import_save_changes").button();
    $("#import_cancel_changes").button();
    //обработчик нажатия кнопки добавления товара
    // $("#doc_add_btn").click(function (event) {
    //     self.createProduct();
    //     return false;
    // });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

};

//===================================================================================
PriceImport.prototype.readProduct = function (id) {
    var self = this;
};


// ===================================================================================
$(document).ready(function () {

    prd = new PriceImport({
        checkFields: ["product_price", "product_price1"],
        tips: $(".validateTips")
    });
    prd.attachProductModule();

});
