<h1 class="panelTitle">Ticket <?=$ticket['id']?>: <?=$ticket['name']?></h1>
<?php if ($toolbar) { ?>
<div class="mini_toolbar"><a onClick="nw('mainPanel', 'ticket/edit/<?=$ticket['id']?>')" title="Edit"><img src="images/icons/ticket_edit.png" alt="" /></a><a onClick="nw('mainPanel', 'ticket/delete/<?=$ticket['id']?>')" title="Delete"><img src="images/icons/ticket_delete.png" alt="" /></a></div>
<?php } ?>

<table class="data-input-table">
	<tr><td class="label-cell">Ticket ID</td><td><?=$ticket['id']?></td></tr>
	<tr><td class="label-cell">Ticket Name</td><td><?=$ticket['name']?></td></tr>
	<tr><td class="label-cell">Project</td><td><?=$ticket['project_name']?></td></tr>
	<tr><td class="label-cell">Assigned User</td><td><?=$ticket['user_name'] ? : '<em>N/A</em>'?></td></tr>
	<tr><td class="label-cell">Assigned User Group</td><td><?=$ticket['usergroup_name'] ? : '<em>N/A</em>'?></td></tr>
	<tr><td class="label-cell">Ticket Category</td><td><?=$ticket['ticket_category_name'] ? : '<em>N/A</em>'?></td></tr>
	<tr><td class="label-cell">Ticket Stage</td><td><?=$ticket['ticket_stage_name'] ? : '<em>N/A</em>'?></td></tr>
	<tr><td class="label-cell">Created</td><td><?=date($this->lang->line('date_format'), strtotime($ticket['created']))?> (<?=time_ago(strtotime($ticket['created']))?> ago).</td></tr>
	<tr><td class="label-cell">Last Activity</td><td><?=date($this->lang->line('date_format'), strtotime($ticket['modified']))?> (<?=time_ago(strtotime($ticket['modified']))?> ago).</td></tr>
	<?php if ($ticket['due']) { ?>
	<tr><td class="label-cell">Deadline</td><td><?=date($this->lang->line('date_format_short'), strtotime($ticket['due']))?></td></tr>
	<?php } ?>
	<?php if ($ticket['closed']) { ?>
	<tr><td class="label-cell">Closed</td><td><?=date($this->lang->line('date_format_short'), strtotime($ticket['closed']))?></td></tr>
	<?php } ?>
	<tr><td class="label-cell">Notes</td><td><?=$ticket['description']?></td></tr>
</table>
