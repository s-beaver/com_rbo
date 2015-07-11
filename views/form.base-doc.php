  <div id="base-doc-form" title="Выбор документа-основания">
    <form id="base-doc-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td><label for="base-doc-search">Поиск</label></td>
            <td><input type="text" name="base-doc-search" id="base-doc-search" style='width: 150px' /> 
              <a href="javascript:doc.baseDocSearch()" id="base-doc-search-btn"> 
                <img src="<?php echo JRBO_PATH?>/images/icon-32-search-on.png" />
              </a>
              <!--input style="visibility: hidden" id="custId" /-->
              <input style="visibility: hidden" id="doc_baseId" />
            </td>
          </tr>
          <tr>
            <td><label for="base-doc-cust-name">Название</label></td><!--cust_name-->
            <td><select name="base-doc-cust-name" id="base-doc-cust-name" style="width: 500px" onChange="doc.setBaseDocList()"></select></td>
          </tr>
          <tr>
            <td><label for="base-doc-doc">Основание</label></td><!--cust_base_doc-->
            <td><select name="base-doc-doc" id="base-doc-doc" style="width: 400px">
              </select></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>
