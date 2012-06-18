<?php
class App extends App_Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->load->library("document");
		$this->load->library("template");
		$this->load->model('company_model');
		$data = $this->document->generate_page_data();
		$data['permissions'] = $this->perm_user;
		$data['company'] = $this->company_model->select_single_simple($this->session->userdata('company_id'));
		$this->load->view('app_gui', $data);
	}

	function motd() {
		if (xhr_request()) {
			$this->output->cache(10);
			$this->load->model("wordpress_model");
			$data['updates'] = $this->wordpress_model->list_posts_by_category('updates', 1);
			$this->load->view('xhr_updates', $data);
		} else {
			die("Invalid Request");
		}
	}

	function toolbar_user() {
		$data['permissions'] = $this->perm_user;
		$this->load->view('user/xhr_toolbar_admin', $data);
	}
	
	function toolbar_project() {
		$data['permissions'] = $this->perm_user;
		$this->load->view('client_project/xhr_toolbar_admin', $data);
	}

	function toolbar_invoice() {
		$data['permissions'] = $this->perm_user;
		$this->load->view('invoice/xhr_toolbar_admin', $data);
	}
	
	function toolbar_ticket() {
		$data['permissions'] = $this->perm_user;
		$this->load->view('ticket/xhr_toolbar_admin', $data);
	}

	function dashboard() {
		$data['permissions'] = $this->perm_user;
		$this->load->view('xhr_dashboard', $data);
	}

	function preferences() {
		$this->load->model("user_model");
		$data['preferences'] = $this->user_model->load_preferences($this->session->userdata('id'));
		$data['user'] = $this->user_model->select_single($this->session->userdata('id'));
		$this->load->view('user/xhr_pref_edit', $data);
	}

	function preferences_submit() {
		$this->load->model("user_model");
		$error = FALSE;
		$data['settings']['name'] = $this->input->post('name');
		$data['settings']['email'] = $this->input->post('email');
		if ($this->input->post('password')) {
			if ($this->input->post('password') == $this->input->post('password2')) {
				$data['settings']['password'] = $this->input->post('password');
			} else {
				$data['error'] = $this->lang->line('error_password_mismatch');
				$error = TRUE;
			}
		}
		$data['preferences']['language'] = $this->input->post('language');
		$data['preferences']['per_page'] = (int) $this->input->post('per_page');
		if ($error) {
			$this->load->view('xhr_error', $data);
		} else {
			if ($this->user_model->update($this->session->userdata('id'), $data['settings'])) {
				if ($this->user_model->update_preferences($this->session->userdata('id'), $data['preferences'])) {
					$this->load->view('user/xhr_pref_edit_submit', $data);
				} else {
					$data['error'] = $this->lang->line('error_user_pref');
					$this->load->view('xhr_error', $data);
				}
			} else {
				$data['error'] = $this->lang->line('error_user_settings');
				$this->load->view('xhr_error', $data);
			}
		}
	}
}