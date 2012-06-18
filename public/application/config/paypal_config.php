<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['paypal_ipn_log_file'] = BASEPATH . 'logs/paypal-transactions.log';
$config['paypal_ipn_log'] = TRUE;
$config['paypal_currency_code'] = 'USD';
$config['paypal_email'] = 'SET-TO-PAYPAL-ADDRESS';
$config['paypal_url'] = 'https://www.paypal.com/cgi-bin/webscr';