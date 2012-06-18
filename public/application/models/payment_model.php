<?php
class Payment_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'payment';
    }

	/**
	 * @param int $payment_id The ID of the payment
	 * @return aray Associative array of information related to a payment
	 * @abstract Gets information regarding a payment
	 */
	function select_single($payment_id) {
		$payment_data = $this->multicache->get("payment:$payment_id");
		if (!$payment_data) {
			$sql = "SELECT * FROM {$this->table_name} WHERE id = " . $this->db->escape($payment_id) . " LIMIT 1";
			$query = $this->db->query($sql);
			$payment_data = $query->row_array();
			$this->multicache->set("payment:$payment_id", $payment_data);
		}
		return $payment_data;
	}

	/**
	 * @param int $invoice_id The ID of the invoice
	 * @return array Associative array of payment information for all payments tied to the specified invoice
	 * @abstract Gets a list of all payments belonging to the specified invoice
	 */
	function select_multiple($invoice_id) {
		$sql = "SELECT * FROM {$this->table_name} WHERE invoice_id = " . $this->db->escape($invoice_id) . " ORDER BY date_received DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int The number of payments belonging to the company
	 * @abstract Gets the total number of payments belonging to a company
	 */
	function get_total($company_id) {
		$sql = "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE company_id = " . $this->db->escape($company_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $payment_id The ID of the payment
	 * @return bool Whether or not the payment was successfully deleted
	 * @abstract Deletes a payment
	 */
	function delete($payment_id) {
		$sql = "DELETE FROM {$this->table_name} WHERE id = " . $this->db->escape($payment_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			$this->multicache->delete("payment:$payment_id");
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of payment information, must include .company_id and .invoice_Id
	 * @return bool Whether or not the payment was successfully added
	 * @abstract Stores a payment for a particular invoice, if the total amount paid is >= the invoice amount, sets the invoice as being paid
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id']) || !isset($data['invoice_id'])) {
			return FALSE;
		}
		if (!$this->db->insert($this->table_name, $data)) {
			return FALSE;
		}
		$payment_id = $this->db->insert_id();

		$sql = "SELECT SUM(amount) AS total_payment FROM payment WHERE invoice_id = {$data['invoice_id']}";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		$total_payment = $row['total_payment'];

		$sql = "SELECT amount FROM invoice WHERE id = {$data['invoice_id']} LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		$total_balance = $row['amount'];

		$success = TRUE;
		if ($total_payment >= $total_balance) {
			$this->load->model('invoice_model');
			if (!$this->invoice_model->update($data['invoice_id'], array('paid' => 1, 'paiddate' => $data['date_received']))) {
				$success = FALSE;
			}
		}
		if ($success) {
			return $payment_id;
		}
	}

	/**
	 * @param int $payment_id The ID of the payment
	 * @param array $data Associative array of information for the payment, ignores the .id
	 * @return bool Whether or not the payment was updated properly
	 * @abstract Updates a payment
	 */
	function update($payment_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->multicache->delete("payment:$payment_id");
		#$this->db->set('modified', 'NOW()', FALSE);
		$this->db->where('id', $invoice_id);
		return $this->db->update($this->table_name, $data);
	}

}