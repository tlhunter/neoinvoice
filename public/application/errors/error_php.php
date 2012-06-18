<?php
if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') !== FALSE) {
	echo "<div class='error'><div>Severity: $severity</div>\n<div>Message: $message</div>\n<div>Filename: $filepath</div>\n<div>Line Number: $line</div></div>\n";
	exit();
}
?>
<div style="border:1px solid #ff0000; padding-left:20px; margin:0 0 10px 0; background-color: #111; color: #fff;">
	<h4>An application error has occured!</h4>
	<p>Severity: <?php echo $severity; ?></p>
	<p>Message:  <?php echo $message; ?></p>
	<p>Filename: <?php echo $filepath; ?></p>
	<p>Line Number: <?php echo $line; ?></p>
</div>