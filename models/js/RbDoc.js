/**
 * Базовый класс JavaScript для работы с документами. Предназначен для
 * наследования
 *
 * Необходимо: - убрать comPath - например передовать из php при создании
 * объекта - четче привязать методы к полям объекта, может быть убрать передачу
 * ключа документа через параметр ф-ции (брать из объекта)
 */

//===================================================================================
function RbDoc(o) {
    this.docId = 0;
    this.docFormPrefix = o.docFormPrefix;
    this.sDocType = o.sDocType;
    this.sDocTypeTitle = o.sDocTypeTitle;
    this.sDocTypeListTitle = o.sDocTypeListTitle;
    if (IsArray(o.checkFields)) {
        for (var i = 0; i < o.checkFields.length; i++) {
            if (IsNull(this.checkFields)) {
                this.checkFields = $("#" + this.docFormPrefix + "\\." + o.checkFields[i]);
            } else {
                this.checkFields = this.checkFields.add($("#" + this.docFormPrefix + "\\." + o.checkFields[i]));
            }
        }
    }
    this.tips = o.tips;
    this.printList = o.printList;//перечень печатных форм документа
    this.copyToList = o.copyToList;//перечень документов для создания из текущего документа

    this.oTable = o.oTable;
    if (!IsNull(this.oTable)) {
        this.oTableAPI = this.oTable.api();
    }
    this.oProduct = new RboProduct();//объект для выбора/редакции товарами
    this.oCust = new RboCust(this);//объект для выбора/редакции контрагента

    this.editing_lineNo = 0;
    this.lines_before_update = 0;

    this.oStatusList = o.statusList;

    this.attachDocForm();
    this.oProduct.attachProductModule();
    this.oCust.attachCustomerModule();
}

//===================================================================================
RbDoc.prototype.attachDocForm = function () {
    var self = this;
    //подключаем форму для редакции документов
    this.oFormDlg = $("#" + self.docFormPrefix + "\\.doc-form");
    this.oFormDlg.dialog({
        autoOpen: false,
        height: 600,
        width: 900,
        modal: true,
        resizable: true
    });

    this.oTableProducts = $('#' + self.docFormPrefix + "\\.products-table").dataTable({
        "retrieve": true,
        "paging": false,
        "searching": false,
        "ordering": false,
        "autoWidth": true,
        "columns": [{
            "title": "Код",
            "data": "product_code",
            "width": "5%"
        }, {
            "title": "Наименование",
            "data": "product_name"
        }, {
            "title": "Цена",
            "className": "center",
            "data": "product_price"
        }, {
            "title": "К-во",
            "className": "center",
            "data": "product_cnt"
        }, {
            "title": "Сумма",
            "className": "center",
            "data": "oper_sum"
        }, {
            "title": "Ред.",
            "className": "center",
            "data": function (source, type, val) {
                return "<a id='" + self.docFormPrefix + ".edit_product' href='javascript:" + self.docFormPrefix + ".showProductForm(" + source.lineNo + ")'><img src='" + comPath + "images/icon-32-edit-on.png'/></a>";
            }
        }],
        "language": dataTablesLanguage
    });

    this.apiTableProducts = this.oTableProducts.api();

};

//===================================================================================
RbDoc.prototype.attachPageElements = function () {
    var self = this;
    this.oTable = $('#TableDoc').dataTable({
        "jQueryUI": true,
        "processing": true,
        "serverSide": true,
        "lengthMenu": [50, 100, 200],
        "ajax": {
            type: "POST",
            url: comPath + "ajax.php?task=get_doc_list&doc_type=" + self.sDocType
        },
        "columns": [{
            "title": "Номер",
            "className": "center",
            "data": function (source, type, val) {
                return "<a href='javascript:" + self.docFormPrefix + ".readDoc(" + source.docId + ")'>" + source.doc_num + " /" + source.doc_date + "</a>";
            }
        }, {
            "title": "Контрагент",
            "data": "doc_cust"
        }, {
            "title": "Сумма",
            "className": "center",
            "data": "doc_sum"
        }, {
            "title": "Фирма",
            "className": "center",
            "data": "doc_firm"
        }, {
            "title": "Статус",
            "className": "center",
            "data": function (source, type, val) {//"doc_status"
                var status = NullTo(source.doc_status,"");
                if (status!="удален" && !IsEmpty(source.pay_date) && source.pay_date!='0000-00-00') status = "оплачен";
                return status;
            }
        }, {
            "title": "Док-ты",
            "data": function (source, type, val) {
                var s = "", docText, elem;
                s += "<div style='display: table-cell; vertical-align: middle'><a href='javascript:" + self.docFormPrefix + ".copyDoc(" + source.docId + ")'><img src='" + comPath + "images/icon-16-new.png'/></a></div>";
                s += "<div style='display: table-cell'>";
                for (var i = 0; i < source.childs.length; i++) {
                    elem = source.childs[i];
                    docText = elem.doc_type + " №" + elem.doc_num + " /" + elem.doc_date;
                    switch (elem.doc_type) {
                        case "акт":
                        {
                            s += "<a href='javascript:s_act.readDoc(" + elem.docId + ")'>" + docText + "</a><br>";
                            break;
                        }
                        case "накл":
                        {
                            s += "<a href='javascript:s_bill.readDoc(" + elem.docId + ")'>" + docText + "</a><br>";
                            break;
                        }
                        default:
                        {
                            s += docText + "<br>";
                        }
                    }

                }
                s += "</div>";
                return s;
            }
        }, {
            "title": "Прим.",
            "data": "doc_rem"
        }, {
            "title": "Менеджер",
            "data": "doc_manager"
        }],
        "language": dataTablesLanguage
    });
    this.oTableAPI = this.oTable.api();

    $("#header_doclist_choose_list h2").html(this.sDocTypeListTitle);

    //подключаем меню перехода в другой раздел
    $("#links").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    //обработчик нажатия кнопки добавления документа
    $("#doc_add_btn").click(function (event) {
        self.createDoc();
        return false;
    });

    //обработчик нажатия кнопки добавления документа
    $("#"+self.docFormPrefix+"\\.doc_status").change(function (event) {
        self.setRW($("#"+self.docFormPrefix+"\\.doc_status").val());
        return false;
    });

};

