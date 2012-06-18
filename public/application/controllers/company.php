<?php
class Company extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('company_model');
		$this->load->library('paypal');
	}

	function preferences() {
		if ($this->perm_user->company->update) {
			$data['preferences'] = $this->company_model->load_preferences($this->session->userdata('company_id'));
			$data['company'] = $this->company_model->select_single($this->session->userdata('company_id'));
			$data['logo_image'] = $this->company_model->get_logo_image($this->session->userdata('company_id'));
			$this->load->view('company/xhr_pref_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_company_settings');
			$this->load->view('xhr_error', $data);
		}
	}

	function preferences_submit() {
		$data['settings']['name'] = htmlentities($this->input->post('name'));
		$data['settings']['invoice_address'] = htmlentities($this->input->post('invoice_address'));
		$data['preferences']['language'] = $this->input->post('language');
		if ($this->perm_user->company->update && $this->company_model->update($this->session->userdata('company_id'), $data['settings'])) {
			if ($this->company_model->update_preferences($this->session->userdata('company_id'), $data['preferences'])) {
				$this->load->view('company/xhr_pref_edit_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_company_pref');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_company_settings');
			$this->load->view('xhr_error', $data);
		}
	}
    
    function logo() {
        $this->load->library('template');
        $data = array();
        $this->template->load('iframe_template', 'company/htm_logo', $data);
    }
    
    function logo_submit() {
        $this->load->library('template');
        $data = array();
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] == 0) {
            $data['message'] = "Your image has been successfully uploaded!";
            $data['success'] = $this->company_model->set_logo_image($this->session->userdata('company_id'), $_FILES['logo_image']['tmp_name']);
            if (!$data['success']) {
                $data['message'] = 'Your image was uploaded, but it it either too big or too small or not a JPG.';
            }
        } else {
            $data['message'] = "There was an error uploading your image. Please try again or select a different image file.";
            $data['success'] = FALSE;
        }
        $this->template->load('iframe_template', 'company/htm_logo_submit', $data);
    }
    
    function logo_delete() {
        if ($this->perm_user->company->update) {
			$this->load->view('company/xhr_logo_delete');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
    }
    
    function logo_delete_submit() {
        if ($this->perm_user->company->update) {
            $this->company_model->remove_logo_image($this->session->userdata('company_id'));
			$this->load->view('company/xhr_logo_delete_submit');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
    }

	function upgrade() {
		if ($this->perm_user->company->upgrade) {

			$this->paypal->add_field('custom', "company_id:{$this->session->userdata('company_id')}");
			$this->paypal->add_field('item_name', 'NeoInvoice Agency Account Upgrade');
			$this->paypal->add_field('item_number', '2');
			$this->paypal->add_field('amount', '9.99');
			$this->paypal->button('Agency Account ($9.99 / mo)');
			$data['paypal_form_agency'] = $this->paypal->paypal_form('payment_form_agency', TRUE);

			$this->paypal->add_field('custom', "company_id:{$this->session->userdata('company_id')}");
			$this->paypal->add_field('item_name', 'NeoInvoice Corporate Account Upgrade');
			$this->paypal->add_field('item_number', '3');
			$this->paypal->add_field('amount', '49.99');
			$this->paypal->button('Corporate Account ($49.99 / mo)');
			$data['paypal_form_corp'] = $this->paypal->paypal_form('payment_form_corp', TRUE);

			$this->load->view('payment/form', $data);
		} else {
			$data['error'] = $this->lang->line('error_company_upgrade');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete() {
		if ($this->perm_user->company->delete) {
			$this->load->view('company/xhr_delete');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit() {
		if ($this->perm_user->company->delete) {
			$this->company_model->delete_mark($this->session->userdata('company_id'));
			#function will NOT delete company, but will instead put up a one week expiration
			#administrators will be able to cancel the deletion before then
			#admins and users will see a message at the top of the screen alerting them
			$this->load->view('company/xhr_delete_submit');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_cancel() {
		$this->company_model->delete_cancel($this->session->userdata('company_id'));
		$this->load->view('company/xhr_delete_cancel');
	}
}