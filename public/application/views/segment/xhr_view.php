<h1 class="panelTitle">Recorded Time Segment</h1>
<div class="mini_toolbar"><a onClick="nw('detailPanel', 'segment/edit/<?=$segment['id']?>')" title="Edit"><img src="images/icons/edit.png" /></a><a onClick="nw('detailPanel', 'segment/delete/<?=$segment['id']?>')" title="Delete"><img src="images/icons/delete.png" /></a></div>
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr><td class="label-cell">Project</td><td><?=$segment['project_name']?></td></tr>
	<tr><td class="label-cell">Contributor</td><td><?=$segment['user_name']?></td></tr>
	<tr><td class="label-cell">Type of Work</td><td><?=$segment['worktype_name']?></td></tr>
	<tr><td class="label-cell">Invoice</td><td><?=$segment['invoice_id'] ? "#".$segment['invoice_id'] : 'N/A'; ?></td></tr>
	<tr><td class="label-cell">Billable</td><td><?=$segment['billable'] ? 'Yes' : 'No'; ?></td></tr>
	<tr><td class="label-cell">Date</td><td><?=$segment['date']?></td></tr>
	<tr><td class="label-cell">Time</td><td><?=$segment['duration']?></td></tr>
	<tr><td class="label-cell" valign="top">Notes</td><td><?=$segment['content']?></td></tr>
</table>
