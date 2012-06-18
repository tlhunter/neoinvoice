<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * PayPal_Lib Controller Class (Paypal IPN Class)
 *
 * This CI library is based on the Paypal PHP class by Micah Carrick
 * See www.micahcarrick.com for the most recent version of this class
 * along with any applicable sample files and other documentaion.
 *
 * This file provides a neat and simple method to interface with paypal and
 * The paypal Instant Payment Notification (IPN) interface.  This file is
 * NOT intended to make the paypal integration "plug 'n' play". It still
 * requires the developer (that should be you) to understand the paypal
 * process and know the variables you want/need to pass to paypal to
 * achieve what you want.  
 *
 * This class handles the submission of an order to paypal as well as the
 * processing an Instant Payment Notification.
 * This class enables you to mark points and calculate the time difference
 * between them.  Memory consumption can also be displayed.
 *
 * The class requires the use of the PayPal_Lib config file.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Commerce
 * @author      Ran Aroussi <ran@aroussi.com>
 * @copyright   Copyright (c) 2006, http://aroussi.com/ci/
 *
 */

// ------------------------------------------------------------------------

class Paypal {
	var $last_error;			// holds the last error encountered
	var $ipn_log;				// bool: log IPN results to text file?

	var $ipn_log_file;			// filename of the IPN log
	var $ipn_response;			// holds the IPN response from paypal	
	var $ipn_data = array();	// array contains the POST values for IPN
	var $fields = array();		// array holds the fields to submit to paypal

	var $submit_btn = '';		// Image/Form button
	var $button_path = '';		// The path of the buttons
	
	var $CI;
	
	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->helper('url');
		$this->CI->load->config('paypal_config');
		
		$this->last_error = '';
		$this->ipn_response = '';

		$this->paypal_url = $this->CI->config->item('paypal_url');
		$this->ipn_log_file = $this->CI->config->item('paypal_ipn_log_file');
		$this->ipn_log = $this->CI->config->item('paypal_ipn_log');
				
		$this->add_field('rm','2');			  // Return method = POST
		$this->add_field('cmd','_xclick');
		$this->add_field('currency_code', $this->CI->config->item('paypal_currency_code'));
	    $this->add_field('quantity', '1');
		$this->add_field('business', $this->CI->config->item('paypal_email'));
		$this->add_field('return', site_url('payment/success'));
		$this->add_field('cancel_return', site_url('payment/cancel'));
		$this->add_field('notify_url', site_url('payment/payment_ipn'));
	}

	function button($value) {
		// changes the default caption of the submit button
		$this->submit_btn = "<input type=\"submit\" name=\"pp_submit\" value=\"$value\" class=\"button\" />\n";
	}

	function add_field($field, $value) {
		// adds a key=>value pair to the fields array, which is what will be 
		// sent to paypal as POST variables.  If the value is already in the 
		// array, it will be overwritten.
		$this->fields[$field] = $value;
	}

	function paypal_auto_form() {
		// this function actually generates an entire HTML page consisting of
		// a form with hidden elements which is submitted to paypal via the 
		// BODY element's onLoad attribute.  We do this so that you can validate
		// any POST vars from you custom form before submitting to paypal.  So 
		// basically, you'll have your own form which is submitted to your script
		// to validate the data, which in turn calls this function to create
		// another hidden form and submit to paypal.

		$this->button('Click here to be redirected to the payment screen...');

		echo '<html>' . "\n";
		echo '<head><title>Processing Payment...</title></head>' . "\n";
		echo '<body onLoad="document.forms[\'paypal_auto_form\'].submit();">' . "\n";
		echo '<p>Please wait, your order is being processed and you will be redirected to the paypal website.</p>' . "\n";
		echo $this->paypal_form('paypal_auto_form');
		echo '</body></html>';
	}

	function paypal_form($form_name = 'payment_form', $new_window = FALSE) {
		$target = '';
		if ($new_window)
			$target = " target='_blank'";
		$str = "<form method=\"post\" action=\"{$this->paypal_url}\" name=\"$form_name\"$target>\n";
		foreach ($this->fields AS $name => $value) {
			$str .= "\t<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
		}
		$str .= "\t{$this->submit_btn}\n";
		$str .= "</form>\n";

		return $str;
	}
	
	function validate_ipn() {
		// parse the paypal URL
		$url_parsed = parse_url($this->paypal_url);		  

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = '';
		if (isset($_POST) && !empty($_POST)) {
			foreach ($_POST AS $field => $value) {
				$value = str_replace("\n", "\r\n", $value);
				$this->ipn_data[$field] = $value;
				$post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
			}
		}
		
		$post_string .= "cmd=_notify-validate"; // append ipn command
		$err_num = 0;
		$err_str = '';
		// open the connection to paypal
		$fp = fsockopen($url_parsed['host'], "80", $err_num, $err_str, 30);
		if(!$fp) {
			// could not open the connection.  If loggin is on, the error message
			// will be in the log.
			$this->last_error = "fsockopen error no. $err_num: $err_str";
			$this->log_ipn_results(false);		 
			return false;
		} else {
			// Post the data back to paypal
			fputs($fp, "POST {$url_parsed['path']} HTTP/1.1\r\n");
			fputs($fp, "Host: {$url_parsed['host']}\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $post_string . "\r\n\r\n"); 

			// loop through the response from the server and append to variable
			while(!feof($fp)) {
				$this->ipn_response .= fgets($fp, 1024);
			}

			fclose($fp); // close connection
		}

		if (strpos($this->ipn_response, "VERIFIED") !== FALSE) {
			// Valid IPN transaction.
			$this->log_ipn_results(TRUE);
			return TRUE;
		} else {
			// Invalid IPN transaction.  Check the log for details.
			$this->last_error = 'IPN Validation Failed.';
			$this->log_ipn_results(FALSE);
			return FALSE;
		}
	}

	function log_ipn_results($success) {
		if (!$this->ipn_log) {
			return;
		}

		$text = "[" . date('Y-m-d H:i:s') . "] ------------------------------------------------------------\n";

		if ($success) {
			$text .= "SUCCESS!\n";
		} else {
			$text .= 'FAIL: ' . $this->last_error . "\n";
		}

		// Log the POST variables
		$text .= "\nIPN POST Vars from Paypal:\n";
		foreach ($this->ipn_data AS $key=>$value) {
			$text .= "$key=$value, ";
		}

		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n" . $this->ipn_response;
		$text .= "\n\n";

		// Write to log
		$fp = fopen($this->ipn_log_file, 'a');
		fwrite($fp, $text); 
		fclose($fp);  // close file
	}

	function dump() {
		ksort($this->fields);
		echo "<pre>\n";
		foreach ($this->fields AS $key => $value) {
			echo $key . ": " . urldecode($value) . "\n";
		}
		echo "</pre>\n";
	}
}