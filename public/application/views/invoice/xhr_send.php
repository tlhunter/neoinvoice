<h1 class="panelTitle">Send Invoice: <?=$invoice['name']?></h1>
<?php
if ($remain) {
?>
<form method="post" action="invoice/send_submit/<?=$invoice['id']?>" name="invoice_send" id="invoice_send">
	<table cellpadding="0" cellspacing="0" class="data-input-table">
		<tr><td class="label-cell">Client Email</td><td>&nbsp;</td><td><input name="recipient" maxlength="64" value="<?=$invoice['client_email']?>" class="textual" /></td></tr>
		<tr><td class="label-cell">Sender</td><td>&nbsp;</td><td><input value="<?=$sender?>" class="readonly" /></td></tr>
		<tr><td class="label-cell">CC Self</td><td>&nbsp;</td><td><input type="checkbox" checked="checked" name="copy_self" /> <small>Sends a copy of the invoice to the above sender email address.</small></td></tr>
		<tr><td class="label-cell" valign="top">Message</td><td>&nbsp;</td><td><textarea name="content" id="content" maxlength="8192" cols="20" rows="6" style="width: 400px; height: 150px;" class="textual">
Duedate: <?=local_date($invoice['duedate'])?> 
Total Amount: <?=$invoice['amount']?> 
Paid Amount: <?=$invoice['total_paid']?> 
Remain Amount: <?=$invoice['payment_remaining']?> 
<?php
$pd = $invoice['past_due'];
if ($pd > 0) {
	echo "Past Due: $pd\n";
} else {
	echo "Due In: $pd\n";
}
?>
Client Name: <?=$invoice['client_name']?> 
Client Email: <?=$invoice['client_email']?> 
Invoice Notes:
<?=$invoice['content']?>
				</textarea></td></tr>
		<?php if ($invoice['itemize']) { ?><tr><td>&nbsp;</td><td>&nbsp;</td><td><small>* This invoice is set as itemized and will include a list of all time segments</small></td></tr><?php } ?>
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td>
			<td>
				<input type="submit" value="Send Invoice" class="button" /> <small>(<?=$remain?> Remain)</small>
				<div style="float: right;">
					Templates: 
					<a class="button" onClick="updateTemplate(document.invoice_send.content, 'reminder', '<?=local_date($invoice['duedate'])?>', '<?=$invoice['amount']?>', '<?=addslashes($invoice['content'])?>', '<?=$invoice['past_due']?>', '<?=addslashes($invoice['client_name'])?>', '<?=$invoice['total_paid']?>', '<?=$invoice['payment_remaining']?>');">Reminder</a>
					<a class="button" onClick="updateTemplate(document.invoice_send.content, 'overdue', '<?=local_date($invoice['duedate'])?>', '<?=$invoice['amount']?>', '<?=addslashes($invoice['content'])?>', '<?=$invoice['past_due']?>', '<?=addslashes($invoice['client_name'])?>', '<?=$invoice['total_paid']?>', '<?=$invoice['payment_remaining']?>');">Overdue</a>
				</div>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	createAjaxForm('invoice_send', 'mainPanel');
	document.invoice_send.recipient.focus();
</script>
<?php
} else {
?>
<div class="error">You've already emailed the maximum number of invoices over the past month. If you would like to send more please consider upgrading your account.</div>
<?php
}
