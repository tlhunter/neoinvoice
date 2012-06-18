<?php
class Company_model extends Model {

	var $logo_hash = "SET-PASSWORD-SALT-HERE";

    function __construct() {
        parent::Model();
    }

	/**
	 * @param int $company_id The ID of the company to be selected
	 * @return array Associative array of extensive information regarding the company
	 */
	function select_single($company_id) {
		#$data = $this->multicache->get("company:$company_id");
		#if (!$data) {
			$sql = "SELECT * FROM company WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			$data = $query->row_array();
			$sql = "SELECT * FROM service WHERE id = {$data['service_id']} LIMIT 1";
			$query = $this->db->query($sql);
			$data['service'] = $query->row_array();
			$sql = "SELECT COUNT(id) AS count FROM user WHERE company_id = $company_id LIMIT 1";
			$query = $this->db->query($sql);
			$temp = $query->row_array();
			$data['user_count'] = $temp['count'];
		#	$this->multicache->set("company:$company_id", $data);
		#}
		return $data;
	}

	/**
	 * @param int $company_id The ID of the company to be selected
	 * @return array Associative array of immediate information regarding the company
	 */
	function select_single_simple($company_id) {
		#$data = $this->multicache->get("company:$company_id");
		#if (!$data) {
			$sql = "SELECT * FROM company WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			$data = $query->row_array();
		#	$this->multicache->set("company:$company_id", $data);
		#}
		return $data;
	}

