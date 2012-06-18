<?php
class Ticketcategory extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("ticketcategory_model");
	}

	function list_items($page = 0) {
		$data['ticketcategories'] = $this->ticketcategory_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page']);
		$data['total'] = $this->ticketcategory_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('ticketcategory/xhr_list_items', $data);
	}

	function delete($ticketcategory_id) {
		if ($this->security_model->own_ticketcategory($ticketcategory_id) && $this->perm_user->tickettype->delete) {
			$data['ticketcategory'] = $this->ticketcategory_model->select_single($ticketcategory_id);
			$this->load->view('ticketcategory/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_ticketcategory');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($ticketcategory_id) {
		if ($this->security_model->own_ticketcategory($ticketcategory_id) && $this->perm_user->tickettype->delete && $this->ticketcategory_model->delete($ticketcategory_id)) {
			$data['message'] = $this->lang->line('ticketcategory_deleted');
			$this->load->view('ticketcategory/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_ticketcategory');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->tickettype->create) {
			$this->load->view('ticketcategory/xhr_add');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->tickettype->create) {
			$data['name'] = htmlentities($this->input->post('name'));
			$data['description'] = htmlentities($this->input->post('description'));
			$data['company_id'] = $this->session->userdata('company_id');
			if ($this->ticketcategory_model->insert($data)) {
				$data['message'] = $this->lang->line('ticketcategory_added');
				$this->load->view('ticketcategory/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_ticketcategory');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($ticketcategory_id) {
		if ($this->security_model->own_ticketcategory($ticketcategory_id) && $this->perm_user->tickettype->update) {
			$data['ticketcategory'] = $this->ticketcategory_model->select_single($ticketcategory_id);
			$this->load->view('ticketcategory/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_ticketcategory');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($ticketcategory_id) {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['description'] = htmlentities($this->input->post('description'));
		$data['company_id'] = (int) $this->session->userdata('company_id');
		if ($this->security_model->own_ticketcategory($ticketcategory_id) && $this->perm_user->tickettype->update && $this->ticketcategory_model->update($ticketcategory_id, $data)) {
			$this->load->view('ticketcategory/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_ticketcategory');
			$this->load->view('xhr_error', $data);
		}
	}


}