<?php
class Payment extends Controller {
	function __construct() {
		parent::Controller();
		$this->load->library('paypal');
		$this->load->library('document');
		$this->load->library('template');
		$this->load->model('company_model');
	}
	
	function cancel() {
		$data = $this->document->generate_page_data();
		$this->template->load('main_template', 'payment/cancel', $data);
	}
	
	function success() {
		$data = $this->document->generate_page_data();
		$data['pp_info'] = $_POST;
		$this->template->load('main_template', 'payment/success', $data);
	}
	
	function payment_ipn() {
		$to = 'paypal@renownedmedia.com';
		$status = '';
		if ($this->paypal->validate_ipn()) {
			$payment = (float) $this->paypal->ipn_data['payment_gross'];
			$type = (int) $this->paypal->ipn_data['item_number'];
			$custom = explode(':', $this->paypal->ipn_data['custom']);
			if ($custom[0] == 'company_id' && count($custom) == 2) {
				$company_id = (int) $custom[1];
				if ($this->security_model->confirm_price($type, $payment)) {
					if ($this->company_model->upgrade_company($company_id, $type, 30)) {
						$status = "COMPANY $company_id UPGRADED SUCCESSFULLY";
					} else {
						$status = "ERROR RUNNING UPGRADE COMPANY FUNCTION FOR COMPANY $company_id!";
					}
				} else {
					$status = "INVALID COST OF $payment FOR ITEM $type, COMPANY $company_id NOT UPGRADED!";
				}
			} else {
				$status = "INVALID CUSTOM DATA " . htmlentities($this->paypal->ipn_data['custom']) . "!";
			}

			##################################################################
			$body  = 'An instant payment notification was successfully received from ';
			$body .= $this->paypal->ipn_data['payer_email'] . ' on ' . date('m/d/Y') . ' at ' . date('g:i A') . "\n\n";
			$body .= " Details:\n";

			foreach ($this->paypal->ipn_data AS $key => $value) {
				$body .= "\n$key: $value";
			}

			$body .= "\n$custom\n";
	
			$this->load->library('email');
			$this->email->to($to);
			$this->email->from($this->paypal->ipn_data['payer_email'], $this->paypal->ipn_data['payer_name']);
			$this->email->subject('CI paypal IPN (Received Payment)');
			$this->email->message($body);	
			$this->email->send();
		}
	}
}