	/**
	 * @param int $company_id The ID of the company to be selected
	 * @return array Associative array representing the company level preferences, or False if there is an error
	 */
	function load_preferences($company_id) {
		#$company_prefs = $this->multicache->get("company_prefs:$company_id");
		#if (!$company_prefs) {
			$sql = "SELECT preferences FROM company WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$company_prefs = json_decode($row->preferences, TRUE);
			} else {
				$company_prefs = FALSE;
			}
			#$this->multicache->set("company_prefs:$company_id", $company_prefs);
		#}
		return $company_prefs;
	}

	/**
	 * @param int $company_id The ID of the company to be deleted
	 * @return bool True or False depending on Success or Failure of deletion
	 * @deprecated
	 * @abstract This is used to delete companies, but we handle this functionality elsewhere
	 */
	function delete($company_id) {
		$sql = "DELETE FROM company WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
		if ($this->db->simple_query()) {
			$this->multicache->delete("company:$company_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company to be marked for deletion
	 * @return bool True or False depending on Success or Failure of deletion
	 * @abstract This function is used to set a company to be deleted at a future date (handled by Cron) by adding a flag to the company db table row
	 */
	function delete_mark($company_id) {
		$sql = "UPDATE company SET delete_date = (CURDATE() + INTERVAL 1 WEEK) WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("company:$company_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company whose future deletion event we want canceled
	 * @return bool True or False depending on Success or Failure or the delete cancel
	 * @abstract Cancels the future deletion date by removing the flag from the company table
	 */
	function delete_cancel($company_id) {
		$sql = "UPDATE company SET delete_date = NULL WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("company:$company_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of company information, ignores .id
	 * @return mixed The ID of the newly added company, or False if there was an error
	 * @abstract Creates a new company in our database
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if ($this->db->insert('company', $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company to be updated
	 * @param array $data Asociative array of company information, ignores .id
	 * @return bool True or False depending on Success or Failure of company update
	 * @abstract Updates a companies information in the database and deletes cache
	 */
	function update($company_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->where('id', $company_id);
		if ($this->db->update('company', $data)) {
			/**
			 * @todo update cache instead of deleting it
			 */
			$this->multicache->delete("company:$company_id");
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return bool True or False depending on Success or Failure of company update
	 * @abstract Sets the companies modified time to now
	 */
	function touch($company_id) {
		$sql = "UPDATE company SET modified = NOW() WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
		$this->multicache->delete("company:$company_id");
		return $this->db->simple_query($sql);
	}

	/**
	 * @param int $company_id The ID of the company to be updated
	 * @param array $data Associated array of preference data to be updated
	 * @return bool True or False depending on the Success or Failure of company update
	 * @abstract Updates the companies preferences, stored in the database as JSON
	 */
	function update_preferences($company_id, $data) {
		$prefs_json = json_encode($data);
		$sql = "UPDATE company SET preferences = '$prefs_json' WHERE id = " . $this->db->escape($company_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			#$this->multicache->set("company_prefs:$company_id", $data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company to be updated
	 * @param int $plan_id The ID of the plan the company is to be upgraded to
	 * @param int $days The number of days the company is to be upgraded
	 * @return <type> True or False depending on Success or Failure of upgrade
	 * @abstract Upgrades (or downgrades) a company to the new plan level for the specified days
	 */
	function upgrade_company($company_id, $plan_id, $days) {
		$company_id += 0;
		$plan_id += 0;
		$days += 0;
		if (!$company_id || !$plan_id || !$days) {
			return FALSE;
		}

		$sql = "SELECT * FROM company WHERE id = $company_id LIMIT 1";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		if (!$data) {
			return FALSE;
		}
		
		$current_plan = $data['service_id'];
		$current_expire = $data['service_expire'];
		if (!$current_expire || $current_expire == '0000-00-00') {
			$current_expire = date('Y-m-d');
		}
		$sql = "UPDATE company SET service_id = $plan_id, service_expire = DATE_ADD('$current_expire', INTERVAL $days DAY) WHERE id = $company_id LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param string $coupon_code The coupon code (e.g. BETATEST2010)
	 * @return array Associative array of coupon information
	 * @abstract Gets information from a coupon based on it's code, used during signup
	 */
	function coupon_data_from_name($coupon_code) {
		$sql = "SELECT * FROM coupon WHERE name = " . $this->db->escape($coupon_code) . " LIMIT 1";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		if (!$data) {
			return FALSE;
		}
		return $data;
	}

	/**
	 * @return int Number of all companies using NeoInvoice
	 * @abstract This is used for overall NeoInvoice statistics
	 */
	function count_all() {
		return $this->db->count_all('company');
	}

    /**
     * @abstract Use this function to set a company logo (overwrite's existing)
     * @param int $company_id The ID of the company
     * @param int $uploaded_filename The file path to the newly uploaded image
     * @return File path to new logo image
     */
	function set_logo_image($company_id, $uploaded_filename) {
		$filename = $this->_get_filename_from_company_id($company_id);
        $size = getimagesize($uploaded_filename);
        if ($size[0] >= 16 && $size[0] <= 1000 && $size[1] >= 16 && $size[1] <= 400 && $size['mime'] == 'image/jpeg' && (filesize($uploaded_filename) <= 512 * 1024) ) {
            return move_uploaded_file($uploaded_filename, $filename);
        }
        return false;
	}

    /**
     * @abstract Use this function to get the path to the logo image
     * @param int $company_id The ID of the company
     * @return string The path to the company logo image, or false if one does not exist
     */
	function get_logo_image($company_id) {
		$filename = $this->_get_filename_from_company_id($company_id);
		if (file_exists($filename)) {
			return $filename;
		} else {
			return FALSE;
		}
	}

    /**
     * @abstract This function will delete the companies image
     * @param int $company_id The ID of the company
     * @return bool A true if the image was deleted, a false if otherwise
     */
	function remove_logo_image($company_id) {
		$filename = $this->_get_filename_from_company_id($company_id);
		return unlink($filename);
	}

    /**
     * @abstract Used to hash out the filename based on the company id
     * @param int $company_id The ID of the company
     * @return string The path to the image (even if it doesn't exist yet)
     */
	private function _get_filename_from_company_id($company_id) {
		$company_id += 0;
		return "assets/logos/$company_id-" . md5($this->logo_hash . $company_id) . ".jpg";
	}

}
