<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs Worktype related database operations
 */
class Worktype_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'worktype';
    }

	/**
	 *
	 * @param int $worktype_id The ID of the worktype
	 * @return array Associated array of worktype data
	 * @abstract Gets information regarding the specified worktype
	 */
	function select_single($worktype_id) {
		$sql = "SELECT * FROM {$this->table_name} WHERE id = " . $this->db->escape($worktype_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 = first)
	 * @param int $limit The number of records to return (0 = all)
	 * @return array Associative array of worktype information, including segment_count and hour_float
	 * @abstract Gets information about all worktypes owned by the company
	 */
	function select_multiple($company_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = '';
		if ($limit)
			$limit_str = "LIMIT $start, $limit";
		$sql = "SELECT {$this->table_name}.*, COUNT(segment.id) AS segment_count, FORMAT(IFNULL(SUM(EXTRACT(HOUR FROM segment.duration) + (EXTRACT(MINUTE FROM segment.duration)/60)), '0.00'), 2) AS hour_float FROM {$this->table_name} LEFT JOIN segment ON {$this->table_name}.id = segment.worktype_id WHERE {$this->table_name}.company_id = " . $this->db->escape($company_id) . " GROUP BY {$this->table_name}.id ORDER BY name $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @return int Count of all worktypes owned by the company
	 * @abstract Counts all worktypes owned by the company
	 */
	function get_total($company_id) {
		$count = $this->multicache->get("count_worktype_by_company:$company_id");
		if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
			$this->multicache->set("count_worktype_by_company:$company_id", $count);
		}
		return $count;
	}

	/**
	 *
	 * @param int $worktype_id The ID of the worktype
	 * @return bool Whether or not the worktype was successfully deleted
	 * @abstract Deletes the specified worktype, and all related segments
	 */
	function delete($worktype_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($worktype_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param array $data Associative array of worktype data, ignores .id, fails without .company_id
	 * @return mixed The ID of the newly created worktype, or False if there was an error
	 * @abstract Creates a new worktype with the supplied data
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
	 * @param int $worktype_id The ID of the worktype
	 * @param array $data Associative array of worktype data, ignores .id
	 * @return bool Whether or not the update was successful
	 * @abstract Updates a worktype with the specified data
	 */
	function update($worktype_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $worktype_id);
		if ($this->db->update($this->table_name, $data)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param int $worktype_id The ID of the worktype
	 * @return bool Whether or not the worktype was successfully touched
	 * @abstract Updates the worktype's modified time to be now
	 */
	function touch($worktype_id) {
		$sql = "UPDATE {$this->table_name} SET modified = NOW() WHERE id = " . $this->db->escape($worktype_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}
	
}