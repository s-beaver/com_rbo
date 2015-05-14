// ===================================================================================
function IsNull(vVal) {
  var und = void null;
  switch (vVal) {
  case und:
  case null:
  case undefined:
  case NaN: {
    return true;
  }
  default: {
    return false;
  }
  }
}

// ===================================================================================
function NullTo(oTest, sVoz) {
  if (IsNull(oTest))
    return sVoz;
  else
    return oTest;
}

/*
 * Join array elements with a string
 *  + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net) +
 * improved by: _argos
 */
// ===================================================================================
function implode(glue, pieces) {

  return ((pieces instanceof Array) ? pieces.join(glue) : pieces);
}

// ===================================================================================
function updateTips(t, tips) {
  tips.text(t).addClass("ui-state-highlight");
  tips.show();
  setTimeout(function() {
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
    updateTips("Длина поля " + n + "  должна быть не менее " + min
        + "  и не более " + max + " символов", tips);
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
 * Ask("123","Удалить","Отмена",function(){deleteOrder(invId)},null,"#dialog-confirm");
 * 
 */
// ===================================================================================
function Ask(sText, okText, cancelText, fnOk, fnCancel, sElemSelector) {
  $(sElemSelector).html(sText);
  var bOk = {};
  bOk[okText] = function(arg) {
    $(sElemSelector).dialog("close");
    fnOk(arg);
  };
  bOk[cancelText] = function() {
    $(sElemSelector).dialog("close");
  }

  $(sElemSelector).dialog({
    resizable : false,
    height : 180,
    modal : true,
    buttons : bOk
  });

  $(sElemSelector).dialog("open");
}
