/**
 * Абстрактный объект JavaScript для работы с документами. Предназначен для
 * наследования
 *
 * Необходимо: - убрать comPath - например передовать из php при создании
 * объекта - четче привязать методы к полям объекта, может быть убрать передачу
 * ключа документа через параметр ф-ции (брать из объекта)
 */

var comPath = "/components/com_rbo/";
//===================================================================================
function RbDoc(o) {
    this.docId = 0;
    this.sDocType = o.sDocType;
    this.sDocTypeTitle = o.sDocTypeTitle;
    this.sDocTypeListTitle = o.sDocTypeListTitle;
    this.allFields = o.allFields;//перечень элементов jquery, предназначенных для ввода данных и проверяемых на обязательность заполнения
    this.tips = o.tips;
    this.printList = o.printList;//перечень печатных форм документа
    this.copyToList = o.copyToList;//перечень документов для создания из текущего документа

    this.oProduct = new RboProduct();//объект для выбора/редакции товарами
    this.oCust = new RboCust(this);//объект для выбора/редакции контрагента

    this.editing_lineNo = 0;
    this.lines_before_update = 0;

    this.oStatusList = o.statusList;
}

//===================================================================================
RbDoc.prototype.attachDocModule = function () {
    var self = this;
    //подключаем форму для редакции документов
    $("#doc-form").dialog({
        autoOpen: false,
        height: 550,
        width: 900,
        modal: true,
        resizable: true
    });

    this.oTable = $('#TableDoc').dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "tableTools": {
            "sSwfPath": "/swf/copy_csv_xls_pdf.swf"
        },
        "aaSorting": [[1, "desc"]],
        "sAjaxSource": comPath + "ajax.php?task=get_doc_list&doc_type=" + self.sDocType,
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
            "sTitle": "Номер",
            "sClass": "center",
            "mData": function (source, type, val) {
                return "<a href='javascript:doc.readDoc(" + source.docId + ")'>" + source.doc_num + " /" + source.doc_date + "</a>";
            }
        }, {
            "sTitle": "Контрагент",
            "mData": "doc_cust"
        }, {
            "sTitle": "Сумма",
            "sClass": "center",
            "mData": "doc_sum"
        }, {
            "sTitle": "Фирма",
            "sClass": "center",
            "mData": "doc_firm"
        }, {
            "sTitle": "Статус",
            "sClass": "center",
            "mData": "doc_status"
        }, {
            "sTitle": "Менеджер",
            "mData": "doc_manager"
        }],
        "oLanguage": {
            "sProcessing": "Подождите...",
            "sLengthMenu": "Показать _MENU_ строк",
            "sZeroRecords": "Записи отсутствуют.",
            "sInfo": "Документы с _START_ по _END_ (всего: _TOTAL_)",
            "sInfoEmpty": "Документов нет",
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

    this.oTableProducts = $('#TableProducts').dataTable({
        "bPaginate": false,
        "searching": false,
        "ordering": false,
        "aoColumns": [{
            "sTitle": "Код",
            "mData": "product_code"
        }, {
            "sTitle": "Наименование",
            "mData": "product_name"
        }, {
            "sTitle": "Цена",
            "sClass": "center",
            "mData": "product_price"
        }, {
            "sTitle": "К-во",
            "sClass": "center",
            "mData": "product_cnt"
        }, {
            "sTitle": "Сумма",
            "sClass": "center",
            "mData": "product_sum"
        }, {
            "sTitle": "Ред.",
            "sClass": "center",
            "mData": function (source, type, val) {
                return "<a id='edit_product' href='javascript:doc.showProductForm(" + source.lineNo + ")'>" + "<img src='" + comPath + "images/icon-32-edit-on.png'/></a>";
            }
        }],
        "oLanguage": {
            "sProcessing": "Подождите...",
            "sLengthMenu": "Показать _MENU_ строк",
            "sZeroRecords": "Записи отсутствуют.",
            "sInfo": "Строки с _START_ по _END_ (всего: _TOTAL_)",
            "sInfoEmpty": "Строк нет",
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

    this.apiTableProducts = this.oTableProducts.api();

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

    //обработчик нажатия кнопки добавления товара в документ
    $("#prod_add_btn").click(function (event) {
        self.showProductForm();
        return false;
    });

};

// ===================================================================================
RbDoc.prototype.setRW = function (sStatus) {
    if (sStatus == "выставлен" || sStatus == "оплачен" || sStatus == "удален" || sStatus == "подписан") {
        this.allFields.attr("disabled", "disabled");
        $("[id^='edit_product']").each(function (x, elem) {
            $(this).attr("href", "javascript:;");
        });
        return true;
    } else {
        this.allFields.removeAttr("disabled");
        return false;
    }
};

// ===================================================================================
RbDoc.prototype.readDocFromURLId = function () {
    var docId = getURLParam("docId");
    if (Number(docId)>0) {
        this.readDoc(docId);
    }
}

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
            self.showDocForm(doc_data);
        }
    });
};

