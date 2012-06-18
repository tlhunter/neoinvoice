<?php
class Login extends Controller {

	function __construct() {
		parent::Controller();
		$this->load->library("document");
		$this->load->library("template");
		$this->load->library('rm_user');
		$this->load->library('email');
		$this->load->helper('cookie');
		$this->session_details = array('id', 'company_id', 'preferences', 'username', 'email', 'name', 'usergroup_id');
	}
	
	function index() {
		$data = $this->document->generate_page_data();
		$this->template->load('main_template', 'form_login', $data);
	}
	
	function forgot($action='form') {
		if ($action == 'form') {
			$data = $this->document->generate_page_data();
			$this->template->load('main_template', 'form_forgot_pass', $data);
		} else if ($action == 'execute') {
			$user_id = $this->rm_user->get_id_from_field('email', $this->input->post('email'));
			if (!$user_id) {
				$data = $this->document->generate_page_data();
				$data['error'] = "Sorry, but this email address is not in our records!";
				$this->template->load('main_template', 'form_forgot_pass', $data);
			} else {
				$temp_password = $this->rm_user->generate_pass(12);
				$this->rm_user->set($user_id, 'lost_password', $temp_password);
				$this->email->from('noreply@neoinvoice.com');
				$this->email->to($this->input->post('email'));
				$this->email->subject("NeoInvoice New Password");
				$this->email->message("Your temporary password is $temp_password.");
				$status = $this->email->send();
				$data = $this->document->generate_page_data();
                if ($status) {
                    $data['contents'] = "<h2><span>Forgot Password</span></h2>\n<p>A new password has been sent to your email address.</p>\n";
                } else {
                    $data['contents'] = "<h2><span>Forgot Password</span></h2>\n<div class='error'>There was an error sending your lost password!</div>\n";
                }
				$this->load->view('main_template', $data);
			}
		}
	}
	
	function register($coupon = '') {
		if (empty($_POST)) {
			$data = $this->document->generate_page_data();
			$session_coupon = $this->session->userdata('coupon');
			if ($coupon) {
				$this->session->set_userdata('coupon', $coupon);
			}
			if (!$coupon && $session_coupon) {
				$coupon = $session_coupon;
			}
			$data['coupon'] = $coupon;
			$this->template->load('main_template', 'form_register', $data);
		} else {
			$this->load->model('company_model');
			$this->load->model('worktype_model');
			$this->load->model('expensetype_model');
			$this->load->model('user_model');
			$this->load->model('ticketcategory_model');
			$this->load->model('ticketstage_model');
			
			$password = $this->input->post('password');
			$password2 = $this->input->post('password2');
			$username = $this->_alphanumeric($this->input->post('username'));
			$name = $this->input->post('your_name', TRUE);
			$company_name = $this->input->post('company_name', TRUE);
			$email = $this->input->post('email');
			$coupon = $this->_alphanumeric($this->input->post('coupon'));
			$gotcha = (int) $this->input->post('gotcha');
			
			if ($password != $password2) {
				$submit_error = TRUE;
				$submit_error_message = "Passwords do not match";
			} else if (empty($username) || $this->input->post('username') != $this->_alphanumeric($this->input->post('username'))) {
				$submit_error = TRUE;
				$submit_error_message = "Invalid username";
			} else if (empty($company_name)) {
				$submit_error = TRUE;
				$submit_error_message = "Empty Company Name";
			} else if (empty($name)) {
				$submit_error = TRUE;
				$submit_error_message = "Empty Real Name";
			} else if (empty($password)) {
				$submit_error = TRUE;
				$submit_error_message = "Invalid Password";
			} else if ($gotcha != 55) {
				$submit_error = TRUE;
				$submit_error_message = "Please Enable JavaScript";
			} else {
				$coupon_data = $this->company_model->coupon_data_from_name($coupon);
				if (!$coupon_data) {
					$company_data = array(
						'name' => $company_name
					);
				} else {
					$expires = strtotime(date("Y-m-d") . " +{$coupon_data['default_service_expire']} day");
					$expires = date('Y-m-d', $expires) . ' 00:00:00';
					$company_data = array(
						'name' => $company_name,
						'coupon_id' => $coupon_data['id'],
						'service_id' => $coupon_data['default_service_id'],
						'service_expire' => $expires
					);
				}
				$company_id = $this->company_model->insert($company_data);
				if ($company_id) {
					$perm_json = json_encode($this->user_model->perm_template('admin'));
					$pref_json = json_encode($this->user_model->pref_template());
					$user_id = $this->rm_user->create(array(
						'username' => $username,
						'password' => $password,
						'email' => $email,
						'active' => 1,
						'permissions' => $perm_json,
						'company_id' => $company_id,
						'preferences' => $pref_json,
						'name' => $name));
				}
				if (!$user_id) {
					$submit_error = TRUE;
					$submit_error_message = "Duplicate username or email address";
					$this->company_model->delete($company_id);
				} else {
					# CREATE A DEFAULT WORK TYPE
					$this->worktype_model->insert(array('company_id' => $company_id, 'hourlyrate' => '0.00', 'name' => 'Pro Bono'));

					# CREATE A DEFAULT EXPENSE TYPE
					$this->expensetype_model->insert(array('company_id' => $company_id, 'taxable' => '0', 'name' => 'Hosting'));

					# CREATE A DEFAULT BUGFIX CATEGORY
					$bugfix = $this->ticketcategory_model->insert(array('company_id' => $company_id, 'name' => 'Bug Fix (Generic)'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $bugfix, 'name' => 'Not Started'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $bugfix, 'name' => 'In Progress'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $bugfix, 'name' => 'Work Complete', 'closed' => '1'));
					unset($bugfix);

					# CREATE A DEFAULT ENHANCEMENT CATEGORY
					$enhancement = $this->ticketcategory_model->insert(array('company_id' => $company_id, 'name' => 'Enhancement (Generic)'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $enhancement, 'name' => 'Not Started'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $enhancement, 'name' => 'In Progress'));
					$this->ticketstage_model->insert(array('company_id' => $company_id, 'ticket_category_id' => $enhancement, 'name' => 'Work Complete', 'closed' => '1'));
					unset($enhancement);
					
					$data = $this->document->generate_page_data();
					$data['username'] = $this->input->post('username');
					$data['email'] = $this->input->post('email');
					#set_cookie('loggedin', '1', 7200);
					/*
					 *  @todo populate session variables and log user in automatically
					 */
					# redirect('app');
					$this->template->load('main_template', 'account_created', $data);
					$submit_error = FALSE;
				}
			}
			if ($submit_error) {
				$data = $this->document->generate_page_data();
				$data['error'] = $submit_error_message;
				$this->template->load('main_template', 'form_register', $data);
			}
		}
	}
	
	function logout() {
		foreach($this->session_details AS $key) {
			$this->session->unset_userdata($key);
		}
		$this->session->unset_userdata('loggedin');
		$this->session->sess_destroy();
		set_cookie('loggedin', '0', 7200);
		redirect('/');
	}
	
	function auth() {
		$user_id = $this->rm_user->auth($this->input->post('login'), $this->input->post('password'));
		if ($user_id > 0) {
			$user_data = $this->rm_user->get($user_id, $this->session_details);
			$this->session->set_userdata($user_data);
			$this->session->set_userdata('loggedin', TRUE);
			set_cookie('loggedin', '1', 7200);
			redirect('app');
		} else {
			$data = $this->document->generate_page_data();
			$this->template->load('main_template', 'error_login', $data);
		}
	}

	function _alphanumeric($value) {
		return preg_replace("[^a-zA-Z0-9_]", "", $value);
	}

}
