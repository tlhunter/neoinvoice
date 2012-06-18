<h1 class="panelTitle">Add Ticket Category</h1>
<form method="post" action="ticketcategory/add_submit" name="ticketcategory_add" id="ticketcategory_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="description" cols="50" rows="4"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add Ticket Category" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('ticketcategory_add', 'detailPanel');
</script>
