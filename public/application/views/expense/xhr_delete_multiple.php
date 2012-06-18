<h1 class="panelTitle">Delete <?=$count?> Expenses</h1>
<div class="notice">
	Are you sure you would like to delete <?=$count?> Expenses?<br />
	<a onClick="nw('mainPanel', 'expense/delete_multiple_submit/<?=$expense_ids?>');" class="button">Delete Expenses</a>
	| <a onClick="showDashboard();">Cancel</a>
</div>