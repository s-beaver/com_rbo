<!doctype html>

<html>
<head>
<link rel="stylesheet"
	href="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.css" />
<!--id="theme"-->
<link rel="stylesheet"
	href="libraries/DataTables-1.10.6/media/css/jquery.dataTables_themeroller.css" />

<script
	src="libraries/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script src="libraries/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script
	src="libraries/DataTables-1.10.6/media/js/jquery.dataTables.min.js"></script>

<script src="components/com_rbo/views/invoices/tmpl/invoices.js"></script>

<style>
#neworder-form * label input {
	display: block;
}

#neworder-form * label select {
	display: block;
}

#neworder-form * input.text {
	margin-bottom: 12px;
	width: 95%;
	padding: .4em;
}

#neworder-form * select {
	margin-bottom: 12px;
	width: 95%;
	padding: .4em;
}

#neworder-form * textarea {
	margin-bottom: 12px;
	width: 95%;
	padding: .4em;
}
</style>

</head>
<body>

	<!-- $("#inv_num") $("#inv_date") $("#inv_cust") - array $("#inv_sum")
	$("#inv_status") $("#inv_rem") $("#inv_firm") $("#inv_manager")-->

	<div id="neworder-form" title="Новый счёт">
		<form id="neworder-form-form" method="post" action="">
			<fieldset>
				<table border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td><label for="inv_num">№</label> <input type="text"
							name="inv_num" id="inv_num"
							class="text ui-widget-content ui-corner-all" value="" /> <label
							for="inv_date">от</label> <input type="text" name="inv_date"
							id="inv_date" class="text ui-widget-content ui-corner-all"
							value="" /> <label for="inv_sum">Сумма</label> <input type="text"
							name="inv_sum" id="inv_sum"
							class="text ui-widget-content ui-corner-all" value="" /> <label
							for="inv_status">Статус</label> <input type="text"
							name="inv_status" id="inv_status"
							class="text ui-widget-content ui-corner-all" value="" /></td>
					</tr>
					<tr>
						<td><label for="inv_firm">Фирма</label> <select id="inv_firm"
							name="inv_firm" class="text ui-widget-content ui-corner-all">
								<option value="ип">ИП</option>
								<option value="ооо">ООО</option>
						</select> <label for="inv_manager">Менеджер</label> <select
							id="inv_manager" name="inv_manager"
							class="text ui-widget-content ui-corner-all">
								<option value="Алексей">Алексей</option>
								<option value="Аня">Аня</option>
								<option value="Володя">Володя</option>
								<option value="Николай">Николай</option>
						</select></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>

	<div id="dialog-confirm" title="Удалить счёт?">Счёт будет удален.
		Продолжить?</div>

	<table id="TableInv" class="display" cellspacing="0" width="100%">
	</table>
</body>
</html>