// ===================================================================================
RbDoc.prototype.setRW = function (sStatus) {//todo проверять из statusList
    var self = this;
    if (sStatus == "выставлен" || sStatus == "оплачен" || sStatus == "удален" || sStatus == "подписан") {
        this.checkFields.attr("disabled", "disabled");
        $("[id^='" + self.docFormPrefix + "\\.edit_product']").each(function (x, elem) {
            $(this).attr("href", "javascript:;");
        });
        $("#" + self.docFormPrefix + "\\.prod_add_btn").off("click");
        $("#" + self.docFormPrefix + "\\.prod_add_btn").click(function (event) {
            return false;
        });
        return true;
    } else {
        this.checkFields.removeAttr("disabled");
        var i=0;
        $("[id^='" + self.docFormPrefix + "\\.edit_product']").each(function (x, elem) {
            $(this).attr("href", "javascript:" + self.docFormPrefix + ".showProductForm(" + i + ")");
            i++;
        });
        //обработчик нажатия кнопки добавления товара в документ
        $("#" + self.docFormPrefix + "\\.prod_add_btn").off("click");
        $("#" + self.docFormPrefix + "\\.prod_add_btn").click(function (event) {
            self.showProductForm();
            return false;
        });
        return false;
    }
};

// ===================================================================================
RbDoc.prototype.readDocFromURLId = function () {
    var docId = getURLParam("docId");
    if (Number(docId) > 0) {
        this.readDoc(docId);
    }
};

// ===================================================================================
RbDoc.prototype.readDoc = function (docId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "docId": docId,
                "doc_type": this.sDocType
            }
        },
        url: comPath + "ajax.php?task=doc_read",
        success: function (doc_data) {
            if (doc_data.errorCode > 0) {
                Msg(doc_data.errorMsg, "Ок", null, "#dialog-confirm", "Ошибка");
            } else {
                self.showDocForm(doc_data);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert("Статус: " + textStatus + " Ошибка: " + errorThrown)
        }
    });
};

// ===================================================================================
RbDoc.prototype.getDocDataBeforeSave = function (docId) {
    var self = this;
    var p = self.apiTableProducts.rows().data();
    var pAr = [];
    for (var i = 0; i < p.length; i++)
        pAr[i] = p[i];

    if (self.oCust.flds.cust_name == "")
        $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

    var oData = getFormData(self.docFormPrefix + "\\.doc-form", self.docFormPrefix, "rbo_docs");
    oData.rbo_docs.docId = docId;
    oData.rbo_docs.doc_type = self.sDocType;
    oData.rbo_docs.doc_base = $("#doc_baseId").val();// скрытое поле в форме выбора документа - основания
    oData.rbo_docs.custId = $("#custId").val();// скрытое поле в форме выбора клиента
    oData.rbo_docs.doc_cust = self.oCust.flds;
    oData.rbo_docs.doc_products = pAr;
    return oData;
};

