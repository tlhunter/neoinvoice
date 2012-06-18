<?php
class App_Controller extends Controller {
	protected $pref_user;
	protected $perm_user;
	function __construct() {
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		parent::Controller();
		if (!$this->session->userdata('loggedin')) {
			if (xhr_request()) {
				die($this->lang->line('error_invalid_request'));
			} else {
				redirect("/");
			}
		} else {
			$user_id = $this->session->userdata('id');
			/*
			 * @todo make this one SQL call instead of two
			 */
			$this->pref_user = $this->user_model->load_preferences($user_id);
			$this->perm_user = $this->user_model->load_permissions($user_id);
			if ($this->pref_user['language']) {
				$this->lang->load('app', $this->pref_user['language']);
			} else {
				$this->lang->load('app', 'english');
			}
		}
	}
}