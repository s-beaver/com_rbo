// ===================================================================================
function initTableDocList(comPath, sDocType) {
  var oTable = $('#TableDoc').dataTable({
    "bJQueryUI" : true,
    "bProcessing" : true,
    "bServerSide" : true,
    // "dom": 'HFT<"toolbar"><t>lfrtip',
    "tableTools" : {
      "sSwfPath" : "/swf/copy_csv_xls_pdf.swf"
    },
    "aaSorting" : [ [ 1, "desc" ] ],
    "sAjaxSource" : comPath + "ajax.php?task=get_doc_list&doc_type=" + sDocType,
    "fnServerData" : function(sSource, aoData, fnCallback, oSettings) {
      oSettings.jqXHR = $.ajax({
        "dataType" : 'json',
        "type" : "POST",
        "data" : aoData,
        "url" : sSource,
        "success" : function(json) {
          fnCallback(json);
        }
      });
    },
    "aoColumns" : [ {
      "sTitle" : "Номер",
      "sClass" : "center",
      "mData" : function(source, type, val) {
        return "<a href='javascript:readDoc(" + source.docId + ")'>" + source.doc_num + " /" + source.doc_date + "</a>";
      }
    }, {
      "sTitle" : "Покупатель",
      "mData" : "doc_cust"
    }, {
      "sTitle" : "Сумма",
      "sClass" : "center",
      "mData" : "doc_sum"
    }, {
      "sTitle" : "Фирма",
      "sClass" : "center",
      "mData" : "doc_firm"
    }, {
      "sTitle" : "Статус",
      "sClass" : "center",
      "mData" : "doc_status"
    }, {
      "sTitle" : "Менеджер",
      "mData" : "doc_manager"
    } ],
    "oLanguage" : {
      "sProcessing" : "Подождите...",
      "sLengthMenu" : "Показать _MENU_ строк",
      "sZeroRecords" : "Записи отсутствуют.",
      "sInfo" : "Документы с _START_ по _END_ (всего: _TOTAL_)",
      "sInfoEmpty" : "Документов нет",
      "sInfoFiltered" : "(отфильтровано из _MAX_ записей)",
      "sInfoPostFix" : "",
      "sSearch" : "Поиск:",
      "sUrl" : "",
      "oPaginate" : {
        "sFirst" : "В начало",
        "sPrevious" : "Предыдущие",
        "sNext" : "Следующие",
        "sLast" : "В конец"
      }
    }
  });

  /*
   * oTableTools = new $.fn.dataTable.TableTools( oTable, { "buttons": [ "copy",
   * "csv", "xls", "pdf", { "type": "print", "buttonText": "Print me!" } ] } ); $(
   * oTableTools.fnContainer() ).insertAfter('div.info');
   */
  return oTable;
}

//===================================================================================
function initTableProducts(comPath, sDocType) {
  var oTableProducts = $('#TableProducts').dataTable({
    "bPaginate" : false,
    "searching" : false,
    "ordering" : false,
    "aoColumns" : [ {
      "sTitle" : "Код",
      //"sWidth" : "100",
      "mData" : "product_code"
    }, {
      "sTitle" : "Наименование",
      //"sWidth" : "450",
      "mData" : "product_name"
    }, {
      "sTitle" : "Цена",
      "sClass" : "center",
      //"sWidth" : "100",
      "mData" : "product_price"
    }, {
      "sTitle" : "К-во",
      "sClass" : "center",
      //"sWidth" : "100",
      "mData" : "product_cnt"
    }, {
      "sTitle" : "Сумма",
      "sClass" : "center",
      //"sWidth" : "100",
      "mData" : "product_sum"
    }, {
      "sTitle" : "Ред.",
      "sClass" : "center",
      //"sWidth" : "50",
      "mData" : function(source, type, val) {
        return "<a id='edit_product' href='javascript:showProductForm(" + source.lineNo + ")'>" + "<img src='" + comPath + "images/icon-32-edit-on.png'/></a>";
      }
    } ],
    "oLanguage" : {
      "sProcessing" : "Подождите...",
      "sLengthMenu" : "Показать _MENU_ строк",
      "sZeroRecords" : "Записи отсутствуют.",
      "sInfo" : "Строки с _START_ по _END_ (всего: _TOTAL_)",
      "sInfoEmpty" : "Строк нет",
      "sInfoFiltered" : "(отфильтровано из _MAX_ записей)",
      "sInfoPostFix" : "",
      "sSearch" : "Поиск:",
      "sUrl" : "",
      "oPaginate" : {
        "sFirst" : "В начало",
        "sPrevious" : "Предыдущие",
        "sNext" : "Следующие",
        "sLast" : "В конец"
      }
    }
  });
  return oTableProducts;
  
}

//===================================================================================
function initHeaderDocList(s) {
  $("#header_doclist_choose_list a h2").html(s);
}
