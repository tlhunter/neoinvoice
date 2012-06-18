<h1 class="panelTitle">Delete Expense Type: <?=$expensetype['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your Expense Type <?=$expensetype['name']?>?<br />
	This will delete all of your expenses using this Type! (give option to reassign Expense Type instead of deleting).<br />
	<a onClick="nw('detailPanel', 'expensetype/delete_submit/<?=$expensetype['id']?>');" class="button">Delete Expense Type</a>
	| <a onClick="nw('detailPanel', 'expensetype/add');">Cancel</a>
</div>