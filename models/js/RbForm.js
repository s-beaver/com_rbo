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
            case "DIV":
            case "LABEL":
                o[objPrefix][s] = $(this).html();
                break;

            case "TEXTAREA":
                o[objPrefix][s] = $(this).val();
                break;

            case "SELECT":
                o[objPrefix][s] = $(this).find('option:selected').text();
                break;

            case "INPUT":
                tagType = NullTo($(this).attr("type"), "");
                if (tagType.toUpperCase() == "CHECKBOX")
                    o[objPrefix][s] = $(this).prop("checked") ? "1" : "0";
                else
                    o[objPrefix][s] = $(this).val();
                break;

            case "BUTTON": {
                break;
            }

            default:
                o[objPrefix][s] = $(this).html();
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
            case "DIV":
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
                    $("#" + sIdDot + " option:contains('" + o[s] + "')").prop("selected", "selected");
                    var sCheck1 = NullTo($('#' + sIdDot + ' option:selected').val(), "");
                    var sCheck2 = NullTo(o[s], "");
                    if (sCheck1.toUpperCase() != sCheck2.toUpperCase()) {
                        $(elem).append('<option value="' + o[s] + '">' + o[s] + '</option>');
                        $("#" + sIdDot + " option:contains('" + o[s] + "')").prop("selected", "selected");
                    }
                } else
                    $('#' + sIdDot + ' option:first').prop("selected", "selected");
                break;

            case "BUTTON": {
                break;
            }

            default:
                $(this).html(o[s]);
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
        "затраты-связь": "затраты-связь"
    };

}