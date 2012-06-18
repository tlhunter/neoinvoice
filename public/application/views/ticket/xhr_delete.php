<h1 class="panelTitle">Delete Ticket <?=$ticket['id']?>: <?=$ticket['name']?></h1>
<div class="notice">
	Are you sure you would like to delete this ticket?<br />
	<a onClick="nw('mainPanel', 'ticket/delete_submit/<?=$ticket['id']?>');" class="button">Delete Ticket</a>
	| <a onClick="selectTicket(<?=$ticket['id']?>);">Cancel</a>
</div>