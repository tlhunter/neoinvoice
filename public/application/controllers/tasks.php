<?php
class Tasks extends Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("tasks_model");
		set_time_limit(0);
	}

	function hourly() {
		echo "Time: " . date("Y-m-d H:i:s") . "\n";
		echo "Executing Hourly Task\n";
		echo "Nothing to do.\n";
	}

	function daily() {
		echo "Time: " . date("Y-m-d H:i:s") . "\n";
		echo "Executing Daily Task\n";
		$deleted_count = $this->tasks_model->delete_companies();
		echo "Deleting $deleted_count companies.\n";
		$this->tasks_model->email_invoice_reminders();
	}

	function weekly() {
		echo "Time: " . date("Y-m-d H:i:s") . "\n";
		echo "Executing Weekly Task\n";
		$filename = "system/cache/database-weekly-backup.zip";
		$this->tasks_model->email_database_backup($filename, BACKUP_EMAIL);
	}

	function monthly() {
		echo "Executing Monthly Task\n";
		echo "Time: " . date("Y-m-d H:i:s") . "\n";

	}
}