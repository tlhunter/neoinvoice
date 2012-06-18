<?php
class Expense_model extends Model {
    function __construct() {
        parent::Model();
    }

	/**
	 * @param $expense_id int The ID of the expense item being selected
	 * @return mixed Associative array of information for the expense and expense type
	 * @abstract Get a single expense
	 */
	function select_single($expense_id) {
		$sql = "SELECT e.*, et.name AS expensetype_name, et.taxable, p.name AS project_name FROM expense AS e, expensetype AS et, project AS p WHERE e.id = " . $this->db->escape($expense_id) . " AND et.id = e.expensetype_id AND p.id = e.project_id LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 * @param int $invoice_id The ID of the project whose expenses we are requesting
	 * @param int $start The starting record offset from the beginning (0 = beginning)
	 * @param int $limit The total number of records to be returned (0 = no limit)
	 * @return mixed Associative array of expenses from the specified range and project
	 * @abstract Select multiple expenses based on Invoice
	 */
	function select_multiple($invoice_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = "";
		if ($limit) {
			 $limit_str = "LIMIT $start, $limit";
		}
		$sql = "SELECT e.*, et.name AS expensetype_name, et.taxable, p.name AS project_name FROM expense AS e, expensetype AS et, project AS p WHERE e.invoice_id = " . $this->db->escape($invoice_id) . " AND et.id = e.expensetype_id AND p.id = e.project_id ORDER BY date $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $project_id The ID of the project whose expenses we are requesting
	 * @param int $start The starting record offset from the beginning (0 = beginning)
	 * @param int $limit The total number of records to be returned (0 = no limit)
	 * @return mixed Associative array of expenses from the specified range and project
	 * @abstract Get multiple expenses based on the project
	 */
	function select_multiple_project($project_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = "";
		if ($limit) {
			 $limit_str = "LIMIT $start, $limit";
		}
		$sql = "SELECT e.*, et.name AS expensetype_name, et.taxable, p.name AS project_name FROM expense AS e, expensetype AS et, project AS p WHERE e.project_id = " . $this->db->escape($project_id) . " AND et.id = e.expensetype_id AND p.id = e.project_id ORDER BY date $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $project_id The ID of the project which we need to return expenses
	 * @return mixed Associative array of expenses from the specified project which are not a part of an invoice
	 * @abstract Select expenses from the supplied project which are not assigned to an Invoice
	 */
	function select_available_project($project_id) {
		$project_id += 0;
		$sql = "SELECT e.*, et.name AS expensetype_name, et.taxable, p.name AS project_name FROM expense AS e, expensetype AS et, project AS p WHERE e.project_id = " . $this->db->escape($project_id) . " AND et.id = e.expensetype_id AND p.id = e.project_id AND ISNULL(e.invoice_id) ORDER BY date";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $project_id The ID of the project
	 * @return int The number of expenses belonging to this project
	 * @abstract Count the number of expenses belonging to the Project
	 */
	function get_total_project($project_id) {
		$sql = "SELECT COUNT(*) AS count FROM expense WHERE project_id = " . $this->db->escape($project_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $expense_id The ID of the expense we are about to delete
	 * @return bool True if item was deleted, False if otherwise
	 * @abstract Deletes the expense
	 */
	function delete($expense_id) {
		$sql = "DELETE FROM expense WHERE id = " . $this->db->escape($expense_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param int $expense_id The ID of the expense we are about to unassign
	 * @return bool True if item was unassigned, False if otherwise
	 * @abstract Unassignes the Expense from the Invoice (without deleting it)
	 */
	function unassign_from_invoice($expense_id) {
		$sql = "UPDATE expense SET invoice_id = NULL WHERE id = " . $this->db->escape($expense_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param array $expense_ids An array of expense ID's
	 * @return bool Success or Failure
	 * @abstract Deletes multiple Expenses
	 */
	function delete_multiple($expense_ids) {
		for ($i = 0; $i < count($expense_ids); $i++) {
			$expense_ids[$i] += 0;
		}
		$sql = "DELETE FROM expense WHERE id IN (" . implode($expense_ids, ', ') . ")";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param mixed $data Associative array of expense data. ['id'] must not be set, ['company_id'] must be set.
	 * @return mixed False for failure, int for inserted ID
	 * @abstract Creates a new expense
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		if ($this->db->insert('expense', $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $expense_id ID of expense to be updated
	 * @param mixed $data Associative array of expense data
	 * @return bool Success or failure of operation
	 * @abstract Updates the expense with new data
	 */
	function update($expense_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $expense_id);
		return $this->db->update('expense', $data);
	}

	/**
	 * @param array $expense_ids Array of expense ID's which will have an updated Invoice
	 * @param int $invoice_id The new invoice ID to be applied to the expenses
	 * @return int Affected rows
	 * @abstract Updates multiple expenses to use the same invoice
	 */

	function multiple_set_invoice($expense_ids, $invoice_id) {
		if (empty($expense_ids)) {
			return TRUE;
		}
		$invoice_id += 0;
		for ($i = 0; $i < count($expense_ids); $i++) {
			$expense_ids[$i] += 0;
		}
		$sql = "UPDATE expense SET invoice_id = $invoice_id WHERE id IN (" . implode($expense_ids, ', ') . ")";
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	/**
	 * @return int Count of all expenses in the database
	 * @abstract Used for application level statistics
	 */
	function count_all() {
		return $this->db->count_all('expense');
	}

}