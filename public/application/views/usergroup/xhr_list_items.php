<h1 class="panelTitle">User Groups: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($usergroups)) {
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th>Name</th><th>Content</th></tr>
<?php
	$i = 0;
	foreach($usergroups AS $usergroup) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		echo "<tr class='$tr'>";
		echo "<td>&nbsp;</td>";
		echo "<td><a onClick=\"nw('detailPanel', 'usergroup/edit/{$usergroup['id']}')\" title='Edit User Group'><img src='images/icons/edit_small.png' /></a></td>";
		echo "<td><a onClick=\"nw('detailPanel', 'usergroup/delete/{$usergroup['id']}')\" title='Delete User Group'><img src='images/icons/delete_small.png' /></a></td>";
		echo "<td>{$usergroup['name']}</td>";
		echo "<td>{$usergroup['content']}</td>";
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
			echo sort_usergroup_page($j, $page, $project_id, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_usergroups') . "</div>\n";
}
?>
<div style="padding: 10px;"><a onClick="nw('detailPanel', 'usergroup/add');">Add User Group</a></div>