<?php
class Appstatistics extends Controller {
	function __construct() {
		parent::Controller();
	}

	function index() {
		$this->load->view("app_statistics");
	}

	function data() {
		$this->load->model("company_model");
		$data['count_companys'] = $this->company_model->count_all();
		$this->load->model("invoice_model");
		$data['count_invoices'] = $this->invoice_model->count_all();
		$this->load->model("segment_model");
		$data['count_segments'] = $this->segment_model->count_all();
		
		header("content-type: text/xml");
		$this->load->view("app_statistics_data", $data);
	}
}