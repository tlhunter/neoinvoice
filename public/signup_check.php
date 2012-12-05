<?php
require_once("config.php");

if (isset($_GET['field']) && ($_GET['field'] == 'username' || $_GET['field'] == 'email')) {
	$field = $_GET['field'];
	$table = 'user';
	$taken = '0';
	$not_taken = '1';
} else if (isset($_GET['field']) && $_GET['field'] == 'coupon') {
	$field = 'name';
	$table = 'coupon';
	$taken = '1';
	$not_taken = '0';
} else {
	die("<div class=\"error\">Invalid Field</div>");
}
if (!isset($_GET['value'])) {
	die("<div class=\"error\">Invalid Value</div>");
}
$value = preg_replace("[^a-zA-Z0-9_.\-\*\/\+\, @]", "", $_GET['value']);
if ($value != $_GET['value']) {
	die("<div class=\"error\">Invalid Value</div>");
}

$connect = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
if (!$connect) {
	die("<div class=\"error\">" . mysql_error() . "</div>");
}
$query = mysql_real_escape_string("SELECT $field FROM $table WHERE $field = '$value' LIMIT 1"); // Prevent SQL Injection

mysql_select_db(MYSQL_DATABASE, $connect);
$result = mysql_query($query, $connect);
if (mysql_num_rows($result)) {
	echo $taken;
} else {
	echo $not_taken;
}