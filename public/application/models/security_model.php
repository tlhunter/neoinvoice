<?php
class Security_model extends Model {

    function __construct() {
        parent::Model();
    }

	/**
	 * @param int $client_id The ID of the Client
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Client exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Client is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_client($client_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM client WHERE id = " . $this->db->escape($client_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		if (!$query->num_rows()) {
			return FALSE;
		}
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $project_id The ID of the Projet
	 * @param int $company_id TheID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Project exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Project is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_project($project_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM project WHERE id = " . $this->db->escape($project_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $expensetype_id The ID of the Expense Type
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Expense Type exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Expense Type is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_expensetype($expensetype_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM expensetype WHERE id = " . $this->db->escape($expensetype_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $expense_id The ID of the Expense
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Expense exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Expense is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_expense($expense_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM expense WHERE id = " . $this->db->escape($expense_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param array $client_ids The IDs of the Expenses
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Expenses all exist and all belong to the Company, a False if otherwise
	 * @abstract Checks to see if all of the specified Expenses are owned by the Company. If not, a flag is raised in the database.
	 */
	function own_expenses($expense_ids, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		for ($i = 0; $i < count($expense_ids); $i++) {
			$expense_ids[$i] += 0;
		}
		$sql = "SELECT company_id FROM expense WHERE id IN (" . implode($expense_ids, ', ') . ")";
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row) {
			if ($row['company_id'] != $company_id) {
				$this->warn_user($this->session->userdata('id'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param int $segment_id The ID of the Segment
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Segment exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Segment is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_segment($segment_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM segment WHERE id = " . $this->db->escape($segment_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $segment_ids The IDs of the Segments
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if all of the Segments exist and belong to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Segments are owned by the Company. If not, a flag is raised in the database.
	 */
	function own_segments($segment_ids, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		for ($i = 0; $i < count($segment_ids); $i++) {
			$segment_ids[$i] += 0;
		}
		$sql = "SELECT company_id FROM segment WHERE id IN (" . implode($segment_ids, ', ') . ")";
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row) {
			if ($row['company_id'] != $company_id) {
				$this->warn_user($this->session->userdata('id'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param int $invoice_id The ID of the Invoice
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Invoice exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Invoice is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_invoice($invoice_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM invoice WHERE id = " . $this->db->escape($invoice_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (isset($row['company_id']) && $row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $payment_id The ID of the Payment
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Payment exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Payment is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_payment($payment_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM payment WHERE id = " . $this->db->escape($payment_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $user_id The ID of the User
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the User exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified User is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_user($user_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM user WHERE id = " . $this->db->escape($user_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $worktype_id The ID of the Worktype
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Worktype exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Worktype is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_worktype($worktype_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM worktype WHERE id = " . $this->db->escape($worktype_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $usergroup_id The ID of the User Group
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the User Group exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified User Group is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_usergroup($usergroup_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM usergroup WHERE id = " . $this->db->escape($usergroup_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $ticketcategory_id The ID of the Ticket Category
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Ticket Category exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Ticket Category is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_ticketcategory($ticketcategory_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM ticket_category WHERE id = " . $this->db->escape($ticketcategory_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $ticketstage_id The ID of the Ticket Stage
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Ticket Stage exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Ticket Stage is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_ticketstage($ticketstage_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM ticket_stage WHERE id = " . $this->db->escape($ticketstage_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $ticket_id The ID of the Ticket
	 * @param int $company_id The ID of the Company (defaults to the Company ID in session)
	 * @return bool A true if the Ticket exists and belongs to the Company, a False if otherwise
	 * @abstract Checks to see if the specified Ticket is owned by the Company. If not, a flag is raised in the database.
	 */
	function own_ticket($ticket_id, $company_id = false) {
		if (!$company_id) {
			$company_id = $this->session->userdata('company_id');
		}
		$sql = "SELECT company_id FROM ticket WHERE id = " . $this->db->escape($ticket_id) . " LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['company_id'] == $company_id) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $plan_id The ID of the Plan
	 * @param float $plan_cost The cost to be validated against the plan
	 * @return bool True if the plan does cost the provided amount, a False if otherwise
	 * @abstract Used during payment processing to see if the amount paid equals the amount the plan costs
	 */
	function confirm_price($plan_id, $plan_cost) {
		$plan_id += 0;
		$plan_cost += 0;
		$sql = "SELECT price FROM service WHERE id = $plan_id LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if ($row['price'] == $plan_cost) {
			return TRUE;
		} else {
			$this->warn_user($this->session->userdata('id'));
			return FALSE;
		}
	}

	/**
	 * @param int $user_id The ID of the user
	 * @return bool True if user was warned, False if there was a problem
	 * @abstract Flags the user as having done something inappropriate with the application
	 */
	function warn_user($user_id) {
		$user_id += 0;
		$sql = "UPDATE user SET warning = warning + 1 WHERE id = $user_id LIMIT 1";
		return $this->db->simple_query($sql);
	}
	
	
}