<?php
class Ticket_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'ticket';
		$this->table_category_name = 'ticket_category';
		$this->table_stage_name = 'ticket_stage';
    }

	/**
	 * @param int $ticket_id The ID of the ticket
	 * @return array Associative array of information regarding the ticket
	 * @abstract Selects a ticket by ID
	 */
	function select_single($ticket_id) {
		$ticket_data = $this->multicache->get("ticket:$ticket_id");
		if (!$ticket_data) {
			$this->db->select("{$this->table_name}.*");
			$this->db->select("{$this->table_category_name}.name AS {$this->table_category_name}_name");
			$this->db->select("{$this->table_stage_name}.name AS {$this->table_stage_name}_name");
			$this->db->select("project.name AS project_name");
			$this->db->select("usergroup.name AS usergroup_name");
			$this->db->select("user.name AS user_name");

			$this->db->from($this->table_name);
			$this->db->join($this->table_category_name, "{$this->table_category_name}.id = {$this->table_name}.{$this->table_category_name}_id", 'left');
			$this->db->join($this->table_stage_name, "{$this->table_stage_name}.id = {$this->table_name}.{$this->table_stage_name}_id", 'left');
			$this->db->join('project', "project.id = {$this->table_name}.project_id", 'left');
			$this->db->join('usergroup', "usergroup.id = {$this->table_name}.assigned_usergroup_id", 'left');
			$this->db->join('user', "user.id = {$this->table_name}.assigned_user_id", 'left');
			
			$this->db->where(array($this->table_name . '.id' => $ticket_id), 1);
			
			$query = $this->db->get();
			$ticket_data = $query->row_array();
			$this->multicache->set("ticket:$ticket_id", $ticket_data);
		}
		return $ticket_data;
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to return (0 is first)
	 * @param int $limit How many records to return (0 is unlimited)
	 * @param bool $verbose True returns all information, False returns id, name, and active
	 * @param string $sort_col The column to sort results by, defaults to 'name'
	 * @return array Associative array of ticket information
	 * @abstract Select multiple tickets belonging to a company
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
		$limit_str = '';
		if ($limit) {
			$limit_str = "LIMIT $start, $limit";
		}
		if ($verbose) {
			$sql = "SELECT
  ticket.*,
  ticket_category.name AS ticket_category_name,
  ticket_stage.name AS ticket_stage_name,
  user.name AS user_name,
  usergroup.name AS usergroup_name,
  project.name AS project_name
FROM
  ticket
LEFT JOIN
  user ON ticket.assigned_user_id = user.id
LEFT JOIN
  usergroup ON ticket.assigned_usergroup_id = usergroup.id
LEFT JOIN
  ticket_stage ON ticket.ticket_stage_id = ticket_stage.id
LEFT JOIN
  ticket_category ON ticket.ticket_category_id = ticket_category.id
LEFT JOIN
  project ON ticket.project_id = project.id
WHERE
  ticket.company_id = " . $this->db->escape($company_id) . "
ORDER BY
  ticket.$sort_column $limit_str";
		} else {
			$sql = "SELECT ticket.id, ticket.name, ticket.assigned_user_id, ticket.assigned_usergroup_id, ticket.due, DATEDIFF(ticket.due, CURDATE()) AS countdown, ticket_stage.closed FROM ticket LEFT JOIN ticket_stage ON ticket.ticket_stage_id = ticket_stage.id WHERE ticket.company_id = " . $this->db->escape($company_id) . " AND (ISNULL(ticket_stage.closed) OR ticket_stage.closed != 1) ORDER BY $sort_column $limit_str";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}
    
    function select_by_project($project_id) {
        $sql = "SELECT ticket.id, ticket.name FROM ticket LEFT JOIN ticket_stage ON ticket.ticket_stage_id = ticket_stage.id WHERE ticket.project_id = " . $this->db->escape($project_id) . " AND (ISNULL(ticket_stage.closed) OR ticket_stage.closed != 1) ORDER BY name";
        $query = $this->db->query($sql);
		return $query->result_array();
    }

	/**
	 *
	 * @param <type> $company_id The ID of the company
	 * @param <type> $user_id The ID of the user
	 * @param <type> $usergroup_id The ID of the user's usergroup
	 * @return <type> array Associative Array of tickets
	 * @abstract Returns a list of tickets that either belong directly to the user or the group the user belongs to
	 */
	function select_relevant($company_id, $user_id, $usergroup_id) {
		$sql = "SELECT id, name FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . " AND (assigned_usergroup_id = " . $this->db->escape($usergroup_id) . " OR assigned_user_id = " . $this->db->escape($user_id) . ")";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int Count of all tickets
	 * @abstract Counts the number of all tickets belonging to the specified company
	 */
	function get_total($company_id) {
		$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $ticket_id The ID of the ticket
	 * @return bool Whether or not the ticket was successfully deleted
	 * @abstract Deletes a ticket
	 */
	function delete($ticket_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($ticket_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("ticket:$ticket_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of ticket information, ignores .id, fails without .company_id or .name
	 * @return mixed The ID of the inserted record, or a False if there was an error
	 * @abstract Creates a new ticket
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id']) || empty($data['name']) || empty($data['project_id'])) {
			return FALSE;
		}
		$this->db->set('created', 'NOW()', FALSE);
		$this->db->set('modified', 'NOW()', FALSE);
		if ($this->db->insert($this->table_name, $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $ticket_id The ID of the ticket
	 * @param array $data Associative array of information for the ticket, ignores the .id
	 * @return bool Whether or not the ticket was updated properly
	 * @abstract Updates a ticket
	 */
	function update($ticket_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->multicache->delete("ticket:$ticket_id");
		$this->db->set('modified', 'NOW()', FALSE);
		$this->db->where('id', $ticket_id);
		return $this->db->update($this->table_name, $data);
	}
}