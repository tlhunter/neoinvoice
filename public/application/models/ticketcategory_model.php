<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs Ticket Category related database operations
 */
class Ticketcategory_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'ticket_category';
    }

	/**
	 *
	 * @param int $ticketcategory_id The ID of the Ticket Category
	 * @return array Associated array of Ticket Category data
	 * @abstract Gets information regarding the specified Ticket Category
	 */
	function select_single($ticketcategory_id) {
		$sql = "SELECT * FROM {$this->table_name} WHERE id = " . $this->db->escape($ticketcategory_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 = first)
	 * @param int $limit The number of records to return (0 = all)
	 * @return array Associative array of Ticket Category information
	 * @abstract Gets information about all Ticket Categories owned by the company
	 */
	function select_multiple($company_id, $start = 0, $limit = 0) {
		$start += 0;
		$limit += 0;
		$limit_str = '';
		if ($limit)
			$limit_str = "LIMIT $start, $limit";
		$sql = "SELECT {$this->table_name}.* FROM {$this->table_name} WHERE {$this->table_name}.company_id = " . $this->db->escape($company_id) . " ORDER BY name $limit_str";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @return int Count of all Ticket Categories owned by the company
	 * @abstract Counts all Ticket Categories owned by the company
	 */
	function get_total($company_id) {
		$count = $this->multicache->get("count_ticketcategory_by_company:$company_id");
		if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
			$this->multicache->set("count_ticketcategory_by_company:$company_id", $count);
		}
		return $count;
	}

	/**
	 *
	 * @param int $ticketcategory_id The ID of the Ticket Category
	 * @return bool Whether or not the Ticket Category was successfully deleted
	 * @abstract Deletes the specified Ticket Category, and all related Ticket Stages
	 * @todo Grab company_id from ticket instead of session
	 */
	function delete($ticketcategory_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($ticketcategory_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("count_ticketcategory_by_company:{$this->session->userdata('company_id')}");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param array $data Associative array of Ticket Category data, ignores .id, fails without .company_id
	 * @return mixed The ID of the newly created Ticket Category, or False if there was an error
	 * @abstract Creates a new Ticket Category with the supplied data
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		if ($this->db->insert($this->table_name, $data)) {
			$this->multicache->delete("count_ticketcategory_by_company:{$data['company_id']}");
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param int $ticketcategory_id The ID of the Ticket Category
	 * @param array $data Associative array of Ticket Category data, ignores .id
	 * @return bool Whether or not the update was successful
	 * @abstract Updates a Ticket Category with the specified data
	 */
	function update($ticketcategory_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $ticketcategory_id);
		if ($this->db->update($this->table_name, $data)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 *
	 * @param int $ticketcategory_id The ID of the Ticket Category
	 * @return bool Whether or not the Ticket Category was successfully touched
	 * @abstract Updates the Ticket Category's modified time to be now
	 */
	function touch($ticketcategory_id) {
		$sql = "UPDATE {$this->table_name} SET modified = NOW() WHERE id = " . $this->db->escape($ticketcategory_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}
	
}