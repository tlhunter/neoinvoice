<?php
/**
 * @todo fix code, just pasted and replaced from worktype
 */
class Expensetype extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("expensetype_model");
	}

	function list_items($page = 0) {
		$data['expensetypes'] = $this->expensetype_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page']);
		$data['total'] = $this->expensetype_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('expensetype/xhr_list_items', $data);
	}

	function delete($expensetype_id) {
		if ($this->security_model->own_expensetype($expensetype_id) && $this->perm_user->expensetype->delete) {
			$data['expensetype'] = $this->expensetype_model->select_single($expensetype_id);
			$this->load->view('expensetype/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expensetype');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($expensetype_id) {
		if ($this->security_model->own_expensetype($expensetype_id) && $this->perm_user->expensetype->delete && $this->expensetype_model->delete($expensetype_id)) {
			$data['message'] = $this->lang->line('expensetype_deleted');
			$this->load->view('expensetype/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expensetype');
			$this->load->view('xhr_error', $data);
		}
	}

	function add() {
		if ($this->perm_user->expensetype->create) {
			$this->load->view('expensetype/xhr_add');
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->expensetype->create) {
			$data['name'] = $this->input->post('name');
			$data['content'] = $this->input->post('content');
			$data['taxable'] = post_checkbox('taxable');
			$data['company_id'] = $this->session->userdata('company_id');
			if ($this->expensetype_model->insert($data)) {
				$data['message'] = $this->lang->line('expensetype_added');
				$this->load->view('expensetype/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_expensetype');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($expensetype_id) {
		if ($this->security_model->own_expensetype($expensetype_id) && $this->perm_user->expensetype->update) {
			$data['expensetype'] = $this->expensetype_model->select_single($expensetype_id);
			$this->load->view('expensetype/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_expensetype');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($expensetype_id) {
		$data['name'] = $this->input->post('name');
		$data['content'] = $this->input->post('content');
		$data['taxable'] = post_checkbox('taxable');
		$data['company_id'] = (int) $this->session->userdata('company_id');
		if ($this->security_model->own_expensetype($expensetype_id) && $this->perm_user->expensetype->update && $this->expensetype_model->update($expensetype_id, $data)) {
			$this->load->view('expensetype/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_expensetype');
			$this->load->view('xhr_error', $data);
		}
	}


}