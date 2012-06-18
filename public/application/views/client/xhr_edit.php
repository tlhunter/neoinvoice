<h1 class="panelTitle">Edit Client</h1>
<form method="post" action="client/edit_submit/<?=$client['id']?>" name="client_edit" id="client_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Client Name</td><td><input name="name" value="<?=$client['name']?>" maxlength="64" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Client Email</td><td><input name="email" value="<?=$client['email']?>" maxlength="64" /></td></tr>
		<tr><td class="label-cell">Client Phone</td><td><input name="phone" value="<?=$client['phone']?>" maxlength="16" /></td></tr>
		<tr><td class="label-cell">Client Address</td><td><input name="address" value="<?=$client['address']?>" maxlength="64" /></td></tr>
		<tr><td class="label-cell">Status</td><td><select name="active"><option value="1" <?php if ($client['active']) echo "selected";?>>Active</option><option value="0"<?php if (!$client['active']) echo "selected";?>>Inactive</option></select></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="Save Client" class="button" /> | <a onClick="selectClient(<?=$client['id']?>);">Cancel</a></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('client_edit', 'detailPanel');
</script>