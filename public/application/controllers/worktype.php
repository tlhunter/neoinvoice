<?php
class Worktype extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("worktype_model");
	}

	function list_items($page = 0) {
		$data['worktypes'] = $this->worktype_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page']);
		$data['total'] = $this->worktype_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('worktype/xhr_list_items', $data);
	}

	function delete($worktype_id) {
		if ($this->security_model->own_worktype($worktype_id) && $this->perm_user->worktype->delete) {
			$data['worktype'] = $this->worktype_model->select_single($worktype_id);
			$this->load->view('worktype/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_worktype');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($worktype_id) {
		if ($this->security_model->own_worktype($worktype_id) && $this->perm_user->worktype->delete && $this->worktype_model->delete($worktype_id)) {
			$data['message'] = $this->lang->line('worktype_deleted');
			$this->load->view('worktype/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_worktype');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->worktype->create) {
			$this->load->view('worktype/xhr_add');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->worktype->create) {
			$data['name'] = $this->input->post('name');
			$data['content'] = $this->input->post('content');
			$data['hourlyrate'] = (float) $this->input->post('hourlyrate');
			$data['company_id'] = $this->session->userdata('company_id');
			if ($this->worktype_model->insert($data)) {
				$data['message'] = $this->lang->line('worktype_added');
				$this->load->view('worktype/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_worktype');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($worktype_id) {
		if ($this->security_model->own_worktype($worktype_id) && $this->perm_user->worktype->update) {
			$data['worktype'] = $this->worktype_model->select_single($worktype_id);
			$this->load->view('worktype/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_worktype');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($worktype_id) {
		$data['name'] = $this->input->post('name');
		$data['content'] = $this->input->post('content');
		$data['hourlyrate'] = (float) $this->input->post('hourlyrate');
		$data['company_id'] = (int) $this->session->userdata('company_id');
		if ($this->security_model->own_worktype($worktype_id) && $this->perm_user->worktype->update && $this->worktype_model->update($worktype_id, $data)) {
			$this->load->view('worktype/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_worktype');
			$this->load->view('xhr_error', $data);
		}
	}


}