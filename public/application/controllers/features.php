<?php

class Features extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library("document");
		$this->load->library("template");
	}
	
	function index() {
		$data = $this->document->generate_page_data();
		$this->template->load('main_template', 'features', $data);
	}
}