<h1 class="panelTitle">Add Work Type</h1>
<form method="post" action="worktype/add_submit" name="worktype_add" id="worktype_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" /></td></tr>
		<tr><td class="label-cell">Hourly Rate</td><td><input name="hourlyrate" value="40.00" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add Work Type" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('worktype_add', 'detailPanel');
</script>
