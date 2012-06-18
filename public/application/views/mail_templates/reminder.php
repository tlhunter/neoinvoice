<div style="font-size: 13px; color: #474747; font-family: 'Trebuchet MS', arial;">
	<div style="height: 60px; border-bottom: 1px solid #ddd;">
		<div style="font-size: 20px; padding: 10px 0 0 10px; color: #222;"><?=$title?><br /><small style="font-size: 12px; font-family: arial, sans-serif; color: #666;"><?=$subtitle?></small></div>
	</div>
	<div style="padding: 10px;">
		<div class="message_paragraph"><?=$message?></div>
<?php if ($segments || $expenses) { ?>
		<table width="100%" cellspacing="0" cellpadding="0" style="margin: 10px 0;">
			<tr><th style="text-align: left; font-weight: normal; color: #000;">Notes</th><th style="text-align: center; font-weight: normal; color: #000;">Date</th><th style="text-align: center; font-weight: normal; color: #000;">Time</th><th style="text-align: center; font-weight: normal; color: #000;">Type</th><th style="text-align: right; font-weight: normal; color: #000;">Fee</th></tr>
<?php if ($segments) foreach($segments AS $segment) { ?>
			<tr><td style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$segment['content']?></td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$segment['date']?></td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$segment['duration']?></td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$segment['worktype_name']?></td><td align='right' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$segment['fee']?></td></tr>
<?php } ?>
<?php if ($expenses) foreach($expenses AS $expense) { ?>
			<tr><td style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$expense['content']?></td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$expense['date']?></td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;">N/A</td><td align='center' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$expense['expensetype_name']?></td><td align='right' style="padding: 2px 0; border-top: 1px dotted #ddd;"><?=$expense['amount']?></td></tr>
<?php } ?>
		</table>
<?php } ?>
	</div>
	<div style="height: 100px; border-top: 1px solid #ddd; font-family: verdana, arial, sans-serif; font-size: 11px;">
		<div style="padding: 10px 10px 0 10px;">Email sent via <a href="http://www.neoinvoice.com">www.neoinvoice.com</a> on behalf of <?=$company['name']?>.</div>
		<div style="padding: 5px 10px 0 10px;">If you don't think you should be the recipient of this email please <a href="http://www.neoinvoice.com/contact/spam-c<?=$invoice['company_id']?>-i<?=$invoice['id']?>">contact NeoInvoice administrators</a>.</div>
	</div>
</div>
