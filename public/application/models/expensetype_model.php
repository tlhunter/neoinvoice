<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs Expense Type related database operations
 * @todo text. pasted and replaced and did some ititial work from worktype
 */
class Expensetype_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'expensetype';
    }

	/**
	 *
	 * @param int $expensetype_id The ID of the Expense Type
	 * @return array Associated array of Expense Type data
	 * @abstract Gets information regarding the specified Expense Type
	 */
	function select_single($expensetype_id) {
		$sql = "SELECT * FROM {$this->table_name} WHERE id = " . $this->db->escape($expensetype_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 = first)
	 * @param int $limit The number of records to return (0 = all)
	 * @return array Associative array of Expense Type information, including expense_count
	 * @abstract Gets information about all Expense Type owned by the company
	 */
	function select_multiple($company_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = '';
		if ($limit)
			$limit_str = "LIMIT $start, $limit";
		$sql = "SELECT {$this->table_name}.*, COUNT(expense.id) AS expense_count, SUM(expense.amount) AS amount_sum FROM {$this->table_name} LEFT JOIN expense ON {$this->table_name}.id = expense.expensetype_id WHERE {$this->table_name}.company_id = " . $this->db->escape($company_id) . " GROUP BY {$this->table_name}.id ORDER BY name $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @return int Count of all Expense Types owned by the company
	 * @abstract Counts all Expense Types owned by the company
	 */
	function get_total($company_id) {
		$count = $this->multicache->get("count_expensetype_by_company:$company_id");
		if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
			$this->multicache->set("count_expensetype_by_company:$company_id", $count);
		}
		return $count;
	}

	/**
	 *
	 * @param int $expensetype_id The ID of the Expense Type
	 * @return bool Whether or not the Expense Type was successfully deleted
	 * @abstract Deletes the specified Expense Type, and all related expenses
	 */
	function delete($expensetype_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($expensetype_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param array $data Associative array of Expense Type data, ignores .id, fails without .company_id
	 * @return mixed The ID of the newly created Expense Type, or False if there was an error
	 * @abstract Creates a new Expense Type with the supplied data
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		if ($this->db->insert($this->table_name, $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param int $expensetype_id The ID of the Expense Type
	 * @param array $data Associative array of Expense Type data, ignores .id
	 * @return bool Whether or not the update was successful
	 * @abstract Updates a Expense Type with the specified data
	 */
	function update($expensetype_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $expensetype_id);
		if ($this->db->update($this->table_name, $data)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param int $expensetype_id The ID of the Expense Type
	 * @return bool Whether or not the Expense Type was successfully touched
	 * @abstract Updates the Expense Type's modified time to be now
	 */
	function touch($expensetype_id) {
		$sql = "UPDATE {$this->table_name} SET modified = NOW() WHERE id = " . $this->db->escape($expensetype_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}
	
}