<?php
class Project extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("project_model");
	}

	function list_items($page = 0, $sort_col = '') {
		$this->load->helper('table_sort_helper');
		$data['projects'] = $this->project_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page'], TRUE, $sort_col);
		$data['total'] = $this->project_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['sort_column'] = $sort_col ? $sort_col : 'name';
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('project/xhr_list_items', $data);
	}

	function list_by_client($client_id, $page = 0, $sort_col = '') {
		if ($this->security_model->own_client($client_id)) {
			$data['projects'] = $this->project_model->select_multiple_client($client_id);
			$this->load->view('project/xhr_list_projects', $data);
		} else {
			
		}
	}

	function view($project_id) {
		if ($this->security_model->own_project($project_id)) {
			$data['project'] = $this->project_model->select_single($project_id);
			$this->load->view('project/xhr_view', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_project');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete($project_id) {
		if ($this->security_model->own_project($project_id) && $this->perm_user->project->delete) {
			$data['project'] = $this->project_model->select_single($project_id);
			$this->load->view('project/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_project');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($project_id) {
		if ($this->security_model->own_project($project_id) && $this->perm_user->project->delete && $this->project_model->delete($project_id)) {
			$data['message'] = $this->lang->line('project_deleted');
			$this->load->view('project/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_project');
			$this->load->view('xhr_error', $data);
		}
	}

	function add($client_id = 0) {
		if ($this->perm_user->project->create) {
			$this->load->model('client_model');
			$data['client_dropdown'] = dropdown_generic('client', $client_id, 'client_id', 'company_id', $this->session->userdata('company_id'), 'id', 'name', array('active' => 1));
			$data['count'] = $this->client_model->get_total($this->session->userdata('company_id'));
			$this->load->view('project/xhr_add', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['active'] = $this->input->post('active');
		$data['client_id'] = $this->input->post('client_id');
		$data['company_id'] = $this->session->userdata('company_id');
		$data['content'] = htmlentities($this->input->post('content'));
		if ($data['client_id'] && $data['name'] && $this->perm_user->project->create && $this->project_model->insert($data)) {
			$data['message'] = $this->lang->line('project_added');
			$this->load->view('project/xhr_add_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_create_project');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($project_id) {
		if ($this->security_model->own_project($project_id) && $this->perm_user->project->update) {
			$data['project'] = $this->project_model->select_single($project_id);
			$data['client_dropdown'] = dropdown_generic('client', $data['project']['client_id'], 'client_id');
			$this->load->view('project/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_project');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($project_id) {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['active'] = $this->input->post('active');
		$data['client_id'] = $this->input->post('client_id');
		$data['company_id'] = $this->session->userdata('company_id');
		$data['content'] = htmlentities($this->input->post('content'));
		if ($data['client_id'] && $data['name'] && $this->security_model->own_project($project_id) && $this->perm_user->project->update && $this->project_model->update($project_id, $data)) {
			$this->load->view('project/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_project');
			$this->load->view('xhr_error', $data);
		}
	}

}