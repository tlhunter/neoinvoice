<?php
class Ticketstage extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("ticketstage_model");
	}

	function list_items($page = 0, $sort_col = '') {
		$this->load->helper('table_sort_helper');
		$data['ticketstages'] = $this->ticketstage_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page']);
		$data['total'] = $this->ticketstage_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['per_page'] = $this->pref_user['per_page'];
		$data['sort_column'] = $sort_col ? : 'name';
		$this->load->view('ticketstage/xhr_list_items', $data);
	}

	function delete($ticketstage_id) {
		if ($this->security_model->own_ticketstage($ticketstage_id) && $this->perm_user->tickettype->delete) {
			$data['ticketstage'] = $this->ticketstage_model->select_single($ticketstage_id);
			$this->load->view('ticketstage/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_ticketstage');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($ticketstage_id) {
		if ($this->security_model->own_ticketstage($ticketstage_id) && $this->perm_user->tickettype->delete && $this->ticketstage_model->delete($ticketstage_id)) {
			$data['message'] = $this->lang->line('ticketstage_deleted');
			$this->load->view('ticketstage/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_ticketstage');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->tickettype->create) {
			$data['ticket_category_dropdown'] = dropdown_generic('ticket_category', 0, 'ticket_category_id');
			$this->load->view('ticketstage/xhr_add', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->tickettype->create) {
			$data['name'] = htmlentities($this->input->post('name'));
			$data['description'] = htmlentities($this->input->post('description'));
			$data['ticket_category_id'] = (int) $this->input->post('ticket_category_id');
			$data['company_id'] = $this->session->userdata('company_id');
			$data['closed'] = post_checkbox('closed');
			if ($this->ticketstage_model->insert($data)) {
				$data['message'] = $this->lang->line('ticketstage_added');
				$this->load->view('ticketstage/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_ticketstage');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($ticketstage_id) {
		if ($this->security_model->own_ticketstage($ticketstage_id) && $this->perm_user->tickettype->update) {
			$data['ticketstage'] = $this->ticketstage_model->select_single($ticketstage_id);
			$data['ticket_category_dropdown'] = dropdown_generic('ticket_category', $data['ticketstage']['ticket_category_id'], 'ticket_category_id');
			$this->load->view('ticketstage/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_ticketstage');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($ticketstage_id) {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['description'] = htmlentities($this->input->post('description'));
		$data['ticket_category_id'] = (int) $this->input->post('ticket_category_id');
		$data['company_id'] = (int) $this->session->userdata('company_id');
		$data['closed'] = post_checkbox('closed');
		if ($this->security_model->own_ticketstage($ticketstage_id) && $this->perm_user->tickettype->update && $this->ticketstage_model->update($ticketstage_id, $data)) {
			$this->load->view('ticketstage/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_ticketstage');
			$this->load->view('xhr_error', $data);
		}
	}


}