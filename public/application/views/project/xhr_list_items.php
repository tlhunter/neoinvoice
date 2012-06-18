<h1 class="panelTitle">Projects: Page <?=round($page/$per_page+1)?></h1>
<?php
if ($projects) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr>
	<th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th>
	<th><?=sort_project_head($page, $sort_column, 'name', 'Project');?></th>
	<th align='center'><?=sort_project_head($page, $sort_column, 'active', 'Active');?></th>
	<th align='center'>Client</th>
	<th align='center'><?=sort_project_head($page, $sort_column, 'created', 'Created');?></th>
	<th align='center'><?=sort_project_head($page, $sort_column, 'modified', 'Modified');?></th>
</tr>
<?php
$i = 0;
foreach($projects AS $project) {
	$tr = $i++ % 2 ? 'even' : 'odd';
	$active = $project['active'] ? 'Yes' : '<span class="dim">No</span>';
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onClick=\"nw('detailPanel', 'project/edit/{$project['id']}')\" title='Edit Project'><img src='images/icons/edit_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('detailPanel', 'project/delete/{$project['id']}')\" title='Delete Project'><img src='images/icons/delete_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('detailPanel', 'expense/list_by_project/{$project['id']}')\" title='View Expenses'><img src='images/icons/money_small.png' /></a></td>";
	echo "<td><a onclick=\"selectProject({$project['id']}, '{$project['name']}');\">{$project['name']}</a></td>";
	echo "<td align='center'>$active</td>";
	echo "<td align='center'>{$project['client_name']}</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($project['created'])) . "</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($project['modified'])) . "</td>";
	echo "</tr>\n";
}
?>
</table>
<?php
	if ($total > $per_page) {
		echo "<div class='pagify'>Page";
		$total_pages = ceil($total / $per_page);
		for ($i = 0; $i < $total_pages; $i++) {
			$j = $i * $per_page;
			$p = $i + 1;
			echo sort_project_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_projects') . "<div>\n";
}