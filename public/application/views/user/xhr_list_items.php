<h1 class="panelTitle">Teammates: Page <?=round($page/$per_page+1)?></h1>
<?php
if ($users) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr>
	<th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th>
	<th><?=sort_user_head($page, $sort_column, 'name', 'User');?></th>
	<th align='center'><?=sort_user_head($page, $sort_column, 'active', 'Active');?></th>
	<th align='center'><?=sort_user_head($page, $sort_column, 'created', 'Created');?></th>
	<th align='center'><?=sort_user_head($page, $sort_column, 'modified', 'Modified');?></th>
</tr>
<?php
$i = 0;
foreach($users AS $user) {
	$tr = $i++ % 2 ? 'even' : 'odd';
	$active = $user['active'] ? 'Yes' : '<span class="dim">No</span>';
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onClick=\"nw('detailPanel', 'user/edit/{$user['id']}')\" title='Edit User'><img src='images/icons/edit_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('detailPanel', 'user/delete/{$user['id']}')\" title='Delete User'><img src='images/icons/delete_small.png' /></a></td>";
	echo "<td><a onclick=\"selectUser({$user['id']});\">{$user['name']}</a></td>";
	echo "<td align='center'>$active</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($user['created'])) . "</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($user['modified'])) . "</td>";
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
			echo sort_user_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_users') . "<div>\n";
}