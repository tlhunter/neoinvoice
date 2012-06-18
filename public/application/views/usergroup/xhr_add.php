<h1 class="panelTitle">Add User Group</h1>
<form method="post" action="usergroup/add_submit" name="usergroup_add" id="usergroup_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add User Group" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('usergroup_add', 'detailPanel');
</script>
