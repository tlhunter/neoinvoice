<?php
class User extends App_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("user_model");
	}

	function list_items($page = 0, $sort_col = '') {
		$this->load->helper('table_sort_helper');
		$data['users'] = $this->user_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page'], TRUE, $sort_col);
		$data['total'] = $this->user_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['sort_column'] = $sort_col ? $sort_col : 'name';
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('user/xhr_list_items', $data);
	}

	function edit($user_id) {
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->update) {
			$data['user'] = $this->user_model->select_single($user_id);
			$data['usergroup_dropdown'] = dropdown_generic('usergroup', $data['user']['usergroup_id'], 'usergroup_id', 'company_id');
			$this->load->view('user/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function permission($user_id) {
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->setperms) {
			$data['permissions'] = $this->user_model->load_permissions($user_id);
			$data['user_id'] = $user_id;
			$this->load->view('user/xhr_permission', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($user_id) {
		$data['name'] = $this->input->post('name');
		$data['active'] = (boolean) $this->input->post('active');
		$data['email'] = $this->input->post('email');
		$data['usergroup_id'] = (int) $this->input->post('usergroup_id') ? : NULL;
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->update && $this->user_model->update($user_id, $data)) {
			$this->load->view('user/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function permission_submit($user_id) {
		$perms = $this->user_model->perm_template('empty');
		foreach ($_POST AS $key => $value) {
			$new_key = explode('_', $key);
			if (isset($perms[$new_key[0]][$new_key[1]])) {
				$perms[$new_key[0]][$new_key[1]] = TRUE;
			}
		}
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->update && $this->user_model->update_permissions($user_id, $perms)) {
			$this->load->view('user/xhr_permission_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete($user_id) {
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->delete) {
			$data['user'] = $this->user_model->select_single($user_id);
			$this->load->view('user/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_user');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($user_id) {
		if ($this->security_model->own_user($user_id) && $this->perm_user->user->delete && $this->user_model->delete($user_id)) {
			$data['message'] = $this->lang->line('user_deleted');
			$this->load->view('user/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_user');
			$this->load->view('xhr_error', $data);
		}
	}
	
	function list_tree($initial = '') {
		$teammates = $this->user_model->select_multiple($this->session->userdata('company_id'), NULL, NULL, FALSE);
		$data['teammate_groups'] = array_regroup($teammates, 'usergroup_name');
		if ($initial)
			$data['no_tree'] = true;
		$this->load->view('user/xhr_display_tree', $data);
	}

	function toolbar() {
		$this->load->view('xhr_teammate_toolbar');
	}

	function view($user_id) {
		if ($this->perm_user->user->update) {
			$data['toolbar'] = TRUE;
		} else {
			$data['toolbar'] = FALSE;
		}
		$data['user'] = $this->user_model->select_single($user_id);
		$this->load->view('user/xhr_view', $data);
	}

	function add() {
		if ($this->perm_user->user->create) {
			$this->load->model("company_model");
			$data['company'] = $this->company_model->select_single($this->session->userdata('company_id'));
			$data['rand_pass'] = $this->rm_user->generate_pass();
			$data['usergroup_dropdown'] = dropdown_generic('usergroup', 0, 'usergroup_id', 'company_id');
			if ($data['company']['user_count'] <= $data['company']['service']['pref_max_user']) {
				$this->load->view('user/xhr_add', $data);
			} else {
				$this->load->view('user/xhr_add_upgrade_first', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->user->create) {
			$data['name'] = $this->input->post('name');
			$data['username'] = $this->input->post('username');
			$data['password'] = $this->input->post('password');
			$data['permissions'] = json_encode($this->user_model->pref_template('standard'));
			$data['email'] = $this->input->post('email');
			$data['company_id'] = $this->session->userdata('company_id');
			$data['usergroup_id'] = (int) $this->input->post('usergroup_id') ? : NULL;
			if (!$this->rm_user->can_create($data)) {
				$data['message'] = $this->rm_user->error_message;
				$this->load->view('xhr_error', $data);
			} else if ($this->rm_user->create($data)) {
				$this->multicache->delete("list_user_by_company:{$data['company_id']}");

                if ($data['email']) {
                    $this->load->library('email');
                    $this->email->from($this->session->userdata('email'));
                    $this->email->to($data['email']);
                    $this->email->subject('New NeoInvoice Account');
                    $this->email->message("Congratulations! You have been added to a NeoInvoice company account.\r\n\r\nSimply browse to www.neoinvoice.com and login with your new credentials to get started.\r\nUsername: {$data['username']}\r\nPassword: {$data['password']}");
                    $this->email->send();
                }

				$data['message'] = $this->lang->line('user_added');
				$this->load->view('user/xhr_add_submit', $data);
			} else {
				$data['error'] = $this->lang->line('error_create_user');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

}