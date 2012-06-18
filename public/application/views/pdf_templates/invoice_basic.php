<style type="text/css">
body {
	font-family: helvetica;
	font-size: 10pt;
}
.style1 {
	font-size: 26pt;
	font-weight: bold;
	color: #CCCCCC;
}
.style2 {
	font-size: 18pt;
	font-weight: bold;
}
.even {
	background-color: #eee;
	border-bottom: 1pt solid #ccc;
	border-left: 1pt solid #ccc;
}
.odd {
	background-color: #fff;
	border-bottom: 1pt solid #ccc;
	border-left: 1pt solid #ccc;
}
#workdata {
	border-top: 1pt solid #ccc;
	border-right: 1pt solid #ccc;
}
</style>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" valign="middle" height="10pt"><div class="style2"><?php if ($image) { echo "<img src=\"$image\" />"; } else { echo $company['name']; } ?></div></td>
    <td width="50%" valign="middle"><div align="right" class="style1"><?php if ($invoice['paid']) echo "PAID "; ?>INVOICE</div></td>
  </tr>
  <tr>
	<td>
<?=nl2br($company['invoice_address'])?>
	</td>
	<td align="right">
Print Date: <?=local_date($invoice['created'])?><br />
Due Date: <?=local_date($invoice['duedate'])?><br />
Invoice #: <?=zero_fill($invoice['id'], 5)?>
	</td>
  </tr>
  <tr>
    <td><br /><br /><strong>Bill To</strong>: <?=$invoice['client_name']?><br />&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><table id="workdata" width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="62%"><strong>Notes</strong></td>
        <td width="10%"><strong>Date</strong></td>
        <td width="12%"><strong>Type</strong></td>
		<td width="6%"><div align="right"><strong>Hours</strong></div></td>
        <td width="10%"><div align="right"><strong>Price</strong></div></td>
      </tr>
<?php
$i = 1;
$time = 0;
$total_cost = 0.00;
foreach($segments AS $segment) {
	$row = $i++ % 2 ? 'even' : 'odd';
	$time_data = mysql_time($segment['duration']);
	$time += $time_data['hour_float'];
	if ($segment['billable']) {
		$total_cost += $segment['fee'];
	}
?>
	  <tr class="<?=$row?>">
        <td><?=$segment['content']?></td>
        <td><?=date('n/j/Y', strtotime($segment['date']))?></td>
        <td><?=$segment['worktype_name']?></td>
        <td><div align="right"><?=$time_data['human_readable']?></div></td>
        <td><div align="right"><?php if ($segment['billable']) echo '$'.$segment['fee']; else echo "N/A"; ?></div></td>
      </tr>
<?php
}

foreach($expenses AS $expense) {
	$row = $i++ % 2 ? 'even' : 'odd';
	if ($expense['billable']) {
		$total_cost += $expense['amount'];
	}
?>
	  <tr class="<?=$row?>">
        <td><?=$expense['content']?></td>
        <td><?=date('n/j/Y', strtotime($expense['date']))?></td>
        <td><?=$expense['expensetype_name']?></td>
        <td>&nbsp;</td>
        <td><div align="right"><?php if ($expense['billable']) echo '$'.$expense['amount']; else echo "N/A"; ?></div></td>
      </tr>
<?php
}

if ($total_cost != $invoice['amount']) {
	$adjustment = number_format($invoice['amount'] - $total_cost, 2);
?>
	  <tr class="odd">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><strong>Etc.</strong>:</td>
        <td>&nbsp;</td>
        <td align="right">$<?=$adjustment?></td>
	  </tr>
<?php
}
?>
      <tr class="odd">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><strong>Total</strong>:</td>
        <td><div align="right"><?=hour_float_readable($time)?></div></td>
        <td align="right"><strong>$<?=$invoice['amount']?></strong></td>
      </tr>
    </table></td>
  </tr>
</table>
<div><strong>Notes:</strong></div>
<div><?=$invoice['content']?></div>
