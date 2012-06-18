<h1 class="panelTitle">Edit Ticket <?=$ticket['id']?>: <?=$ticket['name']?></h1>
<form method="post" action="ticket/edit_submit/<?=$ticket['id']?>" name="ticket_edit" id="ticket_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Ticket Name</td><td><input name="name" class="textual" maxlength="64" value="<?=$ticket['name']?>" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Assigned User</td><td><?=$user_dropdown?></td></tr>
		<tr><td class="label-cell">Assigned Usergroup</td><td><?=$usergroup_dropdown?></td></tr>
		<tr><td class="label-cell">Ticket Category</td><td><?=$ticket_category_dropdown?></td></tr>
		<tr><td class="label-cell">Ticket Stage</td><td><span id="ticket_stage_container"><?=$ticket_stage_dropdown?></span></td></tr>
		<tr><td class="label-cell">Deadline</td><td><?=calendar('due', $ticket['due'])?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" class="textual" rows="6" cols="40"><?=$ticket['description']?></textarea></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Save Ticket" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticket_edit', 'mainPanel');
	document.ticket_edit.name.focus();
	
	$('dropdown_ticket_category_id').addEvent('change',function(event) {
		var selected_category = this.value;
		var req = new Request.HTML({
			method: 'get',
			url: 'ticket/ticket_stage_dropdown/' + selected_category,
			onRequest: function() {  },
			update: $('ticket_stage_container'),
			onComplete: function(response) { }
		}).send();
	});
</script>
