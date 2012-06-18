<h1 class="panelTitle">Record Time</h1>
<form method="post" action="segment/add_submit" name="segment_add" id="segment_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<?php if ($project_id) { ?>
		<input type="hidden" name="project_id" value="<?=$project_id?>" />
		<?php } else { ?>
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?> <span class="required">Required</span></td></tr>
		<?php } ?>
		<tr><td class="label-cell">Worktype</td><td><?=$worktype_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Billable</td><td><input name="billable" type="checkbox" checked="checked" /></td></tr>
		<tr><td class="label-cell">Date</td><td><?=calendar()?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Start Time</td><td><?=$time_start_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">End Time</td><td><?=$time_end_dropdown?> <span class="required">Required</span></td></tr>
<?php
if ($tickets) {
?>
		<tr><td class="label-cell">Related Ticket</td><td><select name="ticket_id"><option value=""></option>
<?php
	foreach($tickets AS $ticket) {
		echo "\t\t\t<option value='{$ticket['id']}'>{$ticket['name']}</option>\n";
	}
?>
		</select></td></tr>
<?php
}
?>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="6" class="textual"></textarea></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Record Time" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('segment_add', 'detailPanel');
	<?php if (!$project_id) { ?>
	document.segment_add.project_id.focus();
	<?php } else { ?>
	document.segment_add.worktype_id.focus();
	<?php } ?>
</script>