// ===================================================================================
RbDoc.prototype.saveDoc = function (docId) {
    var self = this;
    var bValid = true;
    self.checkFields.removeClass("ui-state-error");
    //bValid = bValid && checkNotEmpty($("#"+self.docFormPrefix+"\\.doc_num"), "Номер", self.tips);
    //bValid = bValid && checkNotEmpty($("#"+self.docFormPrefix+"\\.doc_date"), "Дата", self.tips);
    bValid = bValid && checkNotEmpty($("#" + self.docFormPrefix + "\\.doc_manager"), "Менеджер", self.tips);
    if (!bValid)
        return;

    var oData = self.getDocDataBeforeSave(docId);

    var taskCmd = "doc_create";
    if (!IsNull(docId) && docId > 0)
        taskCmd = "doc_update";
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: oData,
        url: comPath + "ajax.php?task=" + taskCmd,
        success: function (doc_data) {
            if (doc_data.errorCode) {
                Msg(doc_data.errorMsg, "Ок", null, "#dialog-confirm", "Ошибка");
            } else {
                self.oFormDlg.dialog("close");
                self.oTableAPI.draw();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {//сюда приводит throw exception в php
            alert("Статус: " + textStatus + " Ошибка: " + errorThrown)
        }
    });
};

// ===================================================================================
RbDoc.prototype.copyDoc = function (docId) {
    var self = this;
    var sText = "";

    if (!IsNull(self.copyToList) && self.copyToList.length > 0) {
        for (var x = 0; x < self.copyToList.length; x++) {
            sText += "<p><input " + (x == 0 ? "checked" : "") + " name='copyDocByChoose' id='copyDocByChoose" + x + "' type='radio' value='" + x + "'>" + self.copyToList[x].title + "</p>";
        }
    } else return;

    Ask(sText, "Создать документ", "Отмена", function () {
        var x = Number($(":radio[name=copyDocByChoose]").filter(":checked").val());
        var docType = self.copyToList[x].docType;
        var viewName = self.copyToList[x].viewName;

        //todo проверить, есть ли уже документ, основание которого равно docId
        $.ajax({
            dataType: 'json',
            type: "POST",
            data: {
                "rbo_docs": {
                    "doc_type": docType,
                    "doc_base": docId
                }
            },
            url: comPath + "ajax.php?task=doc_copy",
            success: function (doc_data) {
                if (doc_data.errorCode > 0) {
                    Msg(doc_data.errorMsg, "Ок", null, "#dialog-confirm", "Ошибка");
                } else {
                    self.oTableAPI.draw();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Статус: " + textStatus + " Ошибка: " + errorThrown)
            }
        });

    }, null, "#dialog-confirm");

};

// ===================================================================================
RbDoc.prototype.createDoc = function () {
    this.showDocForm({});
};

// ===================================================================================
RbDoc.prototype.deleteDoc = function (docId) {
    var self = this;
    $("#" + self.docFormPrefix + "\\.doc_status").val("удален");
    self.saveDoc(docId);
};

// ===================================================================================
RbDoc.prototype.showDocForm = function (doc_data) {
    var self = this;
    self.checkFields.removeClass("ui-state-error");
    refillSelect(self.docFormPrefix + "\\.doc_manager", getPeopleList(), true);
    refillSelect(self.docFormPrefix + "\\.doc_firm", getFirmList());
    refillSelect(self.docFormPrefix + "\\.doc_status", self.oStatusList, true);

    self.docId = doc_data.docId;

    //установим базовые реквизиты документа
    setFormData(self.docFormPrefix + "\\.doc-form", self.docFormPrefix, doc_data);

    /*$("#" + self.docFormPrefix + "\\.doc_num").val(doc_data.doc_num);
    $("#" + self.docFormPrefix + "\\.doc_date").val(doc_data.doc_date);
    $("#" + self.docFormPrefix + "\\.pay_date").val(doc_data.pay_date);
    $("#" + self.docFormPrefix + "\\.doc_sum").val(doc_data.doc_sum);
    $("#" + self.docFormPrefix + "\\.doc_status").val(doc_data.doc_status);*/

    //установим поля документа-основания
    var sDocBase = "";
    $("#doc_baseId").val(doc_data.doc_base);
    if ((doc_data.doc_base > 0) && !IsNull(doc_data.doc_base_doc)) {
        sDocBase = "Счет №" + doc_data.doc_base_doc.doc_num + " от " + doc_data.doc_base_doc.doc_date;
    }
    $("#" + self.docFormPrefix + "\\.doc_base").val(sDocBase);

    //укажем менеджера
    $('#' + self.docFormPrefix + '\\.doc_manager option:selected').each(function () {
        this.selected = false;
    });
    if (!IsEmpty(doc_data.doc_manager))
        $("#" + self.docFormPrefix + "\\.doc_manager option:contains('" + doc_data.doc_manager + "')").prop("selected", "selected");

    //установим поля контрагента
    self.oCust.setCustFlds('saved', doc_data.doc_cust);

    //установим фирму
    if (!IsNull(doc_data.doc_firm))
        $("#" + self.docFormPrefix + "\\.doc_firm option:contains('" + doc_data.doc_firm + "')").prop("selected", "selected");
    $("#" + self.docFormPrefix + "\\.doc_rem").val(doc_data.doc_rem);

    //заполним список товаров/услуг
    self.apiTableProducts.clear();
    var x;
    if (!IsNull(doc_data.doc_products) && doc_data.doc_products.length > 0) {
        for (x = 0; x < doc_data.doc_products.length; x++)
            doc_data.doc_products[x].lineNo = x;
        self.apiTableProducts.rows.add(doc_data.doc_products);
    }
    self.apiTableProducts.columns.adjust().draw();

    //установим документ в правильное состояние RW
    var readOnly = this.setRW(doc_data.doc_status);

    var oBtns = {};

    if (!readOnly) {
        oBtns["Удалить"] = function () {
            Ask("Документ будет удален. Продолжить?", "Удалить документ", "Отмена", function () {
                self.deleteDoc(doc_data.docId);
            }, null, "#dialog-confirm");
        }
    }

    if (!IsNull(self.printList) && self.printList.length > 0) {
        for (x = 0; x < self.printList.length; x++) {
            oBtns[self.printList[x].title] = function (event) {
                self.showPrintView($(event.target).text(), doc_data.docId);
            };
        }
    }

    oBtns["Сохранить"] = function () {
        self.saveDoc(doc_data.docId);
    };

    oBtns["Отмена"] = function () {
        self.oFormDlg.dialog("close");
    };

    self.oFormDlg.dialog({
        title: self.sDocTypeTitle + " №" + $('#' + self.docFormPrefix + '\\.doc_num').val(),
        buttons: oBtns
    });

    $("#dlg_copy_list").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    self.oFormDlg.dialog("open");
};

//===================================================================================
RbDoc.prototype.showPrintView = function (title, docId) {
    var self = this;
    var viewName = "";
    if (!IsNull(self.printList) && self.printList.length > 0) {
        for (var x = 0; x < self.printList.length; x++) {
            if (self.printList[x].title == title) {
                viewName = self.printList[x].viewName;
                break;
            }
        }
    }
    if (viewName != "")
        window.open('index.php?option=com_rbo&view=' + viewName + '&format=raw&docid=' + docId, '_blank');
};

//===================================================================================
RbDoc.prototype.custSearch = function () {
    this.oCust.custSearch();
};

// ===================================================================================
RbDoc.prototype.showProductForm = function (x) {// x-номер редактируемой строки, x=null-добавляем
    var self = this;
    var p = {};

    if (IsNull(x)) {
        self.editing_lineNo = -1;
    } else {
        self.editing_lineNo = x;
        p = self.apiTableProducts.row(x).data();
    }
    self.lines_before_update = self.apiTableProducts.rows().data().length;

    self.oProduct.showProductForm({
        pData: p,
        fnDelete: function () {
            if (self.editing_lineNo >= 0) {
                self.apiTableProducts.row(self.editing_lineNo).remove().draw();
                var pAll = self.apiTableProducts.rows().data();
                var iSum = 0;
                for (var x = 0; x < pAll.length; x++) {
                    iSum += Number(pAll[x].oper_sum);
                }
                $('#' + self.docFormPrefix + '\\.doc_sum').val(iSum);
            }
        },

        fnSave: function (pObj) {
            if (self.editing_lineNo >= 0) {
                pObj.lineNo = self.editing_lineNo;
                self.apiTableProducts.row(pObj.lineNo).data(pObj).draw();
            } else {
                pObj.lineNo = self.lines_before_update;
                self.apiTableProducts.row.add(pObj).draw();
            }
            var pAll = self.apiTableProducts.rows().data();
            var iSum = 0;
            for (var x = 0; x < pAll.length; x++) {
                iSum += Number(pAll[x].oper_sum);
            }
            $('#' + self.docFormPrefix + '\\.doc_sum').val(iSum);
        }
    });

};

//===================================================================================
function RbDocSale(o) {
    RbDocSale.superclass.constructor.apply(this, arguments);

}
//===================================================================================
extendObject(RbDocSale, RbDoc);
//===================================================================================

// ===================================================================================
// RbDocSale.prototype.readDoc = function (docId) {//todo удалить? дублирует RbDoc?
//     var self = this;
//     $.ajax({
//         dataType: 'json',
//         type: "POST",
//         data: {
//             "rbo_docs": {
//                 "docId": docId,
//                 "doc_type": self.sDocType
//             }
//         },
//         url: comPath + "ajax.php?task=doc_read",
//         success: function (doc_data) {
//             if (doc_data.errorCode > 0) {
//                 Msg(doc_data.errorMsg, "Ок", null, "#dialog-confirm", "Ошибка");
//             } else {
//                 self.showDocForm(doc_data);
//             }
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             alert("Статус: " + textStatus + " Ошибка: " + errorThrown)
//         }
//
//     });
// };

