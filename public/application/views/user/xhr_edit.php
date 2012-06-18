<h1 class="panelTitle">Edit Teammate: <?=$user['name']?></h1>
<form method="post" action="user/edit_submit/<?=$user['id']?>" name="user_edit" id="user_edit">
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr><td class="label-cell">Full Name</td><td><input name="name" value="<?=$user['name']?>" maxlength="64" class="textual"> <span class="required">Required</span></td></tr>
	<tr><td class="label-cell">Username</td><td><input name="username" readonly="readonly" value="<?=$user['username']?>" class="readonly"></td></tr>
	<tr><td class="label-cell">Active</td><td><?=dropdown_yes_no($user['active'], 'active')?></td></tr>
	<tr><td class="label-cell">Email Address</td><td><input name="email" maxlength="64" value="<?=$user['email']?>" class="textual"></td></tr>
	<tr><td class="label-cell">User Group</td><td><?=$usergroup_dropdown?></td></tr>
	<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Teammate" class="button" /></td></tr>
</table>
</form>
<script type="text/javascript">
	createAjaxForm('user_edit', 'detailPanel');
</script>