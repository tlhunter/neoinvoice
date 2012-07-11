<?php
class Invoice extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("invoice_model");
	}

	function list_items($page = 0, $sort_col = 'name') {
		$this->load->helper('table_sort_helper');
		$sort_col = preg_replace('/[^a-z\-_]*/','', $sort_col); //remove invalid characters to prevent SQL-injections
		$data['invoices'] = $this->invoice_model->select_multiple($this->session->userdata('company_id'), $page, $this->pref_user['per_page'], TRUE, $sort_col);
		$data['total'] = $this->invoice_model->get_total($this->session->userdata('company_id'));
		$data['page'] = $page;
		$data['sort_column'] = $sort_col;
		$data['per_page'] = $this->pref_user['per_page'];
		$this->load->view('invoice/xhr_list_items', $data);
	}

	function list_by_project($project_id, $page = 0, $sort_col = '') {
		$this->load->view('invoice/xhr_list_items_project', $data);
	}

	function list_by_client($client_id, $page = 0, $sort_col = '') {
		$this->load->view('invoice/xhr_list_items_client', $data);
	}

	function list_pending($page = 0, $sort_col = '') {
		$this->load->view('invoice/xhr_list_items', $data);
	}

	function list_tree($initial = '') {
		$data['invoices'] = $this->invoice_model->select_multiple($this->session->userdata('company_id'));
		if ($initial)
			$data['no_tree'] = true;
		$this->load->view('invoice/xhr_display_tree', $data);
	}

	function view($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id)) {
			$this->load->model('payment_model');
			$this->load->model('expense_model');
			$data['invoice'] = $this->invoice_model->select_single($invoice_id);
			$data['segments'] = $this->invoice_model->select_segments_by_invoice($invoice_id);
			$data['payments'] = $this->payment_model->select_multiple($invoice_id);
			$data['expenses'] = $this->expense_model->select_multiple($invoice_id);
			$this->load->view('invoice/xhr_view', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function download_pdf($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id)) {
			$this->invoice_model->generate_pdf($invoice_id, true, 'invoice.pdf');
		} else {
			$data['error'] = $this->lang->line('error_select_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function send($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->send) {
			$data['invoice'] = $this->invoice_model->select_single($invoice_id);
			$data['sender'] = $this->session->userdata('name') . " &lt;" . $this->session->userdata('email') . "&gt;";
			$data['remain'] = $this->invoice_model->select_email_remain_month($this->session->userdata('company_id'));
			$this->load->view('invoice/xhr_send', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function send_submit($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->send && $this->invoice_model->select_email_remain_month($this->session->userdata('company_id'))) {
			$message = htmlentities($this->input->post('content'));
			$this->invoice_model->mail_invoice($invoice_id, $this->input->post('recipient'), nl2br($message), 'New Invoice', 'You have a new invoice due.', post_checkbox('copy_self'));

			$data['message'] = $this->lang->line("invoice_sent");
			$this->load->view('invoice/xhr_send_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->delete) {
			$data['invoice'] = $this->invoice_model->select_single($invoice_id);
			$this->load->view('invoice/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->delete && $this->invoice_model->delete($invoice_id)) {
			$data['message'] = $this->lang->line('invoice_deleted');
			$this->load->view('invoice/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function add($client_id = 0, $project_ids = '') {
		if (($client_id && !$this->security_model->own_client($client_id)) || !$this->perm_user->invoice->create) {
			$this->security_model->warn_user($this->session->userdata('id'));
			$data['error'] = $this->lang->line('error_load_client');
			$this->load->view('xhr_error', $data);
		} else if (!$client_id && !$project_ids) { # STEP 1
			$this->load->model("client_model");
			$data['clients'] = $this->client_model->select_active($this->session->userdata('company_id'));
			$this->load->view('invoice/xhr_add', $data);
		} else if ($client_id && !$project_ids) { # STEP 2
			$this->load->model("project_model");
			$data['projects'] = $this->project_model->select_multiple_client($client_id, TRUE);
			$data['client_id'] = $client_id;
			$this->load->view('invoice/xhr_add_step-2', $data);
		} else if ($client_id && $project_ids) { # STEP 3
			$this->load->model("client_model");
			$this->load->model("project_model");
			$this->load->model("segment_model");
			$this->load->model("expense_model");
			$data['client'] = $this->client_model->select_single($client_id);
			$data['project_ids'] = explode(':', $project_ids);
			$data['projects'] = $this->project_model->select_multiple_by_ids($data['project_ids'], $this->session->userdata('company_id'));
			$data['default_invoice_name'] = $data['client']['name'] . ' ' . date('Y-m-d');
			foreach($data['project_ids'] AS $project_id) {
				$data['segments'][$project_id] = $this->segment_model->select_available_project($project_id);
				$data['expenses'][$project_id] = $this->expense_model->select_available_project($project_id);
			}
			$this->load->view('invoice/xhr_add_step-3', $data);
		}
	}

	function add_submit() {
		if ($this->perm_user->invoice->create) {
			$this->load->model("segment_model");
			$this->load->model("expense_model");
			$invoice['name'] = htmlentities($this->input->post('name'));
			$invoice['duedate'] = $this->input->post('duedate');
			$invoice['client_id'] = (int) $this->input->post('client_id');
			$invoice['amount'] = (double) $this->input->post('amount');
			$invoice['paid'] = post_checkbox('paid');
			$invoice['remind'] = post_checkbox('remind');
			if ($invoice['paid']) {
				$invoice['paiddate'] = $this->input->post('paiddate');
			} else {
				$invoice['paiddate'] = '0000-00-00';
			}
			$invoice['itemize'] = post_checkbox('itemize');
			$invoice['sent'] = post_checkbox('sent');
			$invoice['content'] = htmlentities($this->input->post('content'));
			$invoice['company_id'] = $this->session->userdata('company_id');
			$segment_ids = $this->input->post('segments');
			$can_update = TRUE;
			if (!$segment_ids || $this->security_model->own_segments($segment_ids)) {
				
			} else {
				$can_update = FALSE;
			}
			$expense_ids = $this->input->post('expenses');
			if (!$expense_ids || $this->security_model->own_expenses($expense_ids)) {

			} else {
				$can_update = FALSE;
			}

			if ($invoice['name'] && $can_update && $this->security_model->own_client($invoice['client_id'])) {
				$invoice_id = $this->invoice_model->insert($invoice);
				if ($invoice_id) {
					if ($this->segment_model->multiple_set_invoice($segment_ids, $invoice_id)) {
						$data['message'] = $this->lang->line('invoice_added');
						$data['error'] = FALSE;
						/**
				 * @todo I think this if statement is whack
				 */
						if ($expense_ids && !$this->expense_model->multiple_set_invoice($expense_ids, $invoice_id)) {
							$data['message'] = $this->lang->line('error_update_expense');
							$data['error'] = TRUE;
						}
					} else {
						$data['message'] = $this->lang->line('error_update_segment');
						$data['error'] = TRUE;
					}
				} else {
					$data['message'] = $this->lang->line('error_create_invoice');
					$data['error'] = TRUE;
				}
				if (!$data['error']) {
					$data['invoice_id'] = $invoice_id;
					$this->load->view('invoice/xhr_add_submit', $data);
				} else {
					$this->load->view('xhr_error', $data);
				}
			} else {
				$data['error'] = $this->lang->line('error_update_segment');
				$this->load->view('xhr_error', $data);
			}
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($invoice_id) {
		if ($this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->update) {
			$this->load->model('client_model');
			$data['invoice'] = $this->invoice_model->select_single($invoice_id);
			$data['client'] = $this->client_model->select_single($data['invoice']['client_id']);
			$this->load->view('invoice/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit_submit($invoice_id) {
		$invoice['name'] = htmlentities($this->input->post('name'));
		$invoice['duedate'] = $this->input->post('duedate');
		$invoice['client_id'] = (int) $this->input->post('client_id');
		$invoice['amount'] = (double) $this->input->post('amount');
		$invoice['paid'] = post_checkbox('paid');
		$invoice['remind'] = post_checkbox('remind');
		if ($invoice['paid']) {
			$invoice['paiddate'] = $this->input->post('paiddate');
		} else {
			$invoice['paiddate'] = '0000-00-00';
		}
		$invoice['itemize'] = post_checkbox('itemize');
		$invoice['sent'] = post_checkbox('sent');
		$invoice['content'] = htmlentities($this->input->post('content'));
		$invoice['company_id'] = $this->session->userdata('company_id');

		if ($invoice['name'] && $this->security_model->own_invoice($invoice_id) && $this->perm_user->invoice->update && $this->invoice_model->update($invoice_id, $invoice)) {
			$data['message'] = $this->lang->line('invoice_updated');
			$this->load->view('invoice/xhr_edit_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_update_invoice');
			$this->load->view('xhr_error', $data);
		}
	}

	function payment($invoice_id = 0) {
		if ($this->perm_user->payment->create) {
			$data['invoices'] = $this->invoice_model->select_payable($this->session->userdata('company_id'), 0,0, FALSE, 'duedate');
			$data['selected'] = $invoice_id;
			$this->load->view('invoice/xhr_payment', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	function payment_submit() {
		$this->load->model('payment_model');
		$data['date_received'] = $_POST['date'];
		$data['content'] = htmlentities($_POST['content']);
		$data['amount'] = (double) $_POST['amount'];
		$data['invoice_id'] = (int) $_POST['invoice_id'];
		$data['company_id'] = $this->session->userdata('company_id');
		if ($this->security_model->own_invoice($data['invoice_id']) && $this->perm_user->payment->create && $this->payment_model->insert($data)) {
			$data['message'] = $this->lang->line('payment_added');
			$this->load->view('invoice/xhr_payment_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_create_payment');
			$this->load->view('xhr_error', $data);
		}
	}

	function payment_delete($payment_id) {
		$this->load->model('payment_model');
		if ($this->security_model->own_payment($payment_id) && $this->perm_user->payment->delete && $this->payment_model->delete($payment_id)) {
			echo "1";
		} else {
			echo "0";
		}
	}

	function expense_unassign($expense_id) {
		$this->load->model('expense_model');
		if ($this->security_model->own_expense($expense_id) && $this->perm_user->expense->update && $this->expense_model->unassign_from_invoice($expense_id)) {
			echo "1";
		} else {
			echo "0";
		}
	}

	function segment_unassign($segment_id) {
		$this->load->model('segment_model');
		if ($this->security_model->own_segment($segment_id) && $this->perm_user->segment->update && $this->segment_model->unassign_from_invoice($segment_id)) {
			echo "1";
		} else {
			echo "0";
		}
	}

}
