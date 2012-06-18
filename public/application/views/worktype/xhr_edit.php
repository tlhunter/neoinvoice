<h1 class="panelTitle">Edit Work Type: <?=$worktype['name']?></h1>
<form method="post" action="worktype/edit_submit/<?=$worktype['id']?>" name="worktype_edit" id="worktype_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Name</td><td><input name="name" value="<?=$worktype['name']?>" /></td></tr>
		<tr><td class="label-cell">Hourly Rate</td><td><input name="hourlyrate" value="<?=$worktype['hourlyrate']?>" /></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4"><?=$worktype['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Work Type" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('worktype_edit', 'detailPanel');
</script>
