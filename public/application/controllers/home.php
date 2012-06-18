<?php

class Home extends Controller {

	function __construct() {
		parent::Controller();
		if (!$this->session->userdata('loggedin')) {
			set_cookie('loggedin', '0', 7200);
		} else {
			set_cookie('loggedin', '1', 7200);
		}
		$this->load->library("document");
		$this->load->library("template");
	}
	
	function index() {
		$data = $this->document->generate_page_data();
		$data['tweets'] = $this->multicache->get("neoinvoice_tweets");
		if (!$data['tweets']) {
			$this->load->library('twitter', array('twitter_id' => 'neoinvoice'));
			$data['tweets'] = $this->twitter->execute();
			$this->multicache->set("neoinvoice_tweets", $data['tweets'], 3600);
		}
		$data['display_login_bar'] = TRUE;
		$this->template->load('main_template', 'home', $data);
	}

	function build_autocomplete() {
		$this->load->helper("autocomplete");
		autocomplete();
	}
}