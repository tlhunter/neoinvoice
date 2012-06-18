<h1 class="panelTitle">Edit Recorded Time</h1>
<form method="post" action="segment/edit_submit/<?=$segment['id']?>" name="segment_edit" id="segment_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?></td></tr>
		<tr><td class="label-cell">Worktype</td><td><?=$worktype_dropdown?></td></tr>
		<tr><td class="label-cell">Billable</td><td><?=checkbox('billable', $segment['billable'])?></td></tr>
		<tr><td class="label-cell">Date</td><td><?=calendar('date', $segment['date'])?></td></tr>
		<tr><td class="label-cell">Start Time</td><td><?=$time_start_dropdown?></td></tr>
		<tr><td class="label-cell">End Time</td><td><?=$time_end_dropdown?></td></tr>
<?php
if ($tickets) {
?>
		<tr><td class="label-cell">Related Ticket</td><td><select name="ticket_id"><option value=""></option>
<?php
	foreach($tickets AS $ticket) {
		$selected = '';
		if ($ticket['id'] == $segment['ticket_id']) {
			$selected = ' selected=\'selected\'';
		}
		echo "\t\t\t<option value='{$ticket['id']}'$selected>{$ticket['name']}</option>\n";
	}
?>
		</select></td></tr>
<?php
}
?>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="6"><?=$segment['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Time" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('segment_edit', 'detailPanel');
</script>
