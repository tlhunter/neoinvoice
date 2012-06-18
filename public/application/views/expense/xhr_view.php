<h1 class="panelTitle">View Expense</h1>
<div class="mini_toolbar"><a onClick="nw('detailPanel', 'expense/edit/<?=$expense['id']?>')" title="Edit"><img src="images/icons/edit.png" /></a><a onClick="nw('detailPanel', 'expense/delete/<?=$expense['id']?>')" title="Delete"><img src="images/icons/delete.png" /></a></div>
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr><td class="label-cell">Project</td><td><?=$expense['project_name']?></td></tr>
	<tr><td class="label-cell">Amount</td><td><?=$expense['amount']?></td></tr>
	<tr><td class="label-cell">Expense Type</td><td><?=$expense['expensetype_name']?></td></tr>
	<tr><td class="label-cell">Invoice</td><td><?=$expense['invoice_id'] ? "#".$expense['invoice_id'] : 'N/A'; ?></td></tr>
	<tr><td class="label-cell">Billable</td><td><?=$expense['billable'] ? 'Yes' : 'No'; ?></td></tr>
	<tr><td class="label-cell" valign="top">Notes</td><td><?=$expense['content']?></td></tr>
	<tr><td class="label-cell">Date</td><td><?=$expense['date']?></td></tr>
</table>