// ===================================================================================
RbDoc.prototype.saveDoc = function (docId) {
    var self = this;
    var bValid = true;
    self.allFields.removeClass("ui-state-error");
    bValid = bValid && checkNotEmpty($("#doc_num"), "Номер", self.tips);
    bValid = bValid && checkNotEmpty($("#doc_date"), "Дата", self.tips);
    bValid = bValid && checkNotEmpty($("#doc_manager"), "Менеджер", self.tips);
    var p = self.apiTableProducts.rows().data();
    var pAr = [];
    for (var i = 0; i < p.length; i++)
        pAr[i] = p[i];

    if (!bValid)
        return;

    if (self.oCust.flds.cust_name == "")
        $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

    var oData = {
        "rbo_docs": {
            "docId": docId,
            "doc_type": self.sDocType,
            "doc_num": $("#doc_num").val(),
            "doc_date": $("#doc_date").val(),
            "doc_sum": $("#doc_sum").val(),
            "doc_base": $("#doc_baseId").val(),// скрытое поле в форме выбора документа - основания
            "doc_status": $("#doc_status").val(),
            "doc_manager": $("#doc_manager").val(),
            "custId": $("#custId").val(),// скрытое поле в форме выбора клиента
            "doc_cust": self.oCust.flds,
            "doc_firm": $("#doc_firm").val(),
            "doc_products": pAr
        }
    };

    var taskCmd = "doc_create";
    if (!IsNull(docId) && docId > 0)
        taskCmd = "doc_update";
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: oData,
        url: comPath + "ajax.php?task=" + taskCmd,
        success: function (doc_data) {
            $("#doc-form").dialog("close");
            self.oTable.fnDraw();
        }
    });
};

// ===================================================================================
RbDoc.prototype.copyDoc = function (title, docId) {
    var self = this;
    var viewName = "";
    var docType = "";
    if (!IsNull(self.copyToList) && self.copyToList.length > 0) {
        for (var x = 0; x < self.copyToList.length; x++) {
            if (self.copyToList[x].title == title) {
                docType = self.copyToList[x].docType;
                viewName = self.copyToList[x].viewName;
                break;
            }
        }
    }

    var p = self.apiTableProducts.rows().data();
    var pAr = [];
    for (var i = 0; i < p.length; i++)
        pAr[i] = p[i];

    if (self.oCust.flds.cust_name == "")
        $("#custId").val("-1");//значит мы сознательно удаляем покупателя из документа

    //проверить, есть ли уже документ, основание которого равно docId
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "docId": null,
                "doc_type": docType,
                "doc_num": $("#doc_num").val(),
                "doc_date": getCurrentDate(),
                "doc_sum": $("#doc_sum").val(),
                "doc_base": docId,
                "doc_status": "",
                "doc_manager": $("#doc_manager").val(),
                "custId": $("#custId").val(),
                "doc_cust": self.oCust.flds,
                "doc_firm": $("#doc_firm").val(),
                "doc_products": pAr
            }
        },
        url: comPath + "ajax.php?task=doc_create",
        success: function (doc_data) {
            window.open('index.php?option=com_rbo&view='+viewName+'&docid='+docId, '_blank');
        }
    });
};

// ===================================================================================
RbDoc.prototype.createDoc = function () {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "doc_type": self.sDocType
            }
        },
        url: comPath + "ajax.php?task=get_doc_num",
        success: function (p) {
            var i = {};
            i.doc_num = p.new_num;
            i.doc_date = p.new_date;
            self.showDocForm(i);
        }
    });
};

// ===================================================================================
RbDoc.prototype.deleteDoc = function (docId) {
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "docId": docId,
                "doc_type": self.sDocType
            }
        },
        url: comPath + "ajax.php?task=doc_delete",
        success: function (doc_data) {
            self.oTable.fnDraw();
        }
    });

    $("#doc-form").dialog("close");
};

