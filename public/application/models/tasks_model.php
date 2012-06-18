<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs automated database tasks which should only be triggered via CRON
 */
class Tasks_model extends Model {

    function __construct() {
        parent::Model();
    }

	/**
	 *
	 * @return int Count of affected rows
	 * @abstract Deletes all companies who are on or past their deletion date
	 */
	function delete_companies() {
		$sql = "DELETE FROM company WHERE delete_date != '0000-00-00' AND !ISNULL(delete_date) AND delete_date > '2009-01-01' AND delete_date <= CURDATE()";
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	/**
	 * @abstract Emails invoice due reminders at the 7 and 2 day intervals
	 */
	function email_invoice_reminders() {
		$invoice_model = model_load_model('invoice_model');
		$sql = "SELECT *, DATEDIFF(duedate, CURDATE()) AS upcoming FROM invoice WHERE (DATEDIFF(duedate, CURDATE()) = 7 OR DATEDIFF(CURDATE(), duedate) = 2) AND paid = 0 AND remind = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			echo "Found {$query->num_rows()} invoices to be mailed.\n";
			foreach ($query->result_array() as $row) {
				if ($invoice_model->select_email_remain_month($row['company_id'])) {
					$message = "This is an automated email reminder that your invoice is due in {$row['upcoming']} days.";
					$status = $invoice_model->mail_invoice($row['id'], FALSE, nl2br($message), 'Invoice Reminder', 'Your invoice is due soon.');
					echo "Sending Invoice {$row['id']}:\n";
					if ($status) echo "Success.\n"; else echo "Faulure.\n";
				} else {
					echo "Company {$row['company_id']} has an automated email to send but has ran out of emails.\n";
				}
			}
		} else {
			echo "There are no invoices to be sent today.\n";
		}
	}

	/**
	 *
	 * @param string $filename Temporary filename for saving database backup to
	 * @param string $email Email address of administrator to receive database backup
	 * @abstract Emails a backup of the database
	 */
	function email_database_backup($filename, $email) {
		$this->load->dbutil();
		$this->load->helper('file');
		$this->load->library('email');

		$backup =& $this->dbutil->backup(array('format' => 'zip'));
		write_file($filename, $backup);

		$this->email->from('noreply@neoinvoice.com', 'NeoInvoice');
		$this->email->to($email);
		$this->email->subject("NeoInvoice Weekly Database Backup - " . date('Y-m-d'));
		$this->email->message('Attached is the NeoInvoice database backup.');
		$this->email->attach($filename);

		$this->email->send();
		$this->email->clear();
		unlink($filename);
	}
}