<h1 class="panelTitle">Clients: Page <?=round($page/$per_page+1)?></h1>
<?php
if ($clients) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr>
	<th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th>
	<th><?=sort_client_head($page, $sort_column, 'name', 'Client');?></th>
	<th align='center'><?=sort_client_head($page, $sort_column, 'active', 'Active');?></th>
	<th align='center'><?=sort_client_head($page, $sort_column, 'email', 'Email');?></th>
	<th align='center'><?=sort_client_head($page, $sort_column, 'created', 'Created');?></th>
	<th align='center'><?=sort_client_head($page, $sort_column, 'modified', 'Modified');?></th>
</tr>
<?php
$i = 0;
foreach($clients AS $client) {
	$tr = $i++ % 2 ? 'even' : 'odd';
	$active = $client['active'] ? 'Yes' : '<span class="dim">No</span>';
	$email = $client['email'] ? "<a href='mailto:{$client['email']}'><img src='images/icons/email_small.png' /></a>" : '&nbsp;';
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onClick=\"nw('detailPanel', 'client/edit/{$client['id']}')\" title='Edit Client'><img src='images/icons/edit_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('detailPanel', 'client/delete/{$client['id']}')\" title='Delete Client'><img src='images/icons/delete_small.png' /></a></td>";
	echo "<td><a onclick=\"selectClient({$client['id']});\">{$client['name']}</a></td>";
	echo "<td align='center'>$active</td>";
	echo "<td align='center'>$email</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($client['created'])) . "</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($client['modified'])) . "</td>";
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
			echo sort_client_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_clients') . "<div>\n";
}