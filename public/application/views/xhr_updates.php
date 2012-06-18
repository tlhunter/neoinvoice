<h1 class="panelTitle">NeoInvoice Updates</h1>
<?php
foreach($updates AS $update) {
	echo "<div class='company_update'>\n";
	echo "<h3><a href='{$update['url']}' target='_blank'>{$update['title']}</a></h3>\n";
	echo "<h4>" . date("F j, Y", strtotime($update['date'])) . "</h4>\n";
	echo "<p>" . nl2br($update['content']) . "</p>\n";
	echo "</div>\n";
}