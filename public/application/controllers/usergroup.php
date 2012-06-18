<?php
class Usergroup extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("usergroup_model");
	}

	function list_items($page = 0) {
		$data['usergroups'] = $this->usergroup_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page']);
		$data['total'] = $this->usergroup_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('usergroup/xhr_list_items', $data);
	}

	function delete($usergroup_id) {
		if ($this->security_model->own_usergroup($usergroup_id) && $this->perm_user->usergroup->delete) {
			$data['usergroup'] = $this->usergroup_model->select_single($usergroup_id);
			$this->load->view('usergroup/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_usergroup');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($usergroup_id) {
		if ($this->security_model->own_usergroup($usergroup_id) && $this->perm_user->usergroup->delete && $this->usergroup_model->delete($usergroup_id)) {
			$data['message'] = $this->lang->line('usergroup_deleted');
			$this->load->view('usergroup/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_usergroup');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->usergroup->create) {
			$this->load->view('usergroup/xhr_add');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->usergroup->create) {
			$data['name'] = $this->input->post('name');
			$data['content'] = $this->input->post('content');
			$data['company_id'] = $this->session->userdata('company_id');
			if ($this->usergroup_model->insert($data)) {
				$data['message'] = $this->lang->line('usergroup_added');
				$this->load->view('usergroup/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_usergroup');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($usergroup_id) {
		if ($this->security_model->own_usergroup($usergroup_id) && $this->perm_user->usergroup->update) {
			$data['usergroup'] = $this->usergroup_model->select_single($usergroup_id);
			$this->load->view('usergroup/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_usergroup');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($usergroup_id) {
		$data['name'] = $this->input->post('name');
		$data['content'] = $this->input->post('content');
		if ($this->security_model->own_usergroup($usergroup_id) && $this->perm_user->usergroup->update && $this->usergroup_model->update($usergroup_id, $data)) {
			$this->load->view('usergroup/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_usergroup');
			$this->load->view('xhr_error', $data);
		}
	}


}