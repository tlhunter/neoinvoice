<h1 class="panelTitle">Edit Preferences</h1>
<form method="post" action="app/preferences_submit" name="preferences_edit" id="preferences_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell"><?=$this->lang->line('username')?></td><td><input name="username" value="<?=$user['username']?>" readonly maxlength="64" class="readonly" /></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('name')?></td><td><input name="name" value="<?=$user['name']?>" maxlength="64" class="textual" /></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('email')?></td><td><input name="email" value="<?=$user['email']?>" maxlength="64" class="textual" /></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('password')?></td><td><input name="password" maxlength="100" type="password" class="textual" /></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('password_confirm')?></td><td><input name="password2" maxlength="100" type="password" class="textual" /></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('language')?></td><td><?=dropdown_manual('language', array('english' => 'English', 'german' => 'Deutsch'), $preferences['language'])?></td></tr>
		<tr><td class="label-cell"><?=$this->lang->line('per_page')?></td><td><?=dropdown_manual('per_page', array('10' => '10', '20' => '20', '40' => '40'), $preferences['per_page'])?></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="<?=$this->lang->line('prefs_save')?>" class="button" /></td></tr>
	</table>
	<div class="notice">Some preferences changes will require a reload of the application.</div>
</form>
<script type="text/javascript">
	createAjaxForm('preferences_edit', 'mainPanel');
	document.preferences_edit.name.focus();
</script>