<h1 class="panelTitle">Edit Ticket Stage: <?=$ticketstage['name']?></h1>
<form method="post" action="ticketstage/edit_submit/<?=$ticketstage['id']?>" name="ticketstage_edit" id="ticketstage_edit">
	<table class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" value="<?=$ticketstage['name']?>" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Ticket Category</td><td><?=$ticket_category_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Closed Status</td><td><?=checkbox('closed', $ticketstage['closed'])?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" cols="50" rows="4"><?=$ticketstage['description']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Ticket Stage" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticketstage_edit', 'detailPanel');
</script>
