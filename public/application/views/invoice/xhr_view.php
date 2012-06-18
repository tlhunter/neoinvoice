<div class="invoice-window">
<h1 class="panelTitle">Invoice: <?=$invoice['name']?></h1>
<h3 class="project_name">For <?=$invoice['client_name']?></h3>
<?php
$value = explode('.', $invoice['amount']);
$dollars = number_format($value[0]);
$cents = $value[1];
$paid = 0.00;
if ($payments) {
	foreach($payments AS $payment) {
		$paid += $payment['amount'];
	}
}
$remaining = number_format($invoice['amount'] - $paid, 2);
$value_remain = explode('.', $remaining);
$dollars_remain = $value_remain[0];
$cents_remain = $value_remain[1];
?>

<div class="invoice_amount">
    <div class="title">Invoice Total</div>
	<div class="invoice_dollars">$<?=$dollars?>.</div>
	<div class="invoice_cents"><?=$cents?></div>
</div>
<div class="invoice_amount invoice_remain">
    <div class="title">Unpaid Balance</div>
	<div class="invoice_dollars">$<?=$dollars_remain?>.</div>
	<div class="invoice_cents"><?=$cents_remain?></div>
</div>
<div style="clear: both;"></div>
<?php
if ($invoice['sent']) {
	echo "<p>You have sent this invoice to the client.</p>\n";
}
if ($invoice['paid']) {
	echo "<p>This invoice was paid on " . date($this->lang->line('date_format_short'), strtotime($invoice['paiddate'])) . ".</p>\n";
} else {
	echo "<p>This invoice is due on " . date($this->lang->line('date_format_short'), strtotime($invoice['duedate'])) . ".</p>\n";
}
?>
<h4>Invoice Time Segments</h4>
<?php
echo "<table width='100%'>";
echo "<tr><th width='16'>&nbsp;</th><th width='130'>Date</th><th>Project</th><th>Work Type</th><th>Time</th><th>Teammate</th><th align='center'>Bill</th></tr>\n";
foreach($segments AS $segment) {
	echo "<tr id='segment-row-{$segment['id']}'>";
	echo "<td><a onClick=\"unassignSegment('{$segment['id']}');\" title='Unassign Time Segment from Invoice'><img src='images/icons/unassign_small.png' alt='unassign' /></a></td>";
	echo "<td><acronym title='".htmlentities($segment['content'])."'>" . date($this->lang->line('date_format_short'), strtotime($segment['date'])) . "</acronym></td>";
	echo "<td>{$segment['project_name']}</td>";
	echo "<td>{$segment['worktype_name']}</td>";
	echo "<td>{$segment['duration']}</td>";
	echo "<td>{$segment['user_name']}</td>";
	echo "<td align='center'>" . yes_no($segment['billable']) . "</td>";
	echo "</tr>\n";
}
echo "</table>\n";
?>
<h4>Invoice Expenses</h4>
<?php
if ($expenses) {
	echo "<table width='100%'>\n";
	echo "<tr><th width='16'>&nbsp;</th><th width='130'>Date</th><th>Project</th><th>Expense Type</th><th width='60' class='monetary' style='padding-right: 8px;'>Amount</th><th align='center'>Bill</th></tr>\n";
	foreach($expenses AS $expense) {
		echo "<tr id='expense-row-{$expense['id']}'><td><a onClick=\"unassignExpense('{$expense['id']}');\" title='Unassign Expense from Invoice'><img src='images/icons/unassign_small.png' alt='unassign' /></a></td><td><acronym title='".htmlentities($expense['content'])."'>" . date($this->lang->line('date_format_short'), strtotime($expense['date'])) . "</acronym></td><td>{$expense['project_name']}</td><td>{$expense['expensetype_name']}</td><td class='monetary' style='padding-right: 8px;'>\${$expense['amount']}</td><td align='center'>" . yes_no($expense['billable']) . "</td></tr>\n";
	}
	echo "</table>\n";
} else {
	echo "<div class='notice'>There are no recorded expenses for this invoice.</div>\n";
}
?>
<h4>Invoice Payments</h4>
<?php
if ($payments) {
	echo "<table width='100%'>\n";
	echo "<tr><th width='16'>&nbsp;</th><th width='130'>Date</th><th width='60' class='monetary' style='padding-right: 8px;'>Amount</th><th>Notes</th></tr>\n";
	foreach($payments AS $payment) {
		echo "<tr id='payment-row-{$payment['id']}'>";
		echo "<td><a onClick=\"deletePayment('{$payment['id']}');\" title='Delete Payment'><img src='images/icons/delete_small.png' alt='delete' /></a></td>";
		echo "<td>" . date($this->lang->line('date_format_short'), strtotime($segment['date'])) . "</td>";
		echo "<td class='monetary' style='padding-right: 8px;'>\${$payment['amount']}</td><td>{$payment['content']}</td></tr>\n";
	}
	echo "</table>\n";
} else {
	echo "<div class='notice'>There are no recorded payments for this invoice.</div>\n";
}
?>
<div class="window_footer_buttons">
	<div style="float: left;">
		<a onclick="nw('mainPanel', 'invoice/payment/<?=$invoice['id']?>')" class="button">Record Payment</a>
		<?php if (!$invoice['paid']) { ?>
		<a onclick="nw('mainPanel', 'invoice/send/<?=$invoice['id']?>')" class="button">Send Invoice</a>
		<?php } ?>
	</div>
	<div style="float: right;">
		<a onclick="nw('mainPanel', 'invoice/edit/<?=$invoice['id']?>')" class="button">Edit Invoice</a>
		<a onclick="nw('mainPanel', 'invoice/delete/<?=$invoice['id']?>')" class="button">Delete Invoice</a>
	</div>
	<div style="clear: both;"></div>
</div>
</div>