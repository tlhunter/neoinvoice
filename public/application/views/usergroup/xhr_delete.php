<h1 class="panelTitle">Delete User Group: <?=$usergroup['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your User Group <?=$usergroup['name']?>?<br />
	<a onClick="nw('detailPanel', 'usergroup/delete_submit/<?=$usergroup['id']?>');" class="button">Delete User Group</a>
	| <a onClick="nw('detailPanel', 'usergroup/add');">Cancel</a>
</div>