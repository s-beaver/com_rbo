<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/rboForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/refCustomers.js"></script>
    <link rel="stylesheet" href="<?php echo JRBO_PATH ?>/css/rbo.css"/>

    <style>
    </style>

</head>
<body>
<?php include RBO_PATH . '/views/header.doclist.php' ?>

<div id="cst-form" title="Справочник-контрагенты">
    <form id="cst-form-form" method="post" action="">

        <fieldset style='padding: 0'>
            <table>
                <tr>
                    <td><label for="rbo_cust.cust_name">Название</label></td>
                    <td><input type="text" name="rbo_cust.cust_name" id="rbo_cust.cust_name" style="width: 400px"><input
                            style="visibility: hidden" id="rbo_cust.custId"/></td>
                </tr>
                <tr>
                    <td><label for="rbo_cust.cust_fullname">
                            Название<br>полностью
                        </label></td>
                    <td><input type="text" name="rbo_cust.cust_fullname" id="rbo_cust.cust_fullname"
                               style='width: 500px'/></td>
                </tr>
                <tr>
                    <td><label for="rbo_cust_flds.cust_addr">Адрес</label></td>
                    <td><input type="text" name="rbo_cust_flds.cust_addr" id="rbo_cust_flds.cust_addr" style='width: 500px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="rbo_cust_flds.cust_nonds">Без НДС</label><input type="checkbox"
                                                                                           name="rbo_cust_flds.cust_nonds"
                                                                                           id="rbo_cust_flds.cust_nonds">
                        <label for="rbo_cust.cust_phone">&nbsp;Тел.</label> <input
                            type="text" name="rbo_cust.cust_phone" id="rbo_cust.cust_phone" style='width: 200px'/>
                        <label for="rbo_cust.cust_email">&nbsp;@</label> <input type="text" name="rbo_cust.cust_email"
                                                                                id="rbo_cust.cust_email"
                                                                                style='width: 200px'/></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="2"><label for="rbo_cust_flds.cust_inn">&nbsp;ИНН</label><input type="text"
                                                                                           name="rbo_cust_flds.cust_inn"
                                                                                           id="rbo_cust_flds.cust_inn"
                                                                                           style='width: 150px'/> <label
                            for="rbo_cust_flds.cust_kpp">&nbsp;КПП</label><input
                            type="text" name="rbo_cust_flds.cust_kpp" id="rbo_cust_flds.cust_kpp" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="rbo_cust_flds.cust_okpo">ОКПО</label><input type="text"
                                                                                       name="rbo_cust_flds.cust_okpo"
                                                                                       id="rbo_cust_flds.cust_okpo"
                                                                                       style='width: 150px'/> <label
                            for="rbo_cust_flds.cust_ogrn">&nbsp;ОГРН</label>
                        <input type="text" name="rbo_cust_flds.cust_ogrn" id="rbo_cust_flds.cust_ogrn" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td><label for="rbo_cust_flds.cust_bank">Банк</label></td>
                    <td><input type="text" name="rbo_cust_flds.cust_bank" id="rbo_cust_flds.cust_bank" style='width: 500px'/></td>
                </tr>
                <tr>
                    <td><label for="rbo_cust_flds.cust_bik">БИК</label></td>
                    <td><input type="text" name="rbo_cust_flds.cust_bik" id="rbo_cust_flds.cust_bik" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="rbo_cust_flds.cust_rch">р/сч</label><input type="text"
                                                                                      name="rbo_cust_flds.cust_rch"
                                                                                      id="rbo_cust_flds.cust_rch"
                                                                                      style='width: 250px'/> <label
                            for="rbo_cust_flds.cust_kch">к/сч</label><input
                            type="text" name="rbo_cust_flds.cust_kch" id="rbo_cust_flds.cust_kch" style='width: 250px'/></td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>

<?php include RBO_PATH . '/views/form.del-doc.php' ?>

<table id="TableCustomer" class="display compact"></table>

<!--div class="toolbar">!RbO!</div-->
</body>
</html>
