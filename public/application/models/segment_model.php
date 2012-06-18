<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs Segment related database operations
 */
class Segment_model extends Model {
    function __construct() {
        parent::Model();
    }

	/**
	 * @param int $segment_id The ID of the segment
	 * @return array Associative array of segment data
	 * @abstract Gets information from the specified Segment
	 */
	function select_single($segment_id) {
		$sql = "SELECT p.name AS project_name, u.name AS user_name, w.name AS worktype_name, s.*, ADDTIME(s.time_start, s.duration) AS time_end FROM project AS p, user AS u, worktype AS w, segment AS s WHERE s.project_id = p.id AND s.user_id = u.id AND s.worktype_id = w.id AND s.id = " . $this->db->escape($segment_id) . "";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The first record to retrieve (0 = first)
	 * @param int $limit The number of records to return (0 = unlimited)
	 * @param bool $verbose If True, returns limited data, otherwise returns full data
	 * @abstract Selects all segments belonging to a company, NOT USED
	 * @deprecated
	 */
	function select_multiple($company_id, $start = 0, $limit = 0, $verbose = TRUE) {

	}

	/**
	 * @param int $project_id The ID of the project
	 * @param int $start The first record to retrieve (0 = first)
	 * @param int $limit The number of records to return (0 = unlimited)
	 * @param bool $verbose If True, returns extensive information. If False, returns id and name.
	 * @return array Associative array of segment data
	 * @abstract Gets a list of segment data for the specified project
	 */
	function select_multiple_project($project_id, $start = 0, $limit = 0, $verbose = TRUE) {
		$start += 0;
		$limit += 0;
		$limit_str = "";
		if ($limit) {
			 $limit_str = "LIMIT $start, $limit";
		}
		if ($verbose) {
			$sql = "SELECT s.*, ADDTIME(s.time_start, s.duration) AS time_end, w.name AS worktype_name, u.name AS user_name FROM segment AS s, worktype AS w, user AS u WHERE project_id = " . $this->db->escape($project_id) . " AND s.worktype_id = w.id AND s.user_id = u.id ORDER BY date, time_start $limit_str";
		} else {
			$sql = "SELECT id, name FROM segment WHERE project_id = " . $this->db->escape($project_id) . " ORDER BY date, time_start $limit_str";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $project_id The ID of the project
	 * @return array Associative array of segment information
	 * @abstract Gets a list of segments which do not already belong to an invoice for the specified project
	 */
	function select_available_project($project_id) {
		$project_id += 0;
		#$sql = "SELECT * FROM segment WHERE project_id = $project_id AND ISNULL(invoice_id)";
		#$sql = "SELECT s.*, w.name AS worktype_name, w.hourlyrate AS worktype_rate, (EXTRACT(HOUR FROM TIMEDIFF(s.time_end, s.time_start)) + (EXTRACT(MINUTE FROM TIMEDIFF(s.time_end, s.time_start)) / 60)) * w.hourlyrate AS fee FROM segment AS s, worktype AS w WHERE s.project_id = $project_id AND s.worktype_id = w.id AND ISNULL(invoice_id)";
		$sql = "SELECT s.*, ADDTIME(s.time_start, s.duration) AS time_end, w.name AS worktype_name, w.hourlyrate AS worktype_rate, (EXTRACT(HOUR FROM s.duration) + (EXTRACT(MINUTE FROM s.duration) / 60)) * w.hourlyrate AS fee, CONCAT(EXTRACT(HOUR FROM s.duration), ':', EXTRACT(MINUTE FROM s.duration)) AS time_readable FROM segment AS s, worktype AS w WHERE s.project_id = $project_id AND s.worktype_id = w.id AND ISNULL(invoice_id)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $user_id The ID of the user
	 * @param int $start The first record (0 = first)
	 * @param int $limit The number of records to return (0 = unlimited)
	 * @abstract Gets a list of all time segments created by the specified user, NOT USED
	 * @deprecated
	 */
	function select_multiple_user($user_id, $start = 0, $limit = 0) {

	}

	/**
	 * @param int $project_id The ID of the project
	 * @return int Count of segments
	 * @abstract Counts the number of segments belonging to the specified project
	 */
	function get_total_project($project_id) {
		$sql = "SELECT COUNT(*) AS count FROM segment WHERE project_id = " . $this->db->escape($project_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $segment_id The ID of the segment
	 * @return True if segment was deleted, False if there was an error
	 * @abstract Deletes the specified segment
	 */
	function delete($segment_id) {
		$sql = "DELETE FROM segment WHERE id = " . $this->db->escape($segment_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param int $segment_id The ID of the segment we are about to unassign
	 * @return bool True if item was unassigned, False if otherwise
	 */
	function unassign_from_invoice($segment_id) {
		$sql = "UPDATE segment SET invoice_id = NULL WHERE id = " . $this->db->escape($segment_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param array $segment_ids Array of segment IDs
	 * @return bool Success or failure of operation, fails if any of the specified segment IDs were not deleted
	 * @abstract Deletes a list of segments
	 */
	function delete_multiple($segment_ids) {
		for ($i = 0; $i < count($segment_ids); $i++) {
			$segment_ids[$i] += 0;
		}
		$sql = "DELETE FROM segment WHERE id IN (" . implode($segment_ids, ', ') . ")";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param array $data Associative array of segment information, ignores .id, fails without .company_id
	 * @return mixed The ID of the inserted record, or a False if there was an error
	 * @abstract Creates a new segment
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		if ($this->db->insert('segment', $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $segment_id The ID of the segment
	 * @param array $data Associative array of segment data
	 * @return bool True if segment was updated, False if there was an error
	 * @abstract Updates the provided segment with the new data
	 */
	function update($segment_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $segment_id);
		return $this->db->update('segment', $data);
	}

	/**
	 * @param array $segment_ids Array of segment ID's
	 * @param int $invoice_id The new invoice which the segments will be tied to
	 * @return int The number of segments which have been successfully updated
	 * @abstract Sets the invoice of several segments at the same time, useful for batch operations
	 */
	function multiple_set_invoice($segment_ids, $invoice_id) {
		if (empty($segment_ids)) {
			return TRUE;
		}
		$invoice_id += 0;
		for ($i = 0; $i < count($segment_ids); $i++) {
			$segment_ids[$i] += 0;
		}
		$sql = "UPDATE segment SET invoice_id = $invoice_id WHERE id IN (" . implode($segment_ids, ', ') . ")";
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	/**
	 * @param int $segment_id The ID of the segment
	 * @return bool True if segment was touched, False if there was an error
	 * @abstract Sets the modified date of the specified segment to the current time
	 */
	function touch($segment_id) {
		$sql = "UPDATE segment SET modified = NOW() WHERE id = " . $this->db->escape($segment_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @return int Count of all segments on NeoInvoice
	 * @abstract Counts all segments recorded in the application, useful for administrative statistics
	 */
	function count_all() {
		return $this->db->count_all('segment');
	}

}