<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs statistics related database operations
 */
class Statistics_model extends Model {
    function __construct() {
        parent::Model();
    }

	/**
	 *
	 * @param int $company_id The ID of the company
	 * @return array Associative array containing user_id, user_name, segment_count, hour_float
	 * @abstract Gets statistical information of all users and how much work they have recorded in the application
	 */
	function select_users_groupby_time($company_id) {
		$sql = "SELECT user_id, (SELECT name FROM user WHERE id = user_id) AS user_name, COUNT(id) AS segment_count, FORMAT(IFNULL(SUM(EXTRACT(HOUR FROM duration) + (EXTRACT(MINUTE FROM duration)/60)), '0.00'), 2) AS hour_float FROM segment WHERE company_id = " . $this->db->escape($company_id) . " GROUP BY user_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

}