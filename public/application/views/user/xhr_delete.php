<h1 class="panelTitle">Delete Teammate: <?=$user['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your Teammate <?=$user['name']?>?<br />
	Deleting this Teammate will also delete all of their time segments!<br />
	<a onClick="nw('detailPanel', 'user/delete_submit/<?=$user['id']?>');" class="button">Delete Teammate</a>
	| <a onClick="selectUser(<?=$user['id']?>);">Cancel</a>
</div>