<h1 class="panelTitle">Delete Project: <?=$project['name']?></h1>
<div class="notice">
	Are you sure you would like to delete your project <?=$project['name']?>?<br />
	<a onClick="nw('detailPanel', 'project/delete_submit/<?=$project['id']?>'); showDashboard();" class="button">Delete Project</a>
	| <a onClick="selectProject(<?=$project['id']?>);">Cancel</a>
</div>