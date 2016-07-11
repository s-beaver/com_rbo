/* Скрипт формы для работы с входящими актами*/
var d_cmp;

function RbDocDCmp(o) {
    RbDocDCmp.superclass.constructor.apply(this, arguments);
    this.product_to_decomplect = {};
}
extendObject(RbDocDCmp, RbDoc);

// ===================================================================================
RbDocDCmp.prototype.showDocForm = function (doc_data) {
    self = this;
    if (!IsNull(doc_data.doc_products) && doc_data.doc_products.length > 0) {
        for (x = 0; x < doc_data.doc_products.length; x++)
            if (doc_data.doc_products[x].product_cnt < 0) {
                self.product_to_decomplect = doc_data.doc_products[x];
                $("#product_to_decomplect").val(self.product_to_decomplect.product_name);
                doc_data.doc_products.splice(x,1);
                break;
            }
    }
    RbDoc.prototype.showDocForm.call(this, doc_data);
};

// ===================================================================================
RbDocDCmp.prototype.getDocDataBeforeSave = function (docId) {
    self = this;
    var oData = RbDoc.prototype.getDocDataBeforeSave.call(this, docId);
    oData.rbo_docs.custId = -1;//мы сознательно удаляем контрагента из документа
    self.product_to_decomplect.product_cnt = -1;
    oData.rbo_docs.doc_products.push(self.product_to_decomplect);
    return oData;
};

//===================================================================================
RbDocDCmp.prototype.editProductToDeComplect = function () {
    var self = this;

    self.oProduct.showProductForm({
        pData: self.product_to_decomplect,
        fnDelete: function () {
            self.product_to_decomplect = {};
            $("#product_to_decomplect").val("");
        },

        fnSave: function (pObj) {
            self.product_to_decomplect = pObj;
            $("#product_to_decomplect").val(self.product_to_decomplect.product_name);
        }
    });
};

// ===================================================================================
$(document).ready(function () {

    d_cmp = new RbDocDCmp({
        sDocType: 'D_CMP',
        docFormPrefix: "d_cmp",
        sDocTypeListTitle: 'Разбивки комплектов',
        sDocTypeTitle: 'Разбивка комплектов',
        checkFields: ["doc_num", "doc_date", "doc_manager", "doc_firm"],
        tips: $(".validateTips"),
        statusList: {
            "подписан": "подписан",
            "удален": "удален"
        }
    });
    d_cmp.attachPageElements();

    //обработчик нажатия кнопки выбора комплекта
    $("#d_cmp\\.pedit").click(function (event) {
        d_cmp.editProductToDeComplect();
        return false;
    });

    $("#dialog-confirm").dialog({
        autoOpen: false
    });

    $("#d_cmp\\.doc_date").datepicker({
        showButtonPanel: true,
        dateFormat: "dd.mm.yy"
    });

    d_cmp.readDocFromURLId();
});
