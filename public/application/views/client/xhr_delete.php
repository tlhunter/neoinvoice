<h1 class="panelTitle">Delete Client: <?=$client['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your client <?=$client['name']?>?<br />
	<a onClick="nw('detailPanel', 'client/delete_submit/<?=$client['id']?>'); showDashboard();" class="button">Delete Client</a>
	| <a onClick="selectClient(<?=$client['id']?>);">Cancel</a>
</div>