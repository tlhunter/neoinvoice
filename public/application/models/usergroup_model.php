<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs User Group related database operations
 */
class Usergroup_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'usergroup';
    }

	/**
	 * @param int $usergroup_id The ID of the usergroup
	 * @return array Associated array of usergroup data
	 * @abstract Gets information regarding the specified usergroup
	 */
	function select_single($usergroup_id) {
		$sql = "SELECT * FROM {$this->table_name} WHERE id = " . $this->db->escape($usergroup_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 = first)
	 * @param int $limit The number of records to return (0 = all)
	 * @return array Associative array of usergroup information, including segment_count and hour_float
	 * @abstract Gets information about all usergroups owned by the company
	 */
	function select_multiple($company_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = '';
		if ($limit)
			$limit_str = "LIMIT $start, $limit";
		$sql = "SELECT * FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY name $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int Count of all usergroups owned by the company
	 * @abstract Counts all usergroups owned by the company
	 */
	function get_total($company_id) {
		$count = $this->multicache->get("count_usergroup_by_company:$company_id");
		if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
			$this->multicache->set("count_usergroup_by_company:$company_id", $count);
		}
		return $count;
	}

	/**
	 * @param int $usergroup_id The ID of the usergroup
	 * @return bool Whether or not the usergroup was successfully deleted
	 * @abstract Deletes the specified usergroup, and all related segments
	 * @todo delete cache count_usergroup_by_company
	 */
	function delete($usergroup_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($usergroup_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of usergroup data, ignores .id, fails without .company_id
	 * @return mixed The ID of the newly created usergroup, or False if there was an error
	 * @abstract Creates a new usergroup with the supplied data
	 * @todo delete cache count_usergroup_by_company
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
	 * @param int $usergroup_id The ID of the usergroup
	 * @param array $data Associative array of usergroup data, ignores .id
	 * @return bool Whether or not the update was successful
	 * @abstract Updates a usergroup with the specified data
	 */
	function update($usergroup_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $usergroup_id);
		if ($this->db->update($this->table_name, $data)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $usergroup_id The ID of the usergroup
	 * @return bool Whether or not the usergroup was successfully touched
	 * @abstract Updates the usergroup's modified time to be now
	 */
	function touch($usergroup_id) {
		$sql = "UPDATE {$this->table_name} SET modified = NOW() WHERE id = " . $this->db->escape($usergroup_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}
	
}