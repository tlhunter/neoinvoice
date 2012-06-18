<h1 class="panelTitle">Tickets: Page <?=round($page/$per_page+1)?></h1>
<?php
if ($tickets) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr>
	<th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th>
	<th><?=sort_ticket_head($page, $sort_column, 'name', 'Name');?></th>
	<th><?=sort_ticket_head($page, $sort_column, 'project_id', 'Project');?></th>
	<th><?=sort_ticket_head($page, $sort_column, 'assigned_user_id', 'User');?></th>
	<th><?=sort_ticket_head($page, $sort_column, 'assigned_usergroup_id', 'User Group');?></th>
	<th><?=sort_ticket_head($page, $sort_column, 'ticket_category_id', 'Category');?></th>
	<th><?=sort_ticket_head($page, $sort_column, 'ticket_stage_id', 'Stage');?></th>
	<th align='center'><?=sort_ticket_head($page, $sort_column, 'due', 'Due Date');?></th>
</tr>
<?php
$i = 0;
foreach($tickets AS $ticket) {
	$category = $ticket['ticket_category_name'] ? : 'N/A';
	$stage = $ticket['ticket_stage_name'] ? : 'N/A';
	$tr = $i++ % 2 ? 'even' : 'odd';
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onClick=\"nw('mainPanel', 'ticket/edit/{$ticket['id']}')\" title='Edit Ticket'><img src='images/icons/edit_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('mainPanel', 'ticket/delete/{$ticket['id']}')\" title='Delete Ticket'><img src='images/icons/delete_small.png' /></a></td>";
	echo "<td><a onclick=\"selectTicket({$ticket['id']}, '{$ticket['name']}');\">{$ticket['name']}</a></td>";
	echo "<td>{$ticket['project_name']}</td>";
	echo "<td>{$ticket['user_name']}</td>";
	echo "<td>{$ticket['usergroup_name']}</td>";
	echo "<td>$category</td>";
	echo "<td>$stage</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format_short'), strtotime($ticket['due'])) . "</td>";
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
			echo sort_ticket_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_tickets') . "<div>\n";
}