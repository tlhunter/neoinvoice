<h1 class="panelTitle">Invoices</h1>
<ul id="display_tree_invoices" class="tree">

	<li class="folder f-open first"><span><?=$this->lang->line('unpaid')?></span>
		<ul>
<?php
foreach($invoices AS $invoice) {
	if (!$invoice['paid']) {
		if ($invoice['past_due'] > 0) {
			$color = ' class="past_due"';
			$extra = ' (-' . $invoice['past_due'] . ' days)';
		} else {
			$days = -(int) $invoice['past_due'];
			$color = '';
			$extra = " (+$days days)";
		}
		echo "\t\t\t<li class=\"doc\" data-tree-icon=\"invoice\"><span><a onclick=\"selectInvoice({$invoice['id']});\"$color>{$invoice['name']}</a> $extra</span></li>\n";
	}
}
?>
		</ul>
	</li>
	<li class="folder f-close"><span><?=$this->lang->line('paid')?></span>
		<ul>
<?php
foreach($invoices AS $invoice) {
	if ($invoice['paid'])
		echo "\t\t\t<li class=\"doc\" data-tree-icon=\"invoice\"><span><a onclick=\"selectInvoice({$invoice['id']});\">{$invoice['name']}</a></span></li>\n";
}
?>
		</ul>
	</li>
</ul>
<?php if (!isset($no_tree) || !$no_tree) { ?>
<script type="text/javascript">
	if (typeof(buildTree) != "undefined")
	buildTree('display_tree_invoices');
</script>
<?php } ?>
<div class="hover-options"><a onClick="refreshInvoicePanel();">Reload Panel</a></div>
