<h1 class="panelTitle">Edit Expense Type: <?=$expensetype['name']?></h1>
<form method="post" action="expensetype/edit_submit/<?=$expensetype['id']?>" name="expensetype_edit" id="expensetype_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" value="<?=$expensetype['name']?>" /></td></tr>
		<tr><td class="label-cell">Taxable</td><td><?=dropdown_yes_no($expensetype['taxable'], 'taxable')?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"><?=$expensetype['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Expense Type" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('expensetype_edit', 'detailPanel');
</script>
