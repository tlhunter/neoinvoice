<h1 class="panelTitle">Project: <?=$project['name']?></h1>
<h2 style="float: left; margin: 10px 0 0 10px;"><?=$project['name']?></h2>
<div class="mini_toolbar"><a onClick="nw('detailPanel', 'project/edit/<?=$project['id']?>')" title="Edit"><img src="images/icons/project_edit.png" /></a><a onClick="nw('detailPanel', 'project/delete/<?=$project['id']?>')" title="Delete"><img src="images/icons/project_delete.png" alt="Delete Project" /></a></div>

<div style="clear: both;"></div>
<div class="panelContentPad">
	<p>Created on <?=date($this->lang->line('date_format'), strtotime($project['created']))?> (<?=time_ago(strtotime($project['created']))?> ago).</p>
	<p>Last modified on <?=date($this->lang->line('date_format'), strtotime($project['modified']))?> (<?=time_ago(strtotime($project['modified']))?> ago).</p>
	<p>Your company has recorded a total of <?=$project['total_time']?> hours of work for this project over <?=$project['segment_count']?> time segments.</p>
</div>