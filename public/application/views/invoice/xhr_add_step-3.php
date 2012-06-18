<h1 class="panelTitle">Add Invoice: Step 3/3</h1>
<form method="post" action="invoice/add_submit" name="invoice_add" id="invoice_add">
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr>
		<td valign="top">
			<table>
				<tr><td class="label-cell">Invoice Name</td><td>&nbsp;</td><td><input name="name" value="<?=$default_invoice_name?>" maxlength="120" class="textual" /> <span class="required">Required</span></td></tr>
				<tr><td class="label-cell">Client Name</td><td>&nbsp;</td><td><input value="<?=$client['name']?>" maxlength="120" readonly class="readonly" /></td></tr><input type="hidden" name="client_id" value="<?=$client['id']?>" />
				<tr><td class="label-cell">Payment Due Date</td><td>&nbsp;</td><td><?=calendar('duedate', date('Y-m-d', strtotime('next month')))?> <span class="required">Required</span></td></tr>
				<tr><td class="label-cell">Invoice Amount</td><td>$</td><td><input id="new-invoice-amount" name="amount" value="0.00" maxlength="8" class="textual" /> <span class="required">Required</span></td></tr>
				<tr><td class="label-cell">Already Paid</td><td>&nbsp;</td><td><input name="paid" type="checkbox" onchange="checkboxToggleElementVisibility(this, 'paidDateToggle');" /> <small>Has the client already paid this invoice?</small></td></tr>
				<tr id="paidDateToggle" style="visibility: hidden;"><td class="label-cell">Paid Date</td><td>&nbsp;</td><td><?=calendar('paiddate')?> <span class="required">Required</span></td></tr>
				<tr><td class="label-cell">Itemize Segments</td><td>&nbsp;</td><td><input name="itemize" type="checkbox" /> <small>Do you want to show the client the time segments?</small></td></tr>
				<tr><td class="label-cell">Already Sent</td><td>&nbsp;</td><td><input name="sent" type="checkbox" /> <small>Have you already notified the client?</small></td></tr>
				<tr><td class="label-cell">Automatic Reminders</td><td>&nbsp;</td><td><input name="remind" type="checkbox" checked /> <small>Should we email a reminder at 7 and 2 days?</small></td></tr>
				<tr><td class="label-cell">&nbsp;</td><td>&nbsp;</td><td><input type="submit" value="Save Invoice" class="button" /></td></tr>
			</table>
		</td>
		<td valign="top">
			Notes:<br />
			<textarea style="width: 300px;" rows="7" cols="20" name="content" class="textual"><?php foreach($projects AS $project) echo $project['name'] . ', '; ?></textarea>
		</td>
	</tr>
</table>
<div class="panelContentPad">
	<div style="margin-top: 10px;"><p>Select the time segments and expenses below that you would like to add to this invoice. Selecting segments will update the invoice amount (which you can override).</p></div>
	<div id="cost-tables">
<?php
foreach($projects AS $project) {
	echo "<div class='project_selection_column'>\n";
	echo "<h2>{$project['name']}</h2>\n";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" class=\"data-input-table\">\n";
	foreach($segments[$project['id']] AS $segment) {
		echo "<tr><td valign='top'><input name='segments[]' id='segment-{$segment['id']}' type='checkbox' value='{$segment['id']}' data-cost='". number_format($segment['fee'], 2) . "' checked /></td><td class='monetary'>$ " . number_format($segment['fee'], 2) . "</td><td>{$segment['content']}</td></tr>\n";
	}
	foreach($expenses[$project['id']] AS $expense) {
		echo "<tr><td valign='top'><input name='expenses[]' id='expense-{$expense['id']}' type='checkbox' value='{$expense['id']}' data-cost='" . number_format($expense['amount'], 2) . "' checked /></td><td class='monetary'>$ " . number_format($expense['amount'], 2) . "</td><td>{$expense['content']}</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
}
?>
	</div>
</div>
</form>
<script type="text/javascript">
	createAjaxForm('invoice_add', 'mainPanel');
	document.invoice_add.name.focus();
	setupCalculateInvoiceCost('cost-tables', 'new-invoice-amount');
</script>
