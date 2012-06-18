<h1 class="panelTitle">Add Expense</h1>
<?php
if ($count_expensetypes) {
	if ($count_projects) {
?>
<form method="post" action="expense/add_submit" name="expense_add" id="expense_add">
	<table class="data-input-table">
		<?php if ($project_id) { ?>
		<input type="hidden" name="project_id" value="<?=$project_id?>" />
		<?php } else { ?>
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?></td></tr>
		<?php } ?>
		<tr><td class="label-cell">Expense Type</td><td><?=$expensetype_dropdown?></td></tr>
		<tr><td class="label-cell">Billable</td><td><input name="billable" type="checkbox" checked="checked" /></td></tr>
		<tr><td class="label-cell">Expense Amount</td><td>$ <input name="amount" type="text" maxlength="7" value="0.00" size="8" class="monetary textual" /></td></tr>
		<tr><td class="label-cell">Date</td><td><?=calendar()?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="6" class="textual"></textarea></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Save Expense" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('expense_add', 'mainPanel');
	<?php if (!$project_id) { ?>
	document.segment_add.project_id.focus();
	<?php } else { ?>
	document.segment_add.expensetype_id.focus();
	<?php } ?>
</script>
<?php
	} else {
		echo "<div class='notice'>" . $this->lang->line('notice_zero_projects') . "</div>\n";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_expensetypes') . "</div>\n";
}
