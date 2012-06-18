<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * USER AUTHENTICATION DATABASE CLASS CodeIgniter Version by RENOWNED MEDIA
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @author Thomas Hunter of Renowned Media
 * @version 1.0.2
 * @link http://www.renownedmedia.com
 * @todo ANYWHERE YOU SEE mysql_num_rows() OR mysql_fetch_assoc(), this is an error!!!
 * RM_USER_TABLE should have at least five columns, an 'id' column (PK, integer, AI, unsigned),
 *		a 'username' column (string, unique), a password column (string, index),
 *		a 'created' column (timestamp default 0's), a 'modified' column (timestamp default 0's)
 */
define('RM_USER_TABLE', 'user');
define('RM_USERNAME_COLUMN', 'username');
define('RM_PASSWORD_COLUMN', 'password');
define('RM_EMAIL_COLUMN', 'email');
define('RM_LOST_PASSWORD_COLUMN', 'lost_password'); # keep blank if you don't want this feature
define('RM_CREATED_COLUMN', 'created');
define('RM_MODIFIED_COLUMN', 'modified');
define('RM_ID_COLUMN', 'id');
define("RM_PASSWORD_SALT", 'SET-PASSWORD-SALT-HERE');

class Rm_user {
	public $error_message = "";
	
	public function create($data) {
		$CI =& get_instance();
		$CI->load->database();
		if (!isset($data[RM_USERNAME_COLUMN]) || !isset($data[RM_PASSWORD_COLUMN])) {
			return false;
		}
		$sql = "INSERT INTO " . RM_USER_TABLE . " SET ";
		foreach($data AS $column => $value) {
			if ($column == RM_PASSWORD_COLUMN) {
				$value = $this->password_encrypt($value);
			}
			$sql .= "$column = " . $CI->db->escape($value) . ", ";
		}
		$sql .= RM_CREATED_COLUMN . " = NOW(), " . RM_MODIFIED_COLUMN . " = NOW()";
		if ($this->runQuery($sql)) {
			return mysql_insert_id(); /** @todo replace with CI function */
		} else {
			return false;
		}
	}

	public function can_create($data) {
		/**
		 * @todo do a test create, check unique keys like username, email, etc.
		 */
		$CI =& get_instance();
		$CI->load->database();
		$sql = "SELECT COUNT(id) AS count FROM " . RM_USER_TABLE . " WHERE " . RM_USERNAME_COLUMN . " = " . $CI->db->escape($data[RM_USERNAME_COLUMN]) . " LIMIT 1";
		$result = $this->runQuery($sql);
		$row = $result->row_array();
		if ($row['count']) {
			$this->error_message = "Username Already Exists";
			return FALSE;
		}
		$sql = "SELECT COUNT(id) AS count FROM " . RM_USER_TABLE . " WHERE " . RM_EMAIL_COLUMN . " = " . $CI->db->escape($data[RM_EMAIL_COLUMN]) . " LIMIT 1";
		$result = $this->runQuery($sql);
		$row = $result->row_array();
		if ($row['count']) {
			$this->error_message = "Email Already Exists";
			return FALSE;
		}
		return TRUE;
	}
	
	public function modify($user_id, $data) {
		$CI =& get_instance();
		$CI->load->database();
		$sql = "UPDATE " . RM_USER_TABLE . " SET ";
		foreach($data AS $column => $value) {
			if ($column == RM_PASSWORD_COLUMN) {
				$value = $this->password_encrypt($value);
			}
			$sql .= "$column = " . $CI->db->escape($value) . ", ";
		}
		$sql .= RM_MODIFIED_COLUMN . " = NOW()";
		$sql .= " WHERE " . RM_ID_COLUMN . " = $user_id LIMIT 1";
		return $this->runQuery($sql);
	}
	
	public function delete($user_id) {
		$user_id += 0;
		$sql = "DELETE FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = $user_id LIMIT 1";
		return $this->runQuery($sql);
	}
	
	public function touch($user_id) {
		$user_id += 0;
		$sql = "UPDATE " . RM_USER_TABLE . " SET " . RM_MODIFIED_COLUMN . " = NOW() WHERE " . RM_ID_COLUMN . " = $user_id LIMIT 1";
		return $this->runQuery($sql);
	}
	
	public function get_modified($user_id) {
		$user_id += 0;
		$sql = "SELECT " . RM_MODIFIED_COLUMN . " FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = $user_id LIMIT 1";
		$result = $this->runQuery($sql);
		if ($result->num_rows()) {
			$row = mysql_fetch_assoc($result);
			return strtotime($row[RM_MODIFIED_COLUMN]);
		} else {
			return false;
		}
	}
	
	public function get_created($user_id) {
		$user_id += 0;
		$sql = "SELECT " . RM_CREATED_COLUMN . " FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = $user_id LIMIT 1";
		$result = $this->runQuery($sql);
		if ($result->num_rows()) {
			$row = mysql_fetch_assoc($result);
			return strtotime($row[RM_CREATED_COLUMN]);
		} else {
			return false;
		}
	}
	
