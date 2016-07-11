/**
 * Хелпер для работы с формами jquery. Получает данные в объект javascript из
 * форм и заполняет форму данными из объекта javascript
 */

//===================================================================================
/*
 * Перебирает следующие элементы формы: input, select, textarea и формирует
 * объект
 */
function getFormData(formName, objPrefix) {
    var s, o = {}, tagName, tagType;
    o[objPrefix] = {};
    $("#" + formName + " *").find("[id^='" + objPrefix + "']").each(function (x, elem) {
        s = $(this).attr("id");
        s = s.replace(objPrefix + ".", "");
        tagName = $(this).prop("tagName");
        switch (tagName.toUpperCase()) {
            case "LABEL":
                o[objPrefix][s] = $(this).html();
                break;

            case "TEXTAREA":
                o[objPrefix][s] = $(this).val();
                break;

            case "SELECT":
                o[objPrefix][s] = $(this).find('option:selected').text();
                o[objPrefix][s + "_value"] = $(this).find('option:selected').val();
                break;

            case "INPUT":
                tagType = NullTo($(this).attr("type"), "");
                if (tagType.toUpperCase() == "CHECKBOX")
                    o[objPrefix][s] = $(this).prop("checked") ? "1" : "0";
                else
                    o[objPrefix][s] = $(this).val();
                break;
        }
    });

    return o;
}

//===================================================================================
function setFormData(formName, objPrefix, o) {
    var s, sId, sIdDot, tagName, tagType;
    o = NullTo(o, {});
    $("#" + formName + " *").find("[id^='" + objPrefix + "']").each(function (x, elem) {
        sId = $(this).attr("id");
        sIdDot = sId.replace(objPrefix + ".", objPrefix + "\\.");
        s = sId.replace(objPrefix + ".", "");
        o[s] = NullTo(o[s], "");
        tagName = $(this).prop("tagName");
        switch (tagName.toUpperCase()) {
            case "LABEL":
                $(this).html(o[s]);
                break;

            case "TEXTAREA":
                $(this).val(o[s]);
                break;

            case "INPUT":
                tagType = NullTo($(this).attr("type"), "");
                if (tagType.toUpperCase() == "CHECKBOX")
                    $(this).prop("checked", o[s] == "1");//(o[s] == "1" || o[s]));
                else
                    $(this).val(o[s]);
                break;

            case "SELECT":
                $('#' + sIdDot + ' option:selected').each(function (x, elem) {
                    $(elem).selected = false;
                });
                if (!IsEmpty(o[s])) {
                    var needToAdd = false;
                    $("#" + sIdDot + " option:contains('" + o[s] + "')").prop("selected", "selected");
                    if (NullTo(o[s + "_value"], "") != "") {//есть сохраненное значение
                        needToAdd = NullTo($('#' + sIdDot + ' option:selected').val(), "").toUpperCase() != NullTo(o[s + "_value"], "").toUpperCase();
                    } else {
                        needToAdd = NullTo($('#' + sIdDot + ' option:selected').val(), "").toUpperCase() != NullTo(o[s], "").toUpperCase();
                    }

                    if (needToAdd) {
                        var selValue = (NullTo(o[s + "_value"], "") == "") ? o[s] : o[s + "_value"];
                        $(elem).append('<option value="' + selValue + '">' + o[s] + '</option>');
                        $("#" + sIdDot + " option:contains('" + o[s] + "')").prop("selected", "selected");
                    }
                } else
                    $('#' + sIdDot + ' option:first').prop("selected", "selected");
                break;

        }
    });

}

//===================================================================================
/**
 * Заполнение тэга select из массива или объекта
 * @param selectId - id тэга select
 * @param o - массив или объект. Если передается массив, то value совпадает с названием элемента,
 * если же передается объект, то value берется из названия свойства объекта
 * @param bAddEmptyLine - добавляется или нет пустой элемент сверху списка
 * @return null
 */
function refillSelect(selectId, o, bAddEmptyLine) {
    bAddEmptyLine = NullTo(bAddEmptyLine, false);
    $('#' + selectId + ' option').remove();
    if (bAddEmptyLine)
        $('#' + selectId).append('<option value=""></option>');
    if (IsArray(o)) {
        for (var i = 0; i < o.length; i++) {
            $('#' + selectId).append('<option value="' + o[i] + '">' + o[i] + '</option>');
        }
    } else if (IsObject(o)) {
        for (var prop in o) {
            $('#' + selectId).append('<option value="' + prop + '">' + o[prop] + '</option>');
        }
    }
}

//===================================================================================
function getPeopleList() {
    return {
        "Алексей": "Алексей",
        "Аня": "Аня",
        "Володя": "Володя",
        "Николай": "Николай"
    };

}

//===================================================================================
function getFirmList() {
    return {
        "ИП-бн": "ИП-бн",
        "ИП-нал": "ИП-нал",
        "ООО": "ООО"
    };

}

//===================================================================================
function getOperTypeList() {
    return {
        "продажа": "продажа",
        "закуп": "закуп",
        "затраты-аренда": "затраты-аренда",
        "затраты-банков": "затраты-банков",
        "затраты-бухгал": "затраты-бухгал",
        "затраты-зарплата": "затраты-зарплата",
        "затраты-коммун": "затраты-коммун",
        "затраты-налоги": "затраты-налоги",
        "затраты-произв": "затраты-произв",
        "затраты-прочие": "затраты-прочие",
        "затраты-связь": "затраты-связь",
        "ддс": "ддс"
    };

}

//===================================================================================
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

//===================================================================================
/**
 * Получает куки по имени, возвращает в виде объекта
 * @param cname - имя куки
 * @returns {{}}
 */
function getObjCookie(cname) {
    var c = getCookie(cname);
    if (c == "") return {};
    var res = {};
    try {
        res = JSON.parse(c);
    }
    catch (e) {
        res = {};
    }
    return res;
}

//===================================================================================
/**
 * Устанавливает куки
 * @param cname - имя куки
 * @param o - объект
 */
function setObjCookie(cname, o) {
    document.cookie = cname + "=" + JSON.stringify(o);
}

//===================================================================================
function getPrintLinkByDoc(docId, docType) {
    docId = NullTo(docId, 0);
    if (docId == 0) return "";
    var link = "index.php?option=com_rbo&format=raw";
    var printLink = "";
    switch (docType) {
        case "акт":
        {
            printLink = "PrnInv";
            break;
        }
        case "накл":
        {
            printLink = "PrnShip";
            break;
        }
        case "B_ACT":
        {
            printLink = "";
            break;
        }
        case "B_BIL":
        {
            printLink = "PrnBBil";
            break;
        }
        default:
            printLink = "";
    }
    if (printLink == "") return "";
    return link + "&view=" + printLink + "&docid=" + docId;
}
