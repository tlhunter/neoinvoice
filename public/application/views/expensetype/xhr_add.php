<h1 class="panelTitle">Add Expense Type</h1>
<form method="post" action="expensetype/add_submit" name="expensetype_add" id="expensetype_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" /></td></tr>
		<tr><td class="label-cell">Taxable</td><td><?=dropdown_yes_no(1, 'taxable')?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add Expense Type" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('expensetype_add', 'detailPanel');
</script>
