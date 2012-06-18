<?php
class Client_model extends Model {

    function __construct() {
        parent::Model();
    }

	/**
	 * @param int $client_id The ID of the client to be selected
	 * @return array Associative array of client information
	 */
	function select_single($client_id) {
		$client_data = $this->multicache->get("client:$client_id");
		if (!$client_data) {
			$sql = "SELECT * FROM client WHERE id = " . $this->db->escape($client_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			$client_data = $query->row_array();

			$sql = "SELECT COUNT(id) AS segment_count, EXTRACT(HOUR_MINUTE FROM SEC_TO_TIME( SUM( TIME_TO_SEC((duration))))) AS total_time FROM `segment` WHERE project_id IN (SELECT id FROM project WHERE client_id = " . $this->db->escape($client_id) . ")";
			$query = $this->db->query($sql);
			$segment_data = $query->row_array();

			if ($segment_data['segment_count']) {
				$segment_data['total_time'] = substr($segment_data['total_time'], 0, strlen($segment_data['total_time'])-2) . ':' . substr($segment_data['total_time'], strlen($segment_data['total_time'])-2);
			} else {
				$segment_data['total_time'] = '0:00';
			}
			$client_data = array_merge($client_data, $segment_data);
			$this->multicache->set("client:$client_id", $client_data, 3600);
		}

		return $client_data;
	}

	/**
	 * @param int $company_id The ID of the company whose clients we are selecting
	 * @param int $start The first record to be displayed (0 = normal)
	 * @param int $limit The amount of items to be returned (0 = no limit)
	 * @param bool $verbose If we want to return all data or just name/id/active
	 * @param string $sort_col The database column we want to order our results by
	 * @return aray Associated array of information on all relevant clients
	 */
	function select_multiple($company_id, $start = 0, $limit = 0, $verbose = TRUE, $sort_col = 'name') {
		$start += 0;
		$limit += 0;
		switch($sort_col) {
			case 'name':
			case 'email':
			case 'created':
			case 'modified':
			case 'active':
				$sort_column = $sort_col;
				break;
			default:
				$sort_column = 'name';
		}
		$limit_str = '';
		if ($limit) {
			$limit_str = "LIMIT $start, $limit";
		}
		if ($verbose) {
			$sql = "SELECT * FROM client WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY $sort_column $limit_str";
			$query = $this->db->query($sql);
			$results = $query->result_array();
		} else {
			$results = $this->multicache->get("list_client_by_company:$company_id");
			if (!$results) {
				$sql = "SELECT id, name, active FROM client WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY name $limit_str";
				$query = $this->db->query($sql);
				$results = $query->result_array();
				$this->multicache->set("list_client_by_company:$company_id", $results);
			}
		}
		
		return $results;
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return array Associated array containing id and name of clients belonging to $company_id who are active
	 */
	function select_active($company_id) {
		$sql = "SELECT id, name FROM client WHERE company_id = " . $this->db->escape($company_id) . " AND active = 1 ORDER BY name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int The number of clients belonging to this company
	 */
	function get_total($company_id) {
		$count = $this->multicache->get("count_client_by_company:$company_id");
		if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM client WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
			$this->multicache->set("count_client_by_company:$company_id", $count);
		}
		return $count;
	}

	/**
	 * @param int $client_id The ID of the client to be deleted
	 * @return bool True or False for Success or Failure of delete
	 */
	function delete($client_id) {
		$sql = "DELETE FROM client WHERE id = " . $this->db->escape($client_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("client:$client_id");
			/**
			 * @todo Run a select to get the company id instead of using whats in the session.
			 */
			$company_id = $this->session->userdata("company_id");
			$this->multicache->delete("count_client_by_company:$company_id");
			$this->multicache->delete("list_client_by_company:$company_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Asociated array of client data, ignores .id, fails without .company_id
	 * @return bool True or False for Success or Failure of insert
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		$this->db->set('created', 'NOW()', FALSE);
		$this->db->set('modified', 'NOW()', FALSE);
		if ($this->db->insert('client', $data)) {
			$this->multicache->delete("list_client_by_company:{$data['company_id']}");
			/**
			 * @todo turn this into an increment instead of a delete
			 */
			$this->multicache->delete("count_client_by_company:{$data['company_id']}");
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $client_id The ID of the client to be updated
	 * @param array $data Associated array of client details, ignores .id
	 * @return int True or False for Success or Failure of update
	 */
	function update($client_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->set('modified', 'NOW()', FALSE);
		$this->db->where('id', $client_id);
		$this->multicache->delete("client:$client_id");
		/**
		 * @todo Run a select to get the company id instead of using whats in the session.
		 */
		$company_id = $this->session->userdata("company_id");
		$this->multicache->delete("list_client_by_company:$company_id");
		return $this->db->update('client', $data);
		#return $this->db->affected_rows(); #Returns a 0 if we update and change nothing
	}

	/**
	 * @param int $client_id The ID of the client to have modified timestamp set to now
	 * @return bool Returns a True or False for Success or Failure of update
	 */
	function touch($client_id) {
		$sql = "UPDATE client SET modified = NOW() WHERE id = " . $this->db->escape($client_id) . " LIMIT 1";
		$this->multicache->delete("client:$client_id");
		return $this->db->simple_query($sql);
	}
}