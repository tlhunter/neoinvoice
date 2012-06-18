<?php
class Project_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'project';
    }

	/**
	 * @param int $project_id The ID of the project
	 * @return array Associative array of information regarding the project
	 * @abstract Selects a project by ID
	 */
	function select_single($project_id) {
		$project_data = $this->multicache->get("project:$project_id");
		if (!$project_data) {
			$query = $this->db->get_where($this->table_name, array('id' => $project_id), 1);
			$project_data = $query->row_array();
			$this->multicache->set("project:$project_id", $project_data);
		}
		return $project_data;
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 is first)
	 * @param int $limit How many records to return (0 is unlimited)
	 * @param bool $verbose True returns all information, False returns id, name, and active
	 * @param string $sort_col The column to sort results by, defaults to 'name'
	 * @return array Associative array of project information
	 * @abstract Select multiple projects belonging to a company
	 */
	function select_multiple($company_id, $start = 0, $limit = 0, $verbose = TRUE, $sort_col = 'name') {
		$start += 0;
		$limit += 0;
		switch($sort_col) {
			case 'name':
			case 'client':
			case 'created':
			case 'modified':
			case 'active':
				$sort_column = $sort_col;
				break;
			default:
				$sort_column = 'name';
		}
		if ($verbose) {
			$sql = "SELECT p.*, c.name AS client_name FROM project AS p, client AS c WHERE p.company_id = " . $this->db->escape($company_id) . " AND p.client_id = c.id ORDER BY p.$sort_column LIMIT $start, $limit";
		} else {
			$limit_str = '';
			if ($limit) {
				$limit_str = "LIMIT $start, $limit";
			}
			$sql = "SELECT id, name, active FROM project WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY $sort_column $limit_str";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $client_id The ID of the client
	 * @return array Associative array of project information for all projects belonging to the specified client
	 * @abstract Select multiple projects belonging to the specified client
	 */
	function select_multiple_client($client_id) {
		$sql = "SELECT * FROM project WHERE client_id = " . $this->db->escape($client_id) . " ORDER BY name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param array $project_ids Array of Project ID's
	 * @param id $company_id The ID of the company (defaults to company ID from session for security purposes)
	 * @return array Associative array of project information
	 * @abstract Select multiple projects by supplying a batch array of project ID's
	 * @todo the security precaution should be handled by the controller not the model
	 */
	function select_multiple_by_ids($project_ids, $company_id = 0) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		for ($i = 0; $i < count($project_ids); $i++) {
			$project_ids[$i] += 0;
		}
		$sql_ids = implode(', ', $project_ids);
		$sql = "SELECT id, name FROM project WHERE id IN ($sql_ids) AND company_id = " . $this->db->escape($company_id) . "";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int Count of all projects
	 * @abstract Counts the number of all projects belonging to the specified company
	 */
	function get_total($company_id) {
		$sql = "SELECT COUNT(*) AS count FROM project WHERE company_id = " . $this->db->escape($company_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $project_id The ID of the project
	 * @return bool True if project was deleted and False if there was an error
	 * @abstract Deletes a project (cascades to time segments as well)
	 */
	function delete($project_id) {
		$sql = "DELETE FROM project WHERE id = " . $this->db->escape($project_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			#$sql = "DELETE FROM segment WHERE project_id = " . $this->db->escape($project_id) . " LIMIT 1";
			#$this->db->simple_query($sql);
			$this->multicache->delete("project:$project_id");
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of project data, ignores .id, failes without .company_id
	 * @return mixed The ID of the project if project was created, False if there was an error
	 * @abstract Creates a new project and returns the ID
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id']) || !isset($data['client_id'])) {
			return FALSE;
		}
		$this->db->set('created', 'NOW()', FALSE);
		$this->db->set('modified', 'NOW()', FALSE);
		if ($this->db->insert('project', $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $project_id The ID of the project
	 * @param array $data Asociative array of project data
	 * @return bool True if project was updated, False if there was an error
	 * @abstract Updates an existing project with the new information
	 */
	function update($project_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $project_id);
		$this->db->set('modified', 'NOW()', FALSE);
		$this->multicache->delete("project:$project_id");
		return $this->db->update('project', $data);
	}

	/**
	 * @param int $project_id The ID of the project
	 * @return bol True if project was touched, False if there was an error
	 * @abstract Updates the modified timestamp of a project
	 */
	function touch($project_id) {
		$sql = "UPDATE project SET modified = NOW() WHERE id = " . $this->db->escape($project_id) . " LIMIT 1";
		$this->multicache->delete("project:$project_id");
		return $this->db->simple_query($sql);
	}

}