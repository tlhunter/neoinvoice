<?php
if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') !== FALSE) {
	echo "<div class='error'><div><strong>$heading</strong></div>\n<div>$message</div></div>\n";
	exit();
}
?>
<html>
<head>
<title>NeoInvoice: Application Error</title>
<style type="text/css">
body#error {
	background-color:	#000;
	margin:				40px;
	font-family:		Lucida Grande, Calibri, Verdana, Sans-serif;
	font-size:			12px;
	color:				#fff;
}
body#error #content  {
	border:				#999 1px solid;
	background-color:	#111;
	padding:			20px 20px 12px 20px;
}
body#error h1 {
	font-weight:		normal;
	font-size:			14px;
	color:				#ff0000;
	margin: 			0 0 4px 0;
}
</style>
</head>
<body id="error">
	<div id="content">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
</html>