<h1 class="panelTitle">Project Recorded Time: Page <?=round($page/$per_page+1)?></h1>
<?php
if (count($segments)) {
?>
<form name="segment_delete_multiple" action="">
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr><th width="32">&nbsp;</th><th>Date</th><th>Start</th><th>Duration</th><th align="center">Billable</th><th align="center">Invoice</th><th>Contributor</th><th>Work Type</th></tr>
<?php
	$i = 0;
	foreach($segments AS $segment) {
		$tr = $i++ % 2 ? 'even' : 'odd';
		$billable = $segment['billable'] ? 'Yes' : 'No';
		echo "<tr class='$tr'>";
		echo "<td align='center'><input type='checkbox' name='{$segment['id']}' /></td>";
		echo "<td><a onclick=\"selectSegment({$segment['id']});\">" . date("M j, Y", strtotime($segment['date'])) . "</a></td>";
		echo "<td>" . date("g:i a", strtotime($segment['time_start'])) . "</td>";
		echo "<td>{$segment['duration']} hrs</td>";
		echo "<td align='center'>$billable</td>";
		if ($segment['invoice_id']) {
			echo "<td align='center'><a onclick=\"selectInvoice({$segment['invoice_id']});\"># {$segment['invoice_id']}</a></td>";
		} else {
			echo "<td align='center'>N/A</td>";
		}
		echo "<td><a onclick=\"selectUser({$segment['user_id']});\">{$segment['user_name']}</a></td>";
		echo "<td>{$segment['worktype_name']}</td>";
		echo "</tr>\n";
	}
?>
<tr><td align="center">&nbsp;&nbsp;&nbsp;<img src="images/icons/arrow.png" alt="With Selected" /></td><td colspan="8"><a class="button" onClick="linkByCheckboxes('mainPanel', 'segment/delete_multiple/', 'segment_delete_multiple')">Delete Selected</a></td></tr>
</table>
</form>
<?php
	if ($total > $per_page) {
		echo "<div class='pagify'>Page";
		$total_pages = ceil($total / $per_page);
		for ($i = 0; $i < $total_pages; $i++) {
			$j = $i * $per_page;
			$p = $i + 1;
			echo sort_segment_page($j, $page, $project_id, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_segments') . "</div>\n";
}