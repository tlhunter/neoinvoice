<h1 class="panelTitle">Add Project</h1>
<?php
if ($count) {
?>
<form method="post" action="project/add_submit" name="project_add" id="project_add">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Project Name</td><td><input name="name" class="textual" /> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Client</td><td><?=$client_dropdown?> <span class="required">Required</span></td></tr>
		<tr><td class="label-cell">Status</td><td><select name="active" class="textual"><option value="1">Active</option><option value="0">Inactive</option></select></td></tr>
		<tr><td class="label-cell" valign="top">Notes</td><td><textarea name="content" cols="50" rows="4" class="textual"></textarea></td></tr>
		<tr><td class="label-cell">&nbsp;</td><td><input type="submit" value="Add Project" class="button" /></td></tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('project_add', 'mainPanel');
	document.project_add.name.focus();
</script>
<?php
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_clients') . "</div>\n";
}
