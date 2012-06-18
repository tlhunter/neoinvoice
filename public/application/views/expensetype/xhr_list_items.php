<h1 class="panelTitle">Expense Types: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($expensetypes)) {
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th>Name</th><th>Taxable</th><th class="monetary">Income</th><th align="right">Uses</th><th>Content</th></tr>
<?php
	$i = 0;
	foreach($expensetypes AS $expensetype) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		echo "<tr class='$tr'>";
		echo "<td>&nbsp;</td>";
		echo "<td><a onClick=\"nw('detailPanel', 'expensetype/edit/{$expensetype['id']}')\" title='Edit Expense Type'><img src='images/icons/edit_small.png' /></a></td>";
		echo "<td><a onClick=\"nw('detailPanel', 'expensetype/delete/{$expensetype['id']}')\" title='Delete Expense Type'><img src='images/icons/delete_small.png' /></a></td>";
		echo "<td>{$expensetype['name']}</td>";
		echo "<td>" . yes_no($expensetype['taxable']) . "</td>";
		echo "<td class='monetary'>$ " . number_format($expensetype['amount_sum'], 2) . "</td>";
		echo "<td align='right'>{$expensetype['expense_count']}</td>";
		echo "<td>{$expensetype['content']}</td>";
		echo "</tr>\n";
	}
?>
</table>
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
	echo "<div class='notice'>" . $this->lang->line('notice_zero_expensetypes') . "</div>\n";
}
?>
<div style="padding: 10px;"><a onClick="nw('detailPanel', 'expensetype/add');">Add Expense Type</a></div>