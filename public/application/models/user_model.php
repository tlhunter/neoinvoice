<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs User related database operations
 */
class User_model extends Model {
    function __construct() {
        parent::Model();
    }

	/**
	 * @param int $user_id The ID of the user
	 * @return array Associative array of user info: id, active, username, name, email, created, modified
	 * @abstract Gets information regarding the specified user
	 */
	function select_single($user_id) {
		$user_data = $this->multicache->get("user:$user_id");
		if (!$user_data) {
			$sql = "SELECT id, active, username, name, email, usergroup_id, created, modified FROM user WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			$user_data = $query->row_array();
			$this->multicache->set("user:$user_id", $user_data);
		}
		return $user_data;
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to be returned (0 = first)
	 * @param int $limit The number of records to be returned (0 = all)
	 * @param bool $verbose Whether to return extensive data or minimal data
	 * @param string $sort_col Which database column to order results by
	 * @return array Associative array of user info: id, company_id, active, username, name, email, created, modified
	 * @abstract Gets information on all users belonging to the specified company
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
			$sql = "SELECT id, company_id, active, username, name, email, created, modified, usergroup_id FROM user WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY $sort_column $limit_str";
		} else {
			$sql = "SELECT user.id, user.name, user.active, user.usergroup_id, usergroup.name AS usergroup_name FROM user LEFT JOIN usergroup ON usergroup.id = user.usergroup_id WHERE user.company_id = " . $this->db->escape($company_id) . " ORDER BY $sort_column $limit_str";
		}
		
		$query = $this->db->query($sql);
		$results = $query->result_array();

		return $results;
	}

	/**
	 * @param int $user_id The ID of the user
	 * @return mixed Associative array of user preferences, or False on error
	 * @abstract Gets an array of user preferences
	 */
	function load_preferences($user_id) {
		$user_prefs_json = $this->multicache->get("user_prefs:$user_id");
		if (!$user_prefs_json) {
			$sql = "SELECT preferences FROM user WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$user_prefs_json = $row->preferences;
			} else {
				$user_prefs_json = FALSE;
			}
			$this->multicache->set("user_prefs:$user_id", $user_prefs_json);
		}
		$user_prefs = json_decode($user_prefs_json, TRUE);
		return $user_prefs;
	}

	/**
	 * @param int $user_id The ID of the user
	 * @return array Object containing user permissions, merged with a template in case new permissions were added
	 * @abstract Gets an object containing the users permissions
	 */
	function load_permissions($user_id) {
		$user_perms_json = $this->multicache->get("user_perms:$user_id");
		if (!$user_perms_json) {
			$sql = "SELECT permissions FROM user WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$user_perms_json = $row->permissions;
			} else {
				return FALSE;
			}
			$this->multicache->set("user_perms:$user_id", $user_perms_json);
		}
		$user_perms = json_decode($user_perms_json);
		$default_perms = arrayToObject($this->perm_template('empty'));
		return (object) array_merge((array) $default_perms, (array) $user_perms);
	}

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @return int Count of users
	 * @abstract Counts all users belonging to the company
	 */
	function get_total($company_id) {
		#$count = $this->multicache->get("count_user_by_company:$company_id");
		#if (!$count) {
			$sql = "SELECT COUNT(*) AS count FROM user WHERE company_id = " . $this->db->escape($company_id) . "";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$count = $data['count'];
		#	$this->multicache->set("count_user_by_company:$company_id", $count);
		#}
		return $count;
	}

	/**
	 * @param int $user_id The ID of the user
	 * @return bool Whether or not the user was successfully deleted
	 * @abstract Deletes the specified user
	 */
	function delete($user_id) {
		$sql = "DELETE FROM user WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("list_user_by_company:" . $this->session->userdata('company_id'));
			$this->multicache->delete("user:$user_id");
			$this->multicache->delete("user_prefs:$user_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $user_id The ID of the user
	 * @param array $data Associative array of user data
	 * @return bool Success or Failure of update
	 * @abstract Updates the specified user with the new data
	 * @todo Remove code from rm_user library
	 */
	function update($user_id, $data) {
		$this->multicache->delete("list_user_by_company:" . $this->session->userdata('company_id'));
		$this->multicache->delete("user:$user_id");
		$this->multicache->delete("user_prefs:$user_id");
		return $this->rm_user->modify($user_id, $data);
	}

	/**
	 * @param int $user_id The ID of the user
	 * @param array $data Associated array of user preferences
	 * @return bool True or False depending on Success or Failure of the update
	 * @abstract Updates the specified users preferences
	 */
	function update_preferences($user_id, $data) {
		$prefs_json = json_encode($data);
		$sql = "UPDATE user SET preferences = " . $this->db->escape($prefs_json) . " WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->set("user_prefs:$user_id", $prefs_json);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $user_id The ID of the user
	 * @param array $data Associated array of user permissions (object?)
	 * @return bool True or False depending on the Success or Failure of the update
	 * @abstract Updates the specified users permissions
	 */
	function update_permissions($user_id, $data) {
		$perms_json = json_encode($data);
		$sql = "UPDATE user SET permissions = " . $this->db->escape($perms_json) . " WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->set("user_perms:$user_id", $perms_json);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param sting $level User template level, 'admin', 'standard', 'empty'
	 * @return array Associated array of user permissions
	 * @abstract Gets an array of user permissions, to be used for creating new users or merging with user permissions in case updates have occured
	 */
	function perm_template($level = 'standard') {
		if ($level == 'admin') {
			return array(
				'company' => array('update' => TRUE, 'delete' => TRUE, 'upgrade' => TRUE),
				'client' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'expense' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'invoice' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'send' => TRUE),
				'payment' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'project' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'reports' => array('access' => TRUE),
				'user' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'setperms' => TRUE),
				'segment' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'editother' => TRUE),
				'worktype' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'usergroup' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'expensetype' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'tickettype' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE),
				'ticket' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'editother' => TRUE)
			);
		} else if ($level == 'standard') {
			return array(
				'company' => array('update' => FALSE, 'delete' => FALSE, 'upgrade' => FALSE),
				'client' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'expense' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'invoice' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'send' => FALSE),
				'payment' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'project' => array('create' => TRUE, 'update' => TRUE, 'delete' => FALSE),
				'reports' => array('access' => FALSE),
				'user' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'setperms' => FALSE),
				'segment' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'editother' => FALSE),
				'worktype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'usergroup' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'expensetype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'tickettype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'ticket' => array('create' => TRUE, 'update' => TRUE, 'delete' => TRUE, 'editother' => FALSE)
			);
		} else {
			return array(
				'company' => array('update' => FALSE, 'delete' => FALSE, 'upgrade' => FALSE),
				'client' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'expense' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'invoice' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'send' => FALSE),
				'payment' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'project' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'reports' => array('access' => FALSE),
				'user' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'setperms' => FALSE),
				'segment' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'editother' => FALSE),
				'worktype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'usergroup' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'expensetype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'tickettype' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE),
				'ticket' => array('create' => FALSE, 'update' => FALSE, 'delete' => FALSE, 'editother' => FALSE)
			);
		}
	}

	/**
	 * @return array Associative array of default permissions
	 */
	function pref_template() {
		return array(
			'language' => 'english',
			'per_page' => 10
		);
	}

}