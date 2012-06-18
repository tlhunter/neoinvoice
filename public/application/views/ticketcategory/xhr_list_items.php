<h1 class="panelTitle">Ticket Categories: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($ticketcategories)) {
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th>Name</th><th>Content</th></tr>
<?php
	$i = 0;
	foreach($ticketcategories AS $ticketcategory) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		echo "<tr class='$tr'>";
		echo "<td>&nbsp;</td>";
		echo "<td><a onClick=\"nw('detailPanel', 'ticketcategory/edit/{$ticketcategory['id']}')\" title='Edit Ticket Category'><img src='images/icons/edit_small.png' /></a></td>";
		echo "<td><a onClick=\"nw('detailPanel', 'ticketcategory/delete/{$ticketcategory['id']}')\" title='Delete Ticket Category'><img src='images/icons/delete_small.png' /></a></td>";
		echo "<td>{$ticketcategory['name']}</td>";
		echo "<td>{$ticketcategory['description']}</td>";
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
			echo sort_ticketcategory_page($j, $page, $project_id, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_ticketcategories') . "</div>\n";
}
?>
<div style="padding: 10px;"><a onClick="nw('detailPanel', 'ticketcategory/add');">Add Ticket Category</a></div>