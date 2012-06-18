<?php
class Invoice_model extends Model {
    function __construct() {
        parent::Model();
		$this->table_name = 'invoice';
    }

	/**
	 * @param int $invoice_id The ID of the invoice to be selected
	 * @return array Associative array of invoice related information
	 * @abstract Gets information regarding a particular invoice
	 */
	function select_single($invoice_id) {
		$sql = "SELECT i.*, DATEDIFF(NOW(), i.duedate) AS past_due, c.name AS client_name, c.email AS client_email FROM invoice AS i, client AS c WHERE i.id = " . $this->db->escape($invoice_id) . " AND i.client_id = c.id LIMIT 1";
		$query = $this->db->query($sql);
		$results = $query->row_array();
		$sql = "SELECT SUM(amount) AS total_paid FROM payment WHERE invoice_id = " . $this->db->escape($invoice_id) . "";
		$query = $this->db->query($sql);
		$results2 = $query->row_array();
		$results['total_paid'] = number_format($results2['total_paid'], 2);
		$results['payment_remaining'] = number_format($results['amount'] - $results['total_paid'], 2);
		return $results;
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int The number of emails the company can still send
	 * @abstract Calculates the number of remaining emails, depending on sent emails over past 30 days, and the max per companies current plan
	 */
	function select_email_remain_month($company_id) {
		$sql = "SELECT pref_max_email FROM service WHERE id = (SELECT service_id FROM company WHERE id =  " . $this->db->escape($company_id) . " LIMIT 1) LIMIT 1";
		$query = $this->db->query($sql);
		$results = $query->row_array();
		$max_sent = $results['pref_max_email'];
		$sql = "SELECT COUNT(id) AS sent_emails FROM emailsent WHERE company_id = " . $this->db->escape($company_id) . " AND created > DATE_SUB(NOW(), INTERVAL 1 MONTH)";
		$query = $this->db->query($sql);
		$results = $query->row_array();
		$current_sent = $results['sent_emails'];
		$remain = $max_sent - $current_sent;
		if ($remain > 0) {
			return $remain;
		} else {
			return 0;
		}
	}

	/**
	 * @param int $company_id The ID of the company
	 * @param int $start The starting record to retrieve (0)
	 * @param int $limit The maximum number of records to return (0 = unlimited)
	 * @param bool $verbose True means return a lot of information, False means name, id, and days past due (negative is future date)
	 * @param string $sort_col The column to be used for ordering purposes
	 * @return array Associative array of information regarding the invoices
	 * @abstract Gets information for multiple invoices, useful for lists or sidebar tree's
	 */
	function select_multiple($company_id, $start = 0, $limit = 0, $verbose = TRUE, $sort_col = 'name') {
		$start += 0;
		$limit += 0;
		if ($verbose) {
			$sql = "SELECT i.*, c.name AS client_name, DATEDIFF(NOW(), i.duedate) AS past_due FROM invoice AS i, client AS c WHERE i.company_id = " . $this->db->escape($company_id) . " AND c.id = i.client_id ORDER BY i.$sort_col";
		} else {
			$sql = "SELECT id, name, DATEDIFF(NOW(), duedate) AS past_due FROM invoice WHERE company_id = " . $this->db->escape($company_id) . " ORDER BY $sort_col";
		}
		if ($limit || $start)
			$sql .= " LIMIT $start, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_payable($company_id) {
		#$sql = "SELECT i.*, SUM(p.amount) AS total_paid, i.amount - SUM(p.amount) AS payment_remaining, DATEDIFF(NOW(), duedate) AS past_due FROM invoice AS i, payment AS p WHERE i.company_id = " . $this->db->escape($company_id) . " AND p.invoice_id = i.id AND paid = 0 ORDER BY duedate";
		$sql = "
SELECT
	invoice.*,
	IFNULL(SUM(payment.amount), 0) AS total_paid,
	invoice.amount - IFNULL(SUM(payment.amount), 0) AS payment_remaining,
	DATEDIFF(NOW(), duedate) AS past_due
FROM
	invoice
LEFT JOIN
	payment
ON
	invoice.id = payment.invoice_id
WHERE
	invoice.company_id = " . $this->db->escape($company_id) . "
AND
	paid = 0
GROUP BY
	invoice.id
ORDER BY
	duedate";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @todo move this function to segments model
	 * @deprecated
	 */
	function select_segments_by_invoice($invoice_id) {
		$sql = "SELECT s.*, w.name AS worktype_name, w.hourlyrate, ROUND((EXTRACT(HOUR FROM s.duration) + (EXTRACT(MINUTE FROM s.duration) / 60)) * w.hourlyrate, 2) AS fee, u.name AS user_name, p.name AS project_name FROM segment AS s, worktype AS w, user AS u, project AS p WHERE invoice_id = " . $this->db->escape($invoice_id) . " AND s.worktype_id = w.id AND s.user_id = u.id AND s.project_id = p.id ORDER BY date, time_start";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @param int $company_id The ID of the company
	 * @return int The number of invoices belonging to the company
	 * @abstract Counts the number of invoices that belong to a company
	 */
	function get_total($company_id) {
		$sql = "SELECT COUNT(*) AS count FROM invoice WHERE company_id = " . $this->db->escape($company_id) . "";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return $data['count'];
	}

	/**
	 * @param int $invoice_id The ID of the invoice to be deleted
	 * @return bool True or False depending on the Success or Failure of the deletion
	 * @abstract Deletes an invoice
	 */
	function delete($invoice_id) {
		$sql = "DELETE FROM invoice WHERE id = " . $this->db->escape($invoice_id) . " LIMIT 1";
		if ($this->db->simple_query($sql)) {
			return $this->db->affected_rows();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $data Associative array of invoice data to be inserted, ignores .id, failes without .company_id
	 * @return bool True or False depending on the Success or Failure of the insert
	 * @abstract Creates a new invoice
	 */
	function insert($data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		if (!isset($data['company_id'])) {
			return FALSE;
		}
		$this->db->set('created', 'NOW()', FALSE);
		$this->db->set('modified', 'NOW()', FALSE);
		if ($this->db->insert('invoice', $data)) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	/**
	 * @param int $company_id The ID of the company (which could be derived from the invoice...)
	 * @param int $invoice_id The ID of the invoice the email is related to
	 * @param string $email The email address of the recipient
	 * @return bol True or False depending on the Success or Failure of recording an email
	 * @abstract Keeps track of emails, later used to determin if a company goes over quota. This function does not send an email, for that use mail_invoice()
	 * @see mail_invoice()
	 */
	function insert_sent_email($company_id, $invoice_id, $email) {
		$sql = "INSERT INTO emailsent SET company_id = " . $this->db->escape($company_id) . ", invoice_id = " . $this->db->escape($invoice_id) . ", email = " . $this->db->escape($email) . "";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param id $invoice_id The ID of the invoice
	 * @param array $data Associative array of invoice information to be updated, ignores .id
	 * @return bool True or False depending on the Success or Failure of the update
	 * @abstract Updates the selected invoice with the new information
	 */
	function update($invoice_id, $data) {
		if (isset($data['id'])) {
			unset($data['id']);
		}
		$this->db->set('modified', 'NOW()', FALSE);
		$this->db->where('id', $invoice_id);
		return $this->db->update('invoice', $data);
	}

	/**
	 * @param int $invoice_id The ID of the invoice to be touched
	 * @return bool True or False depending on the Success or Failure of the update
	 * @abstract Sets the modified field of the invoice to the curren time
	 */
	function touch($invoice_id) {
		$sql = "UPDATE invoice SET modified = NOW() WHERE id = " . $this->db->escape($invoice_id) . " LIMIT 1";
		return $this->db->simple_query($sql);
	}

	/**
	 * @param int $invoice_id The ID of the invoice the PDF will be based on
	 * @param bool $save If True, we save the file to $filename, if False, display directly to browser
	 * @param string $filename The filename to save to disk, or the filename to be saved to the browser
	 */
	function generate_pdf($invoice_id, $save = false, $filename = 'invoice.pdf') {
		$this->load->model('company_model');
		$this->load->model('expense_model');
		$this->load->library('pdf');

		$data['invoice'] = $this->invoice_model->select_single($invoice_id);
		$data['segments'] = $this->invoice_model->select_segments_by_invoice($invoice_id);
		$data['company'] = $this->company_model->select_single($data['invoice']['company_id']);
		$data['expenses']  = $this->expense_model->select_multiple($invoice_id);
        $data['image'] = $this->company_model->get_logo_image($data['invoice']['company_id']);

		$this->pdf->SetCreator('www.NeoInvoice.com');
		$this->pdf->SetAuthor($data['company']['name']);
		$this->pdf->SetTitle($data['invoice']['name']);
		$this->pdf->SetSubject('Invoice Document');
		$this->pdf->SetKeywords('Invoice, Bill');

		$this->pdf->SetFont('helvetica', '', 16);
		$this->pdf->AddPage();

		$html = $this->load->view("pdf_templates/invoice_basic", $data, TRUE);

		$this->pdf->writeHTML($html, true, false, true, false, '');

		if ($data['company']['service']['id'] === 1) {
			$this->pdf->SetFont('helvetica', '', 8);
			$this->pdf->SetY(-13);
			$this->pdf->Cell(0, 10, 'www.neoinvoice.com - Online Invoicing Software', 0, false, 'L', 0, 'http://www.neoinvoice.com', 0, false, 'T', 'M');
		}

		$this->pdf->lastPage();
		$this->pdf->Output($filename, $save ? 'I' : 'F');
	}

	/**
	 * @param int $invoice_id The ID of the invoice the email is based on
	 * @param string $recipient The email address of the recipient
	 * @param string $message Message text placed inside the HTML invoice
	 * @param string $email_title The title of the email
	 * @param string $email_subtitle The subtitle of the email, used in the HTML invoice
	 * @return bool True if the email was sent properly, False if there was an error
	 * @abstract Sends an HTML invoice to the recipient. Updates the number of sent emails, generates and attaches a PDF invoice
	 * @see insert_sent_mail(), generate_pdf()
	 */
	function mail_invoice($invoice_id, $recipient, $message, $email_title, $email_subtitle = '', $cc_sender) {
		$this->load->library('email');
		$company_model = model_load_model('company_model');
		$expense_model = model_load_model('expense_model');
		$invoice = $this->invoice_model->select_single($invoice_id);

		if (!$recipient) {
			$recipient = $invoice['client_email'];
		}
		
		$data['message'] = $message;
		$data['title'] = $email_title;
		$data['subtitle'] = $email_subtitle;
		$data['invoice'] = $invoice;
		$data['company'] = $company_model->select_single($data['invoice']['company_id']);
		$data['expenses'] = $expense_model->select_multiple($invoice_id);

		if ($invoice['itemize']) {
			$data['segments'] = $this->select_segments_by_invoice($invoice_id);
		} else {
			$data['segments'] = FALSE;
		}

		$html = $this->load->view('mail_templates/reminder', $data, TRUE);

		$pdf_filename = 'system/cache/invoice-' . md5($this->session->userdata('company_id') . '-' . $invoice_id) . '.pdf';
		$this->invoice_model->generate_pdf($invoice_id, FALSE, $pdf_filename);

		$this->email->initialize(array('mailtype' => 'html', 'useragent' => 'NeoInvoice'));
		$this->email->from($this->session->userdata('email'), $this->session->userdata('name'));
		$this->email->to($recipient);
		$this->email->subject($email_title);
		$this->email->message($html);
		$this->email->attach($pdf_filename);

		$this->invoice_model->insert_sent_email($this->session->userdata('company_id'), $invoice_id, $recipient);
		$status = $this->email->send();

        if ($status) {
            $this->update($invoice_id, array('sent' => 1));
        }

        if ($cc_sender) {
            $this->email->initialize(array('mailtype' => 'html', 'useragent' => 'NeoInvoice'));
            $this->email->from($this->session->userdata('email'), $this->session->userdata('name'));
            $this->email->to($this->session->userdata('email'));
            $this->email->subject($email_title);
            $this->email->message($html);
            $this->invoice_model->insert_sent_email($this->session->userdata('company_id'), $invoice_id, $recipient);
            $this->email->send();
        }
		
		$this->email->clear(TRUE);
		unlink($pdf_filename);
		return $status;
	}

	/**
	 * @return int Counts all invoices stored in NeoInvoice
	 */
	function count_all() {
		return $this->db->count_all('invoice');
	}

}
