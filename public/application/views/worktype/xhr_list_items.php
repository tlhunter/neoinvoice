<h1 class="panelTitle">Work Types: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($worktypes)) {
?>
<table cellpadding="2" cellspacing="0" width="100%">
<tr><th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th>Name</th><th class='monetary'>Rate</th><th align="right">Total Hours</th><th class="monetary">Income</th><th align="right">Uses</th><th>Content</th></tr>
<?php
	$i = 0;
	foreach($worktypes AS $worktype) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		echo "<tr class='$tr'>";
		echo "<td>&nbsp;</td>";
		echo "<td><a onClick=\"nw('detailPanel', 'worktype/edit/{$worktype['id']}')\" title='Edit Work Type'><img src='images/icons/edit_small.png' /></a></td>";
		echo "<td><a onClick=\"nw('detailPanel', 'worktype/delete/{$worktype['id']}')\" title='Delete Work Type'><img src='images/icons/delete_small.png' /></a></td>";
		echo "<td>{$worktype['name']}</td>";
		echo "<td class='monetary'>$ {$worktype['hourlyrate']}</td>";
		echo "<td class='monetary'>{$worktype['hour_float']}</td>";
		echo "<td class='monetary'>$ " . number_format($worktype['hour_float'] * $worktype['hourlyrate'], 2) . "</td>";
		echo "<td align='right'>{$worktype['segment_count']}</td>";
		echo "<td>{$worktype['content']}</td>";
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
			echo sort_worktype_page($j, $page, $project_id, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_worktypes') . "</div>\n";
}
?>
<div style="padding: 10px;"><a onClick="nw('detailPanel', 'worktype/add');">Add Work Type</a></div>