var monthAr = new Array("", "январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
var monthAr1 = new Array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");

// ===================================================================================
/*
 * Функция предназначена для коректной проверки на не заданое или не правильное
 * значение #hval(variant)#первый параметр vVal - Тестируемое значение
 */
function IsNull(vVal) {
    var und = void null;
    switch (vVal) {
        case und:
        case null:
        case undefined:
        case NaN:
        {
            return true;
        }
        default:
        {
            return false;
        }
    }
}

// ===================================================================================
/*
 * Функция предназначена для проверки и приведения oTest к умолчательному
 * значению sVoz, в случаях, когда oTest равен null или NaN
 * #hval(variant)#первый параметр oTest #hval(variant)#второй параметр sVoz
 */
function NullTo(oTest, sVoz) {
    if (IsNull(oTest))
        return sVoz;
    else
        return oTest;
}

//===================================================================================
/*
 * Функция предназначена для проверки и приведения oTest к умолчательному
 * значению sVoz, в случаях, когда oTest является пустым значением (IsEmpty)
 * #hval(variant)#первый параметр oTest #hval(variant)#второй параметр sVoz
 */
function EmptyTo(oTest, sVoz) {
    if (IsEmpty(oTest))
        return sVoz;
    else
        return oTest;
}

//===================================================================================
/*
 * Функция предназначена для проверки пустое ли значение #hval(variant)# vVal -
 * проверяемое значение #href(lib.ObjLength)# - вычисление количества элементов
 * объекта JavaScript
 */
function IsEmpty(vVal) {
    if (IsNull(vVal))
        return true;
    if (typeof (vVal) == "string")
        return (vVal == "");
    if (IsArray(vVal))
        return (vVal.length == 0);
    if (IsObject(vVal))
        return (ObjLength(vVal) <= 0);
    return false;
}

//=============================================================================
/*
 * Функция предназначена для проверки является ли переданая переменная массивом
 * JScript
 */
function IsArray(obj) {
    if (IsObject(obj)) {
        if (obj instanceof Array)
            return true;
        else {
            try {
                var aAr = new Array();
                if ((obj.constructor + "") == (aAr.constructor + ""))
                    return true;
                else {
                    if (obj.length == undefined) return false;
                    if (obj.length > 0)
                        obj[0] = obj[0];
                    else
                        return (obj[0] == undefined);
                    return true;
                }
            } catch (err) {
                return false;
            }
        }
    }
    return false;
}

//=============================================================================
/*
 * Функция предназначена для проверки является ли переданое значение объектом
 * #hval(variant)# oObj - проверяемый объект
 */
function IsObject(oObj) {
    return typeof (oObj) == "object";
}

//===================================================================================
function extendObject(Child, Parent) {
    var F = function () {
    }
    F.prototype = Parent.prototype;
    Child.prototype = new F();
    Child.prototype.constructor = Child;
    Child.superclass = Parent.prototype;
}

//===================================================================================
function updateTips(t, tips) {
    tips.text(t).addClass("ui-state-highlight");
    tips.show();
    setTimeout(function () {
        tips.removeClass("ui-state-highlight", 1500);
        tips.hide();
    }, 500);
}

// ===================================================================================
function checkNotEmpty(o, n, tips) {
    if (o.val().length == 0) {
        o.addClass("ui-state-error");
        updateTips("Заполните поле " + n, tips);
        return false;
    } else {
        return true;
    }
}

// ===================================================================================
function checkLength(o, n, min, max, tips) {
    if (o.val().length > max || o.val().length < min) {
        o.addClass("ui-state-error");
        updateTips("Длина поля " + n + "  должна быть не менее " + min + "  и не более " + max + " символов", tips);
        return false;
    } else {
        return true;
    }
}

// ===================================================================================
function checkRegexp(o, regexp, n, tips) {
    if (!(regexp.test(o.val()))) {
        o.addClass("ui-state-error");
        updateTips(n, tips);
        return false;
    } else {
        return true;
    }
}

/*
 * Ask("123","Удалить","Отмена",function(){deleteOrder(docId)},null,"#dialog-confirm");
 * 
 */
// ===================================================================================
function Ask(sText, okText, cancelText, fnOk, fnCancel, sElemSelector) {
    $(sElemSelector).html(sText);
    var bOk = {};
    bOk[okText] = function (arg) {
        $(sElemSelector).dialog("close");
        fnOk(arg);
    };
    bOk[cancelText] = function () {
        $(sElemSelector).dialog("close");
    }

    $(sElemSelector).dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: bOk
    });

    $(sElemSelector).dialog("open");
}

