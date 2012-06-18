<h1 class="panelTitle">Add Teammate</h1>
<form method="post" action="user/add_submit" name="user_add" id="user_add">
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr><td class="label-cell">Full Name</td><td><input name="name" maxlength="64" class="textual"> <span class="required">Required</span></td></tr>
	<tr><td class="label-cell">Username</td><td><input name="username" maxlength="32" class="textual"> <span class="required">Required</span></td></tr>
	<tr><td class="label-cell">Temp Password</td><td><input name="password" value="<?=$rand_pass?>" maxlength="100" class="textual"> <span class="required">Required, Should be changed after login</span></td></tr>
	<tr><td class="label-cell">Email Address</td><td><input name="email" maxlength="64" class="textual"></td></tr>
	<tr><td class="label-cell">User Group</td><td><?=$usergroup_dropdown?></td></tr>
	<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Create Teammate" class="button" /></td></tr>
</table>
</form>
<br />
<br />
<p>*<em>With your current plan, you are able to add <strong><?=$company['service']['pref_max_user'] - $company['user_count']?></strong> more teammates.</em></p>
<script type="text/javascript">
	createAjaxForm('user_add', 'mainPanel');
	document.user_add.name.focus();
</script>