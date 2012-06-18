<h1 class="panelTitle">Edit Expense</h1>
<form method="post" action="expense/edit_submit/<?=$expense['id']?>" name="expense_edit" id="expense_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?></td></tr>
		<tr><td class="label-cell">Expense Type</td><td><?=$expensetype_dropdown?></td></tr>
		<tr><td class="label-cell">Billable</td><td><?=checkbox('billable', $expense['billable'])?></td></tr>
		<tr><td class="label-cell">Expense Amount</td><td>$<input name="amount" value="<?=$expense['amount']?>" class="textual" /></td></tr>
		<tr><td class="label-cell">Date</td><td><?=calendar('date', $expense['date'])?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="6" class="textual"><?=$expense['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Expense" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('expense_edit', 'detailPanel');
</script>
