<h1 class="panelTitle">Delete Time</h1>
<div class="notice">
	Are you sure you would like to delete this time segment?<br />
	<a onClick="nw('detailPanel', 'segment/delete_submit/<?=$segment['id']?>');" class="button">Delete Segment</a>
	| <a onClick="selectProject(<?=$segment['project_id']?>, '<?=$segment['id']?>');">Cancel</a>
</div>
<!-- update project pane, show add new item here? -->