  <div id="cust-form" title="Позиция">
    <form id="cust-form-form" method="post" action="">
      <fieldset style='padding: 0'>
        <table>
          <tr>
            <td><label for="cust_search">Поиск</label></td>
            <td><input type="text" name="cust_search" id="cust_search" style='width: 350px' /> 
              <a href="javascript:doc.custSearch()"> 
                <img src="<?php echo JRBO_PATH?>/images/icon-32-search-on.png" />
              </a>
              <input style="visibility: hidden" id="custId" />
            </td>
          </tr>
          <tr>
            <td><label for="cust_name">Название</label></td>
            <td><select name="cust_name" id="cust_name" style="width: 500px" onChange="doc.setCustFlds('selected')"></select></td>
          </tr>
        </table>
        <table>
          <tr>
            <td><label for="cust_fullname">
                Название<br>полностью
              </label></td>
            <td><input type="text" name="cust_fullname" id="cust_fullname" style='width: 500px' /></td>
          </tr>
          <tr>
            <td><label for="cust_addr">Адрес</label></td>
            <td><input type="text" name="cust_addr" id="cust_addr" style='width: 500px' /></td>
          </tr>
          <tr>
            <td colspan="2"><label for="cust_nonds">Без НДС</label><input type="checkbox" name="cust_nonds" id="cust_nonds"> <label for="cust_phone">&nbsp;Тел.</label> <input
              type="text" name="cust_phone" id="cust_phone" style='width: 200px' /> <label for="cust_email">&nbsp;@</label> <input type="text" name="cust_email"
              id="cust_email" style='width: 200px' /></td>
          </tr>
        </table>
        <table>
          <tr>
            <td colspan="2"><label for="cust_inn">&nbsp;ИНН</label><input type="text" name="cust_inn" id="cust_inn" style='width: 150px' /> <label for="cust_kpp">&nbsp;КПП</label><input
              type="text" name="cust_kpp" id="cust_kpp" style='width: 150px' /></td>
          </tr>
          <tr>
            <td colspan="2"><label for="cust_okpo">ОКПО</label><input type="text" name="cust_okpo" id="cust_okpo" style='width: 150px' /> <label for="cust_ogrn">&nbsp;ОГРН</label>
              <input type="text" name="cust_ogrn" id="cust_ogrn" style='width: 150px' /></td>
          </tr>
          <tr>
            <td><label for="cust_bank">Банк</label></td>
            <td><input type="text" name="cust_bank" id="cust_bank" style='width: 500px' /></td>
          </tr>
          <tr>
            <td><label for="cust_bik">БИК</label></td>
            <td><input type="text" name="cust_bik" id="cust_bik" style='width: 150px' /></td>
          </tr>
          <tr>
            <td colspan="2"><label for="cust_rch">р/сч</label><input type="text" name="cust_rch" id="cust_rch" style='width: 250px' /> <label for="cust_kch">к/сч</label><input
              type="text" name="cust_kch" id="cust_kch" style='width: 250px' /></td>
          </tr>
        </table>
      </fieldset>
    </form>
  </div>
