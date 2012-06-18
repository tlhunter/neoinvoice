<?php
class Contact extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library('email');
		$this->load->library("document");
		$this->load->library("template");
		$this->load->library('user_agent');
	}
	
	function index() {
		$data = $this->document->generate_page_data();
		$data['message'] = '';
		$data['name'] = $this->session->userdata('name') ? : '';
		$data['email'] = $this->session->userdata('email') ? : "";
		$this->template->load('main_template', 'contact', $data);
	}

	function send() {
		$data = $this->document->generate_page_data();
		$this->email->from($this->input->post("email"), htmlentities($this->input->post("name")));
		$this->email->to('tlhunter+neoinvoice@gmail.com');
		$this->email->subject('NeoInvoice Submission: ' . $this->input->post("inquiery"));
		$this->email->message($this->input->post("message") . "\n\n" . $this->input->post("phone") . "\n\n" . $this->agent->agent_string());
		if ($this->email->send()) {
			$data['message'] = "<div class='success'>Your message has been sent.</div>";
		} else {
			$data['message'] = "<div class='error'>There was an error sending your email.</div>";
		}
		$data['name'] = $this->input->post('name') ? : '';
		$data['email'] = $this->input->post('email') ? : "";
		$this->template->load('main_template', 'contact', $data);
	}
}