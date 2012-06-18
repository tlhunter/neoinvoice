<h1 class="panelTitle">Browse Client Projects</h1>
<?php
if ($projects) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr><th width="2">&nbsp;</th><th>Project</th><th>Created</th><th>Modified</th></tr>
<?php
$i = 0;
foreach($projects AS $project) {
	$tr = $i++ % 2 ? 'even' : 'odd';
	if (!$project['name'])
		$tr .= " inactive";
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onclick=\"selectProject({$project['id']});\">{$project['name']}</a></td>";
	echo "<td>" . date($this->lang->line('date_format'), strtotime($project['created'])) . "</td>";
	echo "<td>" . date($this->lang->line('date_format'), strtotime($project['modified'])) . "</td>";
	echo "</tr>\n";
}
?>
</table>
<?php
} else {
	echo "<div class='notice message_replace_table'>" . $this->lang->line('notice_client_zero_projects') . "<div>\n";
}