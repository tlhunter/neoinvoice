<h1 class="panelTitle">Delete Invoice: <?=$invoice['name']?></h1>
<div class="notice">
	Are you sure you would like to delete invoice <strong><?=$invoice['name']?></strong>? Doing so will delete all associated payments and unassign their time segments.<br />
	<a onClick="nw('mainPanel', 'invoice/delete_submit/<?=$invoice['id']?>');" class="button">Delete Invoice</a>
	| <a onClick="showDashboard();">Cancel</a>
</div>