// ===================================================================================
function convertDate(d) {// в формате 01.12.2015
    if (IsNull(d))
        return "";
    var re = new RegExp("(\\.([0-9]{1,2})\\.)");
    return d.replace(re, function (a, b, c) {
        return " " + monthAr1[Number(c)] + " "
    });
}

// ===================================================================================
function getCurrentDate() {
    var d = new Date();
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    return day + "." + month + "." + year;
}

// ===================================================================================
function number_to_string(_number, _no_first_upper, _no_rub) {
    var _arr_numbers = new Array();
    _arr_numbers[1] = new Array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать',
        'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    _arr_numbers[2] = new Array('', '', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    _arr_numbers[3] = new Array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    // ++++++++++++++++++
    function number_parser(_num, _desc) {
        var _string = '';
        var _num_hundred = '';
        if (_num.length == 3) {
            _num_hundred = _num.substr(0, 1);
            _num = _num.substr(1, 3);
            _string = _arr_numbers[3][_num_hundred] + ' ';
        }
        if (_num < 20)
            _string += _arr_numbers[1][parseFloat(_num)] + ' ';
        else {
            var _first_num = _num.substr(0, 1);
            var _second_num = _num.substr(1, 2);
            _string += _arr_numbers[2][_first_num] + ' ' + _arr_numbers[1][_second_num] + ' ';
        }
        switch (_desc) {
            case 0:
                var _last_num = parseFloat(_num.substr(-1));
                if (_no_rub)
                    break;
                if (_last_num == 1)
                    _string += 'рубль';
                else if (_last_num > 1 && _last_num < 5)
                    _string += 'рубля';
                else
                    _string += 'рублей';
                break;
            case 1:
                var _last_num = parseFloat(_num.substr(-1));
                if (_last_num == 1)
                    _string += 'тысяча ';
                else if (_last_num > 1 && _last_num < 5)
                    _string += 'тысячи ';
                else
                    _string += 'тысяч ';
                _string = _string.replace('один ', 'одна ');
                _string = _string.replace('два ', 'две ');
                break;
            case 2:
                var _last_num = parseFloat(_num.substr(-1));
                if (_last_num == 1)
                    _string += 'миллион ';
                else if (_last_num > 1 && _last_num < 5)
                    _string += 'миллиона ';
                else
                    _string += 'миллионов ';
                break;
            case 3:
                var _last_num = parseFloat(_num.substr(-1));
                if (_last_num == 1)
                    _string += 'миллиард ';
                else if (_last_num > 1 && _last_num < 5)
                    _string += 'миллиарда ';
                else
                    _string += 'миллиардов ';
                break;
        }
        _string = _string.replace('  ', ' ');
        return _string;
    }

    // ++++++++++++++++++
    function decimals_parser(_num) {
        var _first_num = _num.substr(0, 1);
        var _second_num = parseFloat(_num.substr(1, 2));
        var _string = ' ' + _first_num + _second_num;
        if (_second_num == 1)
            _string += ' копейка';
        else if (_second_num > 1 && _second_num < 5)
            _string += ' копейки';
        else
            _string += ' копеек';
        return _string;
    }

    _no_rub = NullTo(_no_rub, false);
    _no_first_upper = NullTo(_no_first_upper, false);

    if (!_number || _number == 0)
        return false;
    if (typeof _number !== 'number') {
        _number = _number.replace(',', '.');
        _number = parseFloat(_number);
        if (isNaN(_number))
            return false;
    }
    _number = _number.toFixed(2);
    if (_number.indexOf('.') != -1) {
        var _number_arr = _number.split('.');
        var _number = _number_arr[0];
        var _number_decimals = _number_arr[1];
    }
    var _number_length = _number.length;
    var _string = '';
    var _num_parser = '';
    var _count = 0;
    for (var _p = (_number_length - 1); _p >= 0; _p--) {
        var _num_digit = _number.substr(_p, 1);
        _num_parser = _num_digit + _num_parser;
        if ((_num_parser.length == 3 || _p == 0) && !isNaN(parseFloat(_num_parser))) {
            _string = number_parser(_num_parser, _count) + _string;
            _num_parser = '';
            _count++;
        }
    }
    if (_number_decimals && !_no_rub)
        _string += decimals_parser(_number_decimals);
    if (_no_first_upper)
        return _string;
    var _string1 = _string.substr(0, 1).toUpperCase();
    var _string2 = _string.substr(1);
    return _string1 + _string2;
}

// ===================================================================================
function getURLParam(paramName) {
    if (IsEmpty(paramName)) return "";
    var parStr = window.location.search.toString();
    var re = new RegExp(paramName + "=(\\w+)", "i");
    var aSearched = parStr.match(re);
    if (IsNull(aSearched))
        return "";
    else
        return aSearched[1];
}
