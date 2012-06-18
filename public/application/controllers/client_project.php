<?php
class Client_project extends App_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("client_model");
		$this->load->model("project_model");
	}

	function list_tree($initial = '') {
		$data['projects'] = $this->project_model->select_multiple($this->session->userdata('company_id'), 0, 0, FALSE, 'name');
		$data['clients'] = $this->client_model->select_multiple($this->session->userdata('company_id'), 0, 0, FALSE, 'name');
		if ($initial)
			$data['no_tree'] = true;
		$this->load->view('client_project/xhr_display_tree', $data);
	}


}