<h1 class="panelTitle">Edit Invoice: <?=$invoice['name']?></h1>
<form method="post" action="invoice/edit_submit/<?=$invoice['id']?>" name="invoice_edit" id="invoice_edit">
<table cellpadding="0" cellspacing="0" class="data-input-table">
	<tr>
		<td valign="top">
			<table>
				<tr><td class="label-cell">Invoice Name</td><td>&nbsp;</td><td><input name="name" value="<?=$invoice['name']?>" maxlength="120" class="textual" /> <span class="required">Required</span></td></tr>
				<tr><td class="label-cell">Client Name</td><td>&nbsp;</td><td><input value="<?=$client['name']?>" maxlength="120" readonly class="readonly" /></td></tr><input type="hidden" name="client_id" value="<?=$client['id']?>" />
				<tr><td class="label-cell">Payment Due Date</td><td>&nbsp;</td><td><?=calendar('duedate', $invoice['duedate'])?> <span class="required">Required (YYYY-MM-DD)</span></td></tr>
				<tr><td class="label-cell">Invoice Amount</td><td>$</td><td><input name="amount" value="<?=$invoice['amount']?>" maxlength="8" class="textual" /></td></tr>
				<tr><td class="label-cell">Already Paid</td><td>&nbsp;</td><td><?=checkbox('paid', $invoice['paid'], '', "checkboxToggleElementVisibility(this, 'paidDateToggle');")?> <small>Has the client already paid this invoice?</small></td></tr>
				<tr id="paidDateToggle"<?php if (!$invoice['paid']) { ?> style="visibility: hidden;"<?php } ?>><td class="label-cell">Paid Date</td><td>&nbsp;</td><td><?=calendar('paiddate', $invoice['paiddate'])?> <span class="required">Required (YYYY-MM-DD)</span></td></tr>
				<tr><td class="label-cell">Itemize Segments</td><td>&nbsp;</td><td><?=checkbox('itemize', $invoice['itemize'])?> <small>Do you want to show the client the time segments?</small></td></tr>
				<tr><td class="label-cell">Already Sent</td><td>&nbsp;</td><td><?=checkbox('sent', $invoice['sent'])?> <small>Have you already notified the client?</small></td></tr>
				<tr><td class="label-cell">Automatic Reminders</td><td>&nbsp;</td><td><?=checkbox('remind', $invoice['remind'])?> <small>Should we email a reminder at 7 and 2 days?</small></td></tr>
				<tr><td class="label-cell">&nbsp;</td><td>&nbsp;</td><td><input type="submit" value="Save Invoice" class="button" /></td></tr>
			</table>
		</td>
		<td valign="top">
			Notes:<br />
			<textarea style="width: 300px;" rows="7" cols="20" name="content" class="textual"><?=$invoice['content']?></textarea>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
	createAjaxForm('invoice_edit', 'mainPanel');
	document.invoice_edit.name.focus();
</script>
