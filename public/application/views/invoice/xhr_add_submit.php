<h1 class="panelTitle">Invoice Added</h1>
<div class="success"><?=$message?></div>
<br />
<div class="notice">
	<a onClick="nw('mainPanel', 'invoice/payment/<?=$invoice_id?>')" class="button">Add Payment</a> |
	<a onClick="nw('mainPanel', 'invoice/send/<?=$invoice_id?>')" class="button">Send Invoice</a>
</div>
<script type="text/javascript">
	refreshInvoicePanel();
</script>