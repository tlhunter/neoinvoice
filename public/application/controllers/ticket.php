<?php
/**
 * @todo pasted in code from user controller, started making changes, but far far from complete
 */
class Ticket extends App_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("ticket_model");
	}

	function list_items($page = 0, $sort_col = '') {
		$this->load->helper('table_sort_helper');
		$data['tickets'] = $this->ticket_model->select_multiple($this->session->userdata('id'), $page, $this->pref_user['per_page'], TRUE, $sort_col);
		$data['total'] = $this->ticket_model->get_total($this->session->userdata('id'));
		$data['page'] = $page;
		$data['sort_column'] = $sort_col ? $sort_col : 'name';
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('ticket/xhr_list_items', $data);
	}
	
	function edit($ticket_id) {
		if ($this->security_model->own_ticket($ticket_id) && $this->perm_user->ticket->update) {
			$data['ticket'] = $this->ticket_model->select_single($ticket_id);
			$data['project_dropdown'] = dropdown_generic('project', $data['ticket']['project_id'], 'project_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['user_dropdown'] = dropdown_generic('user', $data['ticket']['assigned_user_id'], 'assigned_user_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['usergroup_dropdown'] = dropdown_generic('usergroup', $data['ticket']['assigned_usergroup_id'], 'assigned_usergroup_id');
			$data['ticket_category_dropdown'] = dropdown_generic('ticket_category', $data['ticket']['ticket_category_id'], 'ticket_category_id');
			if ($data['ticket']['ticket_category_id']) {
				$data['ticket_stage_dropdown'] = dropdown_generic('ticket_stage', $data['ticket']['ticket_stage_id'], 'ticket_stage_id', 'company_id', 0, 'id', 'name', array('ticket_category_id' => $data['ticket']['ticket_category_id']));
			} else {
				$data['ticket_stage_dropdown'] = "Select a Category";
			}
			$this->load->view('ticket/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_ticket');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($ticket_id) {
		$data['name'] = htmlentities($this->input->post('name'));
		$data['description'] = htmlentities($this->input->post('description'));
		$data['project_id'] = $this->input->post('project_id');
		$data['assigned_user_id'] = $this->input->post('assigned_user_id') ? : NULL;
		$data['assigned_usergroup_id'] = $this->input->post('assigned_usergroup_id') ? : NULL;
		$data['created_user_id'] = $this->session->userdata('id');
		$data['ticket_category_id'] = $this->input->post('ticket_category_id') ? : NULL;
			if ($data['ticket_category_id']) {
				$data['ticket_stage_id'] = $this->input->post('ticket_stage_id');
			} else {
				$data['ticket_stage_id'] = NULL;
			}
		$data['company_id'] = $this->session->userdata('company_id');
		$data['due'] = $this->input->post('due');
		if ($this->security_model->own_ticket($ticket_id) && $this->perm_user->ticket->update && $this->ticket_model->update($ticket_id, $data)) {
			$this->load->view('ticket/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_ticket');
			$this->load->view('xhr_error', $data);
		}
	}
	
	function delete($ticket_id) {
		if ($this->security_model->own_ticket($ticket_id) && $this->perm_user->ticket->delete) {
			$data['ticket'] = $this->ticket_model->select_single($ticket_id);
			$this->load->view('ticket/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($ticket_id) {
		if ($this->security_model->own_ticket($ticket_id) && $this->perm_user->ticket->delete && $this->ticket_model->delete($ticket_id)) {
			$data['message'] = $this->lang->line('ticket_deleted');
			$this->load->view('ticket/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_ticket');
			$this->load->view('xhr_error', $data);
		}
	}

	function list_tree($initial = '') {
		$data['tickets'] = $this->ticket_model->select_multiple($this->session->userdata('company_id'), 0, 0, FALSE);
		if ($initial) {
			$data['no_tree'] = true;
		}
		$this->load->view('ticket/xhr_display_tree', $data);
	}
	
	function view($ticket_id) {
		if ($this->perm_user->ticket->update) {
			$data['toolbar'] = TRUE;
		} else {
			$data['toolbar'] = FALSE;
		}
		$data['ticket'] = $this->ticket_model->select_single($ticket_id);
		$this->load->view('ticket/xhr_view', $data);
	}

	function ticket_stage_dropdown($ticket_category_id = 0) {
		$ticket_category_id += 0;
		if (!$ticket_category_id) {
			echo "Select a Category";
		} else {
			echo dropdown_generic('ticket_stage', 0, 'ticket_stage_id', 'company_id', 0, 'id', 'name', array('ticket_category_id' => $ticket_category_id));
			echo ' <span class="required">Required</span>';
		}
	}
	
	function add() {
		if ($this->perm_user->ticket->create) {
			$data['project_dropdown'] = dropdown_generic('project', 0, 'project_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['user_dropdown'] = dropdown_generic('user', 0, 'assigned_user_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['usergroup_dropdown'] = dropdown_generic('usergroup', 0, 'assigned_usergroup_id');
			$data['ticket_category_dropdown'] = dropdown_generic('ticket_category', 0, 'ticket_category_id');
			$this->load->view('ticket/xhr_add', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->ticket->create) {
			$data['name'] = htmlentities($this->input->post('name'));
			$data['description'] = htmlentities($this->input->post('description'));
			$data['project_id'] = $this->input->post('project_id');
			$data['assigned_user_id'] = $this->input->post('assigned_user_id') ? : NULL;
			$data['assigned_usergroup_id'] = $this->input->post('assigned_usergroup_id') ? : NULL;
			$data['created_user_id'] = $this->session->userdata('id');
			$data['ticket_category_id'] = $this->input->post('ticket_category_id') ? : NULL;
			if ($data['ticket_category_id']) {
				$data['ticket_stage_id'] = $this->input->post('ticket_stage_id');
			} else {
				$data['ticket_stage_id'] = NULL;
			}
			$data['company_id'] = $this->session->userdata('company_id');
			$data['due'] = $this->input->post('due');
			if ($this->ticket_model->insert($data)) {
				$data['message'] = $this->lang->line('ticket_added');
				$this->load->view('ticket/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_ticket');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

}