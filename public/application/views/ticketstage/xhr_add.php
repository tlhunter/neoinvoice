<h1 class="panelTitle">Add Ticket Stages</h1>
<form method="post" action="ticketstage/add_submit" name="ticketstage_add" id="ticketstage_add">
	<table class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Ticket Category</td><td><?=$ticket_category_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Closed Status</td><td><input name="closed" type="checkbox" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" cols="50" rows="4"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add Ticket Stage" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticketstage_add', 'detailPanel');
</script>
