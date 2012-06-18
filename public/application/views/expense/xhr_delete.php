<h1 class="panelTitle">Delete Expense</h1>
<div class="notice">
	Are you sure you would like to delete this Expense?<br />
	<a onClick="nw('detailPanel', 'expense/delete_submit/<?=$expense['id']?>');" class="button">Delete Expense</a>
	| <a onClick="selectProject(<?=$expense['project_id']?>);">Cancel</a>
</div>