// ===================================================================================
RbDoc.prototype.showDocForm = function (i) {
    var self = this;
    refillSelect("doc_manager", getPeopleList(), true);
    refillSelect("doc_firm", getFirmList());
    refillSelect("doc_status", self.oStatusList, true);

    self.docId = i.docId;

    //установим базовые реквизиты документа
    $("#doc_num").val(i.doc_num);
    $("#doc_date").val(i.doc_date);
    $("#doc_sum").val(i.doc_sum);
    $("#doc_status").val(i.doc_status);

    //установим поля документа-основания
    var sDocBase = "";
    $("#doc_baseId").val(i.doc_base);
    if ((i.doc_base > 0) && !IsNull(i.doc_base_doc)) {
        sDocBase = "Счет №" + i.doc_base_doc.doc_num + " от " + i.doc_base_doc.doc_date;
    }
    $("#doc_base").val(sDocBase);

    //укажем менеджера
    $('#doc_manager option:selected').each(function () {
        this.selected = false;
    });
    if (!IsEmpty(i.doc_manager))
        $("#doc_manager option:contains('" + i.doc_manager + "')").prop("selected", "selected");

    //установим поля контрагента
    self.oCust.setCustFlds('saved', i.doc_cust);

    //установим фирму
    if (!IsNull(i.doc_firm))
        $("#doc_firm option:contains('" + i.doc_firm.toUpperCase() + "')").prop("selected", "selected");
    $("#doc_rem").val(i.doc_rem);

    //заполним список товаров/услуг
    self.oTableProducts.fnClearTable();
    var x;
    if (!IsNull(i.doc_products) && i.doc_products.length > 0) {
        for (x = 0; x < i.doc_products.length; x++)
            i.doc_products[x].lineNo = x;
        self.oTableProducts.fnAddData(i.doc_products);
    }

    //установим документ в правильное состояние RW
    var readOnly = this.setRW(i.doc_status);

    var oBtns = {};

    if (!readOnly) {
        oBtns["Удалить"] = function () {
            Ask("Документ будет удален. Продолжить?", "Удалить документ", "Отмена", function () {
                self.deleteDoc(i.docId);
            }, null, "#dialog-confirm");
        }
    }

    if (!IsNull(self.copyToList) && self.copyToList.length > 0) {
        for (x = 0; x < self.copyToList.length; x++) {
            oBtns[self.copyToList[x].title] = function (event) {
                self.copyDoc($(event.target).text(), i.docId);
            };
        }
    }

    if (!IsNull(self.printList) && self.printList.length > 0) {
        for (x = 0; x < self.printList.length; x++) {
            oBtns[self.printList[x].title] = function (event) {
                self.showPrintView($(event.target).text(), i.docId);
            };
        }
    }

    oBtns["Сохранить"] = function () {
        self.saveDoc(i.docId);
    };

    oBtns["Отмена"] = function () {
        $("#doc-form").dialog("close");
    };

    $("#doc-form").dialog({
        title: self.sDocTypeTitle + " №" + $('#doc_num').val(),
        buttons: oBtns
    });

    /*var htmlSelectCopyTo = "<select id='dlg_copy_list_select'></select>";
     $(".ui-dialog-buttonset").html(htmlSelectCopyTo+$(".ui-dialog-buttonset").html());
     refillSelect("dlg_copy_list_select",{"акт":"создать акт","накл":"создать накладную"});*/

    $("#dlg_copy_list").selectmenu({
        select: function (event, ui) {
            if (!IsEmpty($(this).val()))
                location.href = $(this).val();
        }
    });

    $("#doc-form").dialog("open");
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
    self.editing_lineNo = x;

    var p = self.oTableProducts.fnGetData(x);
    if (IsNull(p))
        self.lines_before_update = 0;
    else
        self.lines_before_update = p.length;

    self.oProduct.showProductForm({
        pData: p,
        fnDelete: function () {
            if (self.editing_lineNo >= 0) {
                self.oTableProducts.fnDeleteRow(self.editing_lineNo);
                var pAll = self.oTableProducts.fnGetData();
                var iSum = 0;
                for (var x = 0; x < pAll.length; x++) {
                    iSum += Number(pAll[x].product_sum);
                }
                $('#doc_sum').val(iSum);
            }
        },

        fnSave: function (pObj) {
            if (self.editing_lineNo >= 0) {
                pObj.lineNo = self.editing_lineNo;
                self.oTableProducts.fnUpdate(pObj, pObj.lineNo);
            } else {
                pObj.lineNo = self.lines_before_update;
                self.oTableProducts.fnAddData(pObj);
            }
            var pAll = self.oTableProducts.fnGetData();
            var iSum = 0;
            for (var x = 0; x < pAll.length; x++) {
                iSum += Number(pAll[x].product_sum);
            }
            $('#doc_sum').val(iSum);
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
RbDocSale.prototype.readDoc = function (docId) {//удалить? дублирует RbDoc?
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "docId": docId,
                "doc_type": self.sDocType
            }
        },
        url: comPath + "ajax.php?task=doc_read",
        success: function (doc_data) {
            self.showDocForm(doc_data);
        }
    });
};

// ===================================================================================
RbDocSale.prototype.createDoc = function () {//удалить? дублирует RbDoc?
    var self = this;
    $.ajax({
        dataType: 'json',
        type: "POST",
        data: {
            "rbo_docs": {
                "doc_type": self.sDocType
            }
        },
        url: comPath + "ajax.php?task=get_doc_num",
        success: function (p) {
            var i = {};
            i.doc_num = p.new_num;
            i.doc_date = p.new_date;
            self.showDocForm(i);
        }
    });
};