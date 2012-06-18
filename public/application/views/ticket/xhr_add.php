<h1 class="panelTitle">Add Ticket</h1>
<form method="post" action="ticket/add_submit" name="ticket_add" id="ticket_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Ticket Name</td><td><input name="name" class="textual" maxlength="64" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Project</td><td><?=$project_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Assigned User</td><td><?=$user_dropdown?></td></tr>
		<tr><td class="label-cell">Assigned Usergroup</td><td><?=$usergroup_dropdown?></td></tr>
		<tr><td class="label-cell">Ticket Category</td><td><?=$ticket_category_dropdown?></td></tr>
		<tr><td class="label-cell">Ticket Stage</td><td><span id="ticket_stage_container">Select a Category</span></td></tr>
		<tr><td class="label-cell">Deadline</td><td><?=calendar('due', strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "+1 month"))?></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" class="textual" rows="6" cols="40"></textarea></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Add Ticket" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticket_add', 'mainPanel');
	document.ticket_add.name.focus();
	
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
