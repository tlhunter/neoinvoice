<?php
/*
 * This is the main configuration file for the NeoInvoice project.
 * There are others though, take a look at README.md to be safe.
 */
define('BACKUP_EMAIL', "EMAIL-ADDRESS-FOR-BACKUPS");
switch($_SERVER['SERVER_NAME']) {
	case 'localhost':
	case 'neoinvoice.local':
		define('MYSQL_HOSTNAME', "localhost");
		define('MYSQL_USERNAME', "MYSQL-USERNAME");
		define('MYSQL_PASSWORD', "MYSQL-PASSWORD");
		define('MYSQL_DATABASE', "neoinvoice");
		define('CACHE_SYSTEM',   "apc"); # apc, memcache
		define('DATABASE_DEBUG',   TRUE);
		break;
	case 'neoinvoice.com':
	case 'www.neoinvoice.com':
		define('MYSQL_HOSTNAME', "localhost");
		define('MYSQL_USERNAME', "MYSQL-USERNAME");
		define('MYSQL_PASSWORD', "MYSQL-PASSWORD");
		define('MYSQL_DATABASE', "neoinvoice");
		define('CACHE_SYSTEM',   "memcache"); # apc, memcache
		define('DATABASE_DEBUG',   FALSE);
		break;
	default:
		die("INVALID HOST");
}
