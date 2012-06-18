<h1 class="panelTitle">Record Invoice Payment</h1>
<?php
if (!$invoices) {
	echo "<div class='notice'>You don't have any invoices pending payment.</div>\n";
} else {
?>
<h4>Select an invoice to record a payment for:</h4>
<form method="post" action="invoice/payment_submit" name="invoice_payment" id="invoice_payment">
<table cellpadding="0" cellspacing="0" class="data-input-table" style="float: left;">
	<tr><td>&nbsp;</td><td>Invoice</td><td>Due Date</td><td>Initial</td><td>Balance</td><td>Due In</td></tr>
<?php
foreach($invoices AS $invoice) {
	if ($invoice['past_due'] > 0) {
		$tr = 'past_due';
	} else {
		$tr = '';
	}
	
	if ($selected == $invoice['id']) {
		$sel = " checked";
	} else {
		$sel = '';
	}
	echo "<tr class='$tr line_bottom'>";
	echo "<td><input name='invoice_id' value='{$invoice['id']}' id='invoice-{$invoice['id']}' type='radio'$sel /></td>";
	echo "<td>{$invoice['name']}</td>";
	echo "<td>{$invoice['duedate']}</td>";
	echo "<td class='monetary'>\${$invoice['amount']}</td>";
	echo "<td class='monetary'><strong>\${$invoice['payment_remaining']}</strong></td>";
	echo "<td>" . -$invoice['past_due'] . " days</td>";
	echo "</tr>\n";
}
?>
</table>
<div style="width: 300px; float: right;">
	Payment Amount:<br />
	$ <input name="amount" value="0.00" class="textual" maxlength="10" /><br />
	Payment Notes:<br />
	<textarea name="content" cols="20" rows="5" class="textual" style="width: 280px;"></textarea><br />
	Payment Date:<br />
	<?=calendar()?>
	<div style="margin: 10px 0;"><input type="submit" class="button" value="Record Payment" /></div>
</div>
</form>
<script type="text/javascript">
	createAjaxForm('invoice_payment', 'mainPanel');
</script>
<?php
}
