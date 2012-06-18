<h1 class="panelTitle">Delete Ticket Stage: <?=$ticketstage['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your Ticket Stage <?=$ticketstage['name']?>?<br />
	<a onClick="nw('detailPanel', 'ticketstage/delete_submit/<?=$ticketstage['id']?>');" class="button">Delete Ticket Stage</a>
	| <a onClick="nw('detailPanel', 'ticketstage/add');">Cancel</a>
</div>