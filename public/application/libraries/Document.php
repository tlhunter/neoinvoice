<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document {
	function __construct() {
		$this->CI =& get_instance();
		if ($this->CI->session->userdata('loggedin')) {
			$this->pages = array(base_url() => 'Home',
						'app' => 'Application',
						'features' => 'Features',
						'docs' => 'Documentation',
						'contact' => 'Contact',
						'login/logout' => 'Logout');
		} else {
			$this->pages = array(base_url() => 'Home',
						'login/register' => 'Sign Up!',
						'features' => 'Features',
						'docs' => 'Documentation',
						'contact' => 'Contact');
		}
		
		$this->current_page = $this->CI->uri->segment(1); #current page = login
		if ($this->CI->uri->segment(2)) {
			$this->current_page .= "/" . $this->CI->uri->segment(2); #current page = login/auth
		}
		if (!isset($this->pages[$this->current_page])) {
			$this->current_page = $this->CI->uri->segment(1); #if login/auth isn't defined, we turn it back into login
		}
		if (empty($this->current_page)) {
			$this->current_page = 'home'; #current page = home if empty
		}
	}
	
	public function generate_page_data() {
		$data['navigation'] = $this->generate_navigation();
		$data['title'] = "NeoInvoice - Professional Invoicing and Billing";
		$data['footer'] = $this->CI->load->view('footer', null, true);
		$data['analytics'] = $this->CI->load->view('analytics', null, true);
		$data['splash'] = ($this->current_page == 'home') ? $this->CI->load->view('splash', null, true) : '';
		$data['contents'] = 'Content Undefined';
		$data['loggedin'] = $this->CI->session->userdata('loggedin');
		$data['session_username'] = $this->CI->session->userdata('username');
		$data['session_email'] = $this->CI->session->userdata('email');
		$data['session_name'] = $this->CI->session->userdata('name');
		$data['session_userid'] = $this->CI->session->userdata('id');
		$data['session_companyid'] = $this->CI->session->userdata('id');
		$data['body_class'] = 'body-' . $this->CI->uri->segment(1);
		$data['display_login_bar'] = TRUE;
		if ($this->CI->uri->segment(2)) {
			$data['body_class'] .= ' ' . 'body-' . $this->CI->uri->segment(1) . '-' . $this->CI->uri->segment(2);
		}
		return $data;
	}
	
	public function generate_navigation() {
		$i = 0;
		$first = "first ";
		$nav = '';

		foreach($this->pages AS $url => $title) {
			$i++;
			if ($this->current_page == $url || ($this->current_page == 'home' && empty($url))) {
				$active = "active";
			} else {
				$active = "";
			}
			$nav .= "<a href=\"$url\" id=\"nav$i\" class=\"$first$active\"><span>$title</span></a>\n";
			$first = '';
		}
		return $nav;
	}
	
}