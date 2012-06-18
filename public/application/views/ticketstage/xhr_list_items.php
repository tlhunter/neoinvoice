<h1 class="panelTitle">Ticket Stages: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($ticketstages)) {
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th>Name</th><th>Ticket Category</th><th>Content</th></tr>
<?php
	$i = 0;
	foreach($ticketstages AS $ticketstage) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		echo "<tr class='$tr'>";
		echo "<td>&nbsp;</td>";
		echo "<td><a onClick=\"nw('detailPanel', 'ticketstage/edit/{$ticketstage['id']}')\" title='Edit Ticket Stage'><img src='images/icons/edit_small.png' /></a></td>";
		echo "<td><a onClick=\"nw('detailPanel', 'ticketstage/delete/{$ticketstage['id']})\" title='Delete Ticket Stage'><img src='images/icons/delete_small.png' /></a></td>";
		echo "<td>{$ticketstage['name']}</td>";
		echo "<td>{$ticketstage['ticket_category_name']}</td>";
		echo "<td>{$ticketstage['description']}</td>";
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
			echo sort_ticketstage_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_ticketstages') . "</div>\n";
}
?>
<div style="padding: 10px;"><a onClick="nw('detailPanel', 'ticketstage/add');">Add Ticket Stage</a></div>