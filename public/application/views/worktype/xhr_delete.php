<h1 class="panelTitle">Delete Work Type: <?=$worktype['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your Work Type <?=$worktype['name']?>?<br />
	This will delete all of your time segments using this Work Type!<br />
	<a onClick="nw('detailPanel', 'worktype/delete_submit/<?=$worktype['id']?>');" class="button">Delete Work Type</a>
	| <a onClick="nw('detailPanel', 'worktype/add');">Cancel</a>
</div>