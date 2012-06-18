<h1 class="panelTitle">Invoices: Page <?=round($page/$per_page+1)?></h1>
<?php
if ($invoices) {
?>
<table cellpadding="2" cellspacing="0" width="100%" class="list_items">
<tr>
	<th width="2">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th><th width="12">&nbsp;</th>
	<th><?=sort_invoice_head($page, $sort_column, 'name', 'Invoice');?></th>
	<th>Client</th>
	<th align='center'><?=sort_invoice_head($page, $sort_column, 'paid', 'Paid');?></th>
	<th align='center'><?=sort_invoice_head($page, $sort_column, 'sent', 'Sent');?></th>
	<th align='center'><?=sort_invoice_head($page, $sort_column, 'duedate', 'Due Date');?></th>
	<th align='right'><?=sort_invoice_head($page, $sort_column, 'amount', 'Amount');?></th>
	<th align='left'>Notes</th>
</tr>
<?php
$i = 0;
foreach($invoices AS $invoice) {
	$tr = $i++ % 2 ? 'even' : 'odd';
	$paid = $invoice['paid'] ? 'Yes' : '<span class="dim">No</span>';
	$sent = $invoice['sent'] ? 'Yes' : '<span class="dim">No</span>';
	echo "<tr class='$tr'>";
	echo "<td>&nbsp;</td>";
	echo "<td><a onClick=\"nw('mainPanel', 'invoice/edit/{$invoice['id']}')\" title='Edit Invoice'><img src='images/icons/edit_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('mainPanel', 'invoice/delete/{$invoice['id']}')\" title='Delete Invoice'><img src='images/icons/delete_small.png' /></a></td>";
	echo "<td><a onClick=\"nw('mainPanel', 'invoice/send/{$invoice['id']}')\" title='Send Invoice'><img src='images/icons/email_small.png' /></a></td>";
	echo "<td><a href=\"invoice/download_pdf/{$invoice['id']}\" title='Download PDF' target='_blank'><img src='images/icons/pdf_small.png' /></a></td>";
	echo "<td><a onclick=\"selectInvoice({$invoice['id']});\">{$invoice['name']}</a></td>";
	echo "<td>{$invoice['client_name']}</td>";
	echo "<td align='center'>$paid</td>";
	echo "<td align='center'>$sent</td>";
	echo "<td align='center'>" . date($this->lang->line('date_format'), strtotime($invoice['duedate'])) . "</td>";
	echo "<td class='monetary'>\${$invoice['amount']}</td>";
	echo "<td align='left'>" . trimmer($invoice['content'], 50) . "</td>";
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
			echo sort_invoice_page($j, $page, $sort_column, $p);
		}
		echo "</div>";
	}
} else {
	echo "<div class='notice'>" . $this->lang->line('notice_zero_invoices') . "<div>\n";
}
