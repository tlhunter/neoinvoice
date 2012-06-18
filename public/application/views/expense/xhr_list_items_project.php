<h1 class="panelTitle">Project Expenses: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($expenses)) {
?>
<form name="expense_delete_multiple" action="">
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="32">&nbsp;</th><th>Date</th><th align="center">Billable</th><th align="center">Invoice</th><th class='monetary'>Amount</th><th align="center">Expense Type</th></tr>
<?php
	$i = 0;
	foreach($expenses AS $expense) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		$billable = $expense['billable'] ? 'Yes' : 'No';
		echo "<tr class='$tr'>";
		echo "<td align='center'><input type='checkbox' name='{$expense['id']}' /></td>";
		echo "<td><a onclick=\"nw('detailPanel', 'expense/view/{$expense['id']}')\">" . date("M j, Y", strtotime($expense['date'])) . "</a></td>";
		echo "<td align='center'>$billable</td>";
		if ($expense['invoice_id']) {
			echo "<td align='center'><a onclick=\"selectInvoice({$expense['invoice_id']});\"># {$expense['invoice_id']}</a></td>";
		} else {
			echo "<td align='center'>N/A</td>";
		}
		echo "<td class='monetary'>\${$expense['amount']}</td>";
		echo "<td align='center'>{$expense['expensetype_name']}</td>";
		echo "</tr>\n";
	}
?>
<tr><td align="center">&nbsp;&nbsp;&nbsp;<img src="images/icons/arrow.png" alt="With Selected" /></td><td colspan="8"><a class="button" onClick="linkByCheckboxes('mainPanel', 'expense/delete_multiple/', 'expense_delete_multiple')">Delete Selected</a></td></tr>
</table>
</form>
<?php
	if ($total > $per_page) {
		echo "<div class='pagify'>Page";
		$total_pages = ceil($total / $per_page);
		for ($i = 0; $i < $total_pages; $i++) {
			$j = $i * $per_page;
			$p = $i + 1;
			echo sort_expense_page($j, $page, $project_id, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_expenses') . "</div>\n";
}