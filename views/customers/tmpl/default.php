<!doctype html>

<html>
<head>
    <?php include RBO_PATH . '/views/header.head.links.php' ?>
    <script src="<?php echo JRBO_PATH ?>/library/lib.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RbForm.js"></script>
    <script src="<?php echo JRBO_PATH ?>/models/js/RefCustomers.js"></script>
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
                    <td><label for="cust_data.cust_addr">Адрес</label></td>
                    <td><input type="text" name="cust_data.cust_addr" id="cust_data.cust_addr" style='width: 500px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="cust_data.cust_nonds">Без НДС</label><input type="checkbox"
                                                                                           name="cust_data.cust_nonds"
                                                                                           id="cust_data.cust_nonds">
                        <label for="rbo_cust.cust_phone">&nbsp;Тел.</label> <input
                            type="text" name="rbo_cust.cust_phone" id="rbo_cust.cust_phone" style='width: 200px'/>
                        <label for="rbo_cust.cust_email">&nbsp;@</label> <input type="text" name="rbo_cust.cust_email"
                                                                                id="rbo_cust.cust_email"
                                                                                style='width: 200px'/></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="2"><label for="cust_data.cust_inn">&nbsp;ИНН</label><input type="text"
                                                                                           name="cust_data.cust_inn"
                                                                                           id="cust_data.cust_inn"
                                                                                           style='width: 150px'/> <label
                            for="cust_data.cust_kpp">&nbsp;КПП</label><input
                            type="text" name="cust_data.cust_kpp" id="cust_data.cust_kpp" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="cust_data.cust_okpo">ОКПО</label><input type="text"
                                                                                       name="cust_data.cust_okpo"
                                                                                       id="cust_data.cust_okpo"
                                                                                       style='width: 150px'/> <label
                            for="cust_data.cust_ogrn">&nbsp;ОГРН</label>
                        <input type="text" name="cust_data.cust_ogrn" id="cust_data.cust_ogrn" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td><label for="cust_data.cust_bank">Банк</label></td>
                    <td><input type="text" name="cust_data.cust_bank" id="cust_data.cust_bank" style='width: 500px'/></td>
                </tr>
                <tr>
                    <td><label for="cust_data.cust_bik">БИК</label></td>
                    <td><input type="text" name="cust_data.cust_bik" id="cust_data.cust_bik" style='width: 150px'/></td>
                </tr>
                <tr>
                    <td colspan="2"><label for="cust_data.cust_rch">р/сч</label><input type="text"
                                                                                      name="cust_data.cust_rch"
                                                                                      id="cust_data.cust_rch"
                                                                                      style='width: 250px'/> <label
                            for="cust_data.cust_kch">к/сч</label><input
                            type="text" name="cust_data.cust_kch" id="cust_data.cust_kch" style='width: 250px'/></td>
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
