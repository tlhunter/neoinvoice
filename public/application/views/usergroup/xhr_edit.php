<h1 class="panelTitle">Edit User Group: <?=$usergroup['name']?></h1>
<form method="post" action="usergroup/edit_submit/<?=$usergroup['id']?>" name="usergroup_edit" id="usergroup_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" value="<?=$usergroup['name']?>" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"><?=$usergroup['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save User Group" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('usergroup_edit', 'detailPanel');
</script>
