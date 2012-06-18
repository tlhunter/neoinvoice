<?php
/**
 * @todo fix up code, only pasted from segment, replace, delete some functions.
 */
class Expense extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("expense_model");
	}

	/**
	 * @param int $page First record (0)
	 * @deprecated
	 */
	function list_items($page = 0) {
		/* Probably not going to use this */
	}

	/**
	 *
	 * @param int $project_id ID of project
	 * @param int $page Starting record (0)
	 * @abstract Displays a list of all expenses belonging to a specific project
	 */
	function list_by_project($project_id, $page = 0) {
		$this->load->helper('table_sort_helper');
		if ($this->security_model->own_project($project_id)) {
			$data['expenses'] = $this->expense_model->select_multiple_project($project_id, $page, $this->pref_user['per_page'], TRUE);
			$data['total'] = $this->expense_model->get_total_project($project_id);
			$data['page'] = $page;
			$data['per_page'] = $this->pref_user['per_page'];
			$data['project_id'] = $project_id;
			$this->load->view('expense/xhr_list_items_project', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	/**
	 *
	 * @param in $expense_id Expense ID
	 * @abstract Displays the specified expense
	 */
	function view($expense_id) {
		if ($this->security_model->own_expense($expense_id)) {
			$data['expense'] = $this->expense_model->select_single($expense_id);
			$this->load->view('expense/xhr_view', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	/**
	 *
	 * @param int $expense_id Expense ID
	 * @abstract Prompts the user if they are sure they want to delete the specified expense
	 */
	function delete($expense_id) {
		if ($this->security_model->own_expense($expense_id) && $this->perm_user->expense->delete) {
			$data['expense'] = $this->expense_model->select_single($expense_id);
			$this->load->view('expense/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	/**
	 *
	 * @param int $expense_id Expense ID
	 * @abstract Deletes the specified expense
	 */
	function delete_submit($expense_id) {
		if ($this->security_model->own_expense($expense_id) && $this->perm_user->expense->delete && $this->expense_model->delete($expense_id)) {
			$data['message'] = $this->lang->line('expense_deleted');
			$this->load->view('expense/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_multiple($expense_ids) {
		$data['expense_ids'] = explode(':', $expense_ids);
		if ($this->security_model->own_expenses($data['expense_ids']) && $this->perm_user->expense->delete) {
			$data['count'] = count($data['expense_ids']);
			$data['expense_ids'] = $expense_ids;
			$this->load->view('expense/xhr_delete_multiple', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expenses');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_multiple_submit($expense_ids) {
		$data['expense_ids'] = explode(':', $expense_ids);
		if ($this->security_model->own_expenses($data['expense_ids']) && $this->perm_user->expense->delete && $this->expense_model->delete_multiple($data['expense_ids'])) {
			$data['message'] = $this->lang->line('expenses_deleted');
			$this->load->view('expense/xhr_delete_multiple_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_expenses');
			$this->load->view('xhr_error', $data);
		}
	}

	function add($project_id = 0) {
		$this->load->model('expensetype_model');
		$this->load->model('project_model');
		if ($this->perm_user->expense->create) {
			$data['project_dropdown'] = dropdown_generic('project', $project_id, 'project_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['expensetype_dropdown'] = dropdown_generic('expensetype', null, 'expensetype_id');
			$data['project_id'] = $project_id;
			$data['count_expensetypes'] = $this->expensetype_model->get_total($this->session->userdata('company_id'));
			$data['count_projects'] = $this->project_model->get_total($this->session->userdata('company_id'));
			$this->load->view('expense/xhr_add', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		$data['date'] = $this->input->post('date');
		$data['project_id'] = (int) $this->input->post('project_id');
		$data['expensetype_id'] = (int) $this->input->post('expensetype_id');
		$data['billable'] = post_checkbox('billable');
		$data['content'] = htmlentities($this->input->post('content'));
		$data['amount'] = (float) $this->input->post('amount');
		$data['company_id'] = (int) $this->session->userdata('company_id');
		if ($data['project_id'] && $data['expensetype_id'] && $this->perm_user->expense->create && $this->expense_model->insert($data)) {
			$data['message'] = $this->lang->line('expense_added');
			$this->load->view('expense/xhr_add_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_create_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($expense_id) {
		if ($this->security_model->own_expense($expense_id) && $this->perm_user->expense->update) {
			$data['expense'] = $this->expense_model->select_single($expense_id);
			$data['project_dropdown'] = dropdown_generic('project', $data['expense']['project_id'], 'project_id');
			$data['expensetype_dropdown'] = dropdown_generic('expensetype', $data['expense']['expensetype_id'], 'expensetype_id');
			$this->load->view('expense/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_expense');
			$this->load->view('xhr_error', $data);
		}
	}

	/**
	 * @todo fix up this code, make it more stable
	 */
	function edit_submit($expense_id) {
		$data['date'] = $this->input->post('date');
		$data['project_id'] = (int) $this->input->post('project_id');
		$data['expensetype_id'] = (int) $this->input->post('expensetype_id');
		$data['billable'] = post_checkbox('billable');
		$data['content'] = htmlentities($this->input->post('content'));
		$data['amount'] = (float) $this->input->post('amount');
		$data['company_id'] = (int) $this->session->userdata('company_id');
		if ($this->security_model->own_expense($expense_id) && $this->perm_user->expense->update && $this->expense_model->update($expense_id, $data)) {
			$this->load->view('expense/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_expense');
			$this->load->view('xhr_error', $data);
		}
	}
}