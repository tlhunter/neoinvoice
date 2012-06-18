<h1 class="panelTitle">Edit Project: <?=$project['name']?></h1>
<form method="post" action="project/edit_submit/<?=$project['id']?>" name="project_edit" id="project_edit">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Project Name</td><td><input name="name" value="<?=$project['name']?>" class="textual" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Client</td><td><?=$client_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Status</td><td><select name="active" class="textual"><option value="1" <?php if ($project['active']) echo "selected";?>>Active</option><option value="0"<?php if (!$project['active']) echo "selected";?>>Inactive</option></select></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4" class="textual"><?=$project['content']?></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Save Project" class="button" /> | <a onClick="selectProject(<?=$project['id']?>, '<?=$project['name']?>');">Cancel</a></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('project_edit', 'detailPanel');
</script>
