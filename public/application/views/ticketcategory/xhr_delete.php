<h1 class="panelTitle">Delete Ticket: <?=$ticketcategory['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your Ticket Category <?=$ticketcategory['name']?>?<br />
	<a onClick="nw('detailPanel', 'ticketcategory/delete_submit/<?=$ticketcategory['id']?>');" class="button">Delete Ticket Category</a>
	| <a onClick="nw('detailPanel', 'ticketcategory/add');">Cancel</a>
</div>