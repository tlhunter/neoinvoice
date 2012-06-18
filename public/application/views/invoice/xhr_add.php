<h1 class="panelTitle">Add Invoice: Step 1/3</h1>
<div class="panelContentPad">
	<p>Select a client below to invoice. After selecting a client you will be able to select which projects to invoice.</p>
	<div id="client_list_invoice">
	<?php
	if ($clients) {
		foreach($clients AS $client) {
			echo "<div class=\"client_link\"><a onClick=\"nw('mainPanel', 'invoice/add/{$client['id']}');\" class=\"button\">{$client['name']}</a></div>\n";
		}
	} else {
		echo "<div class='notice'>You do not have any clients added yet! To get started, please create a client, create a project, and enter some time.</div>\n";
	}
	?>
		<div style="clear: both;"></div>
	</div>
</div>