	public function auth($username, $password) {
		$CI =& get_instance();
		$CI->load->database();
		$pass = $this->password_encrypt($password);
		$sql = "SELECT " . RM_ID_COLUMN . " FROM " . RM_USER_TABLE . " WHERE " . RM_USERNAME_COLUMN . " = " . $CI->db->escape($username) . " AND ( " . RM_PASSWORD_COLUMN . " = '$pass' ";
		$test = RM_LOST_PASSWORD_COLUMN;
		if (!empty($test) && !empty($password)) {
			$sql .= "OR " . RM_LOST_PASSWORD_COLUMN . " = " . $CI->db->escape($password) . "";
		}
		$sql .= ") LIMIT 1";
		$query = $this->runQuery($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$sql = "UPDATE " . RM_USER_TABLE . " SET " . RM_LOST_PASSWORD_COLUMN . " = '', " . RM_MODIFIED_COLUMN . " = NOW() WHERE " . RM_USERNAME_COLUMN . " = '$username' LIMIT 1";
			$this->runQuery($sql);
			return ($row[RM_ID_COLUMN]);
		} else {
			return false;
		}
	}
	
	public function set($user_id, $field, $value = null) {
		$CI =& get_instance();
		$CI->load->database();
		$user_id += 0;
		if (is_array($field)) {
			return $this->modify($user_id, $field);
		} else {
			$sql = "UPDATE " . RM_USER_TABLE . " SET $field = " . $CI->db->escape($value) . ", " . RM_MODIFIED_COLUMN . " = NOW() WHERE " . RM_ID_COLUMN . " = '$user_id' LIMIT 1";
			return $this->runQuery($sql);
		}
	}
		
	public function get($user_id, $field) {
		$user_id += 0;
		if (is_array($field)) {
			$sql = "SELECT ";
			foreach($field AS $column) {
				$sql .= "$column, ";
			}
			$sql = rtrim($sql,", ");
			$sql .= " FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = '$user_id' LIMIT 1";
			$query = $this->runQuery($sql);
			if ($query->num_rows() > 0) {
				$row = $query->row_array();
				return $row;
			} else {
				return false;
			}
		} else {
			$sql = "SELECT $field FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = '$user_id' LIMIT 1";
			$query = $this->runQuery($sql);
			if ($query->num_rows()) {
				$row = $query->row_array();
				return $row[$field];
			} else {
				return false;
			}
		}
	}
	
	public function change_password($user_id, $new_password, $old_password = false) {
		$user_id += 0;
		$pass = $this->password_encrypt($new_password);
		$sql = "UPDATE " . RM_USER_TABLE . " SET " . RM_PASSWORD_COLUMN . " = '$pass', " . RM_MODIFIED_COLUMN . " = NOW() WHERE " . RM_ID_COLUMN . " = '$user_id'";
		if ($old_password !== false) {
			$old_pass = $this->password_encrypt($old_password);
			$sql .= " AND " . RM_PASSWORD_COLUMN . " = '$old_pass'";
		}
		$sql .= " LIMIT 1";
		return $this->runQuery($sql);
	}
	
	public function get_username_from_id($user_id) {
		$user_id += 0;
		$sql = "SELECT " . RM_USERNAME_COLUMN . " FROM " . RM_USER_TABLE . " WHERE " . RM_ID_COLUMN . " = '$user_id' LIMIT 1";
		$result = $this->runQuery($sql);
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_assoc($result);
			return $row[RM_USERNAME_COLUMN];
		} else {
			return false;
		}
	}
	
	public function get_id_from_username($username) {
		$CI =& get_instance();
		$CI->load->database();
		$sql = "SELECT " . RM_ID_COLUMN . " FROM " . RM_USER_TABLE . " WHERE " . RM_USERNAME_COLUMN . " = " . $CI->db->escape($username) . " LIMIT 1";
		$result = $this->runQuery($sql);
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_assoc($result);
			return $row[RM_ID_COLUMN];
		} else {
			return false;
		}
	}
	
	public function get_id_from_field($field_name, $field_value) {
		$CI =& get_instance();
		$CI->load->database();
		$sql = "SELECT " . RM_ID_COLUMN . " FROM " . RM_USER_TABLE . " WHERE $field_name = " . $CI->db->escape($field_value) . " LIMIT 1";
		$result = $this->runQuery($sql);
		if ($result->num_rows()) {
			$row = $result->row_array();
			return $row[RM_ID_COLUMN];
		} else {
			return false;
		}
	}
	
	public function generate_pass($len=8) {
		$totalChar = $len; // number of chars in the password
		$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";  // salt to select chars from
		srand((double)microtime()*1000000); // start the random generator
		$password=""; // set the inital variable
		for ($i=0;$i<$totalChar;$i++)  // loop and create password
			$password = $password . substr ($salt, rand() % strlen($salt), 1);
		return $password;
	}

	
	private function password_encrypt($password) {
		return sha1($password . RM_PASSWORD_SALT); # This could be changed to something like crypt($password) or md5($password . "SALT")
	}
	
	private function runQuery($query) {
		$CI =& get_instance();
		$CI->load->database();
		#echo $query;
		return $CI->db->query($query);
	}
}