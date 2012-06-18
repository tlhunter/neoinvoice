<?php
class Client extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("client_model");
	}

	function list_items($page = 0, $sort_col = '') {
		$this->load->helper('table_sort_helper');
		$data['clients'] = $this->client_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page'], TRUE, $sort_col);
		$data['total'] = $this->client_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['sort_column'] = $sort_col ? $sort_col : 'name';
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('client/xhr_list_items', $data);
	}

	function view($client_id) {
		if ($this->security_model->own_client($client_id)) {
			$data['client'] = $this->client_model->select_single($client_id);
			$this->load->view('client/xhr_view', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_client');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete($client_id) {
		if ($this->security_model->own_client($client_id) && $this->perm_user->client->delete) {
			$data['client'] = $this->client_model->select_single($client_id);
			$this->load->view('client/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_client');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($client_id) {
		if ($this->security_model->own_client($client_id) && $this->perm_user->client->delete && $this->client_model->delete($client_id)) {
			$this->load->view('client/xhr_delete_submit');
		} else {
			$data['error'] = $this->lang->line('error_delete_client');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->client->create) {
			$this->load->view('client/xhr_add');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['active'] = $this->input->post('active');
		$data['email'] = $this->input->post('email');
		$data['phone'] = htmlentities($this->input->post('phone'));
		$data['address'] = htmlentities($this->input->post('address'));
		$data['company_id'] = $this->session->userdata('company_id');
		if ($data['name'] && $this->perm_user->client->create && $this->client_model->insert($data)) {
			$data['message'] = $this->lang->line('client_added');
			$this->load->view('client/xhr_add_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_create_client');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($client_id) {
		if ($this->security_model->own_client($client_id) && $this->perm_user->client->update) {
			$data['client'] = $this->client_model->select_single($client_id);
			$this->load->view('client/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_client');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($client_id) {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['active'] = $this->input->post('active');
		$data['email'] = $this->input->post('email');
		$data['phone'] = htmlentities($this->input->post('phone'));
		$data['address'] = htmlentities($this->input->post('address'));
		if ($data['name'] && $this->security_model->own_client($client_id) && $this->perm_user->client->update && $this->client_model->update($client_id, $data)) {
			$this->load->view('client/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_client');
			$this->load->view('xhr_error', $data);
		}
	}

}