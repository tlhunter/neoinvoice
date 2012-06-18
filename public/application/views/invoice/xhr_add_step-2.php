<h1 class="panelTitle">Add Invoice: Step 2/3</h1>
<div class="panelContentPad">
	<p>Select the projects you would like to invoice. You will be able to select individual time segments on the next screen.</p>
	<div><a onClick="linkByCheckboxes('mainPanel', 'invoice/add/<?=$client_id?>/', 'invoice_add_2')" class="button">Invoice Selected Projects</a></div>
	<form name="invoice_add_2" action="">
	<table>
<?php
foreach($projects AS $project) {
	$content = '';
	if ($project['content']) {
		$content = " - {$project['content']}";
	}
	echo "\t<tr>";
	echo "<td align='center'><input type='checkbox' name='{$project['id']}' id='checkbox-project-{$project['id']}' /></td>";
	echo "<td>&nbsp;</td>";
	echo "<td><label for='checkbox-project-{$project['id']}'>{$project['name']}</label>$content</td>";
	echo "</tr>\n";
}
?>
	</table>
	</form>
<?php
if (count($projects) > 10) {
?>
	<div><a onClick="linkByCheckboxes('mainPanel', 'invoice/add/<?=$client_id?>/', 'invoice_add_2')">Invoice Selected Projects</a></div>
<?php
}
?>
</div>