<h1 class="panelTitle">Add Client</h1>
<form method="post" action="client/add_submit" name="client_add" id="client_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Client Name</td><td><input name="name" class="textual" maxlength="64" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Client Email</td><td><input name="email" class="textual" maxlength="64" /></td></tr>
		<tr><td class="label-cell">Client Phone</td><td><input name="phone" class="textual" maxlength="16" /></td></tr>
		<tr><td class="label-cell">Client Address</td><td><input name="address" class="textual" maxlength="64" /></td></tr>
		<tr><td class="label-cell">Status</td><td><select name="active" class="textual"><option value="1">Active</option><option value="0">Inactive</option></select></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Add Client" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('client_add', 'mainPanel');
	document.client_add.name.focus();
</script>