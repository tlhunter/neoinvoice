<?php

class Widget extends Controller {
    protected $username;
    protected $password;

    protected $user = array();
    
    static $WIDGET_VERSION = 20110705;

	function __construct() {
		parent::Controller();
	}
	
    /**
     * Get list of projects and worktypes
     * @todo make it work
     */
	function get_worktypes_projects() {
        $this->_authenticate();
		$this->load->model('worktype_model');
		$this->load->model('project_model');	
		$worktypes = $this->worktype_model->select_multiple($this->user['company_id']);
		$projects = $this->project_model->select_multiple($this->user['company_id'], null, null, FALSE);
		$this->_output_data(
			array(
				'worktypes' => $worktypes,
				'projects' => $projects,
			)
		);
	}
    
    function debug() {
?>
<h3>tickets</h3>
<form method="post" action="<?=base_url()?>widget/tickets">
user: <input name="username" /><br />
pass: <input name="password" type="password" /><br />
project: <input name="project" /><br />
<input type="submit" />
</form>

<h3>get_worktypes_projects</h3>
<form method="post" action="<?=base_url()?>widget/get_worktypes_projects">
user: <input name="username" /><br />
pass: <input name="password" type="password" /><br />
<input type="submit" />
</form>

<h3>save</h3>
<form method="post" action="<?=base_url()?>widget/save">
user: <input name="username" /><br />
pass: <input name="password" type="password" /><br />
project: <input name="project" /><br />
worktype: <input name="worktype" /><br />
ticket: <input name="ticket" /><br />
notes: <textarea name="notes"></textarea><br />
<input type="submit" />
</form>

<?php
    }
    
    /**
     * Save time
     * @todo make it work
     */
    function save() {
        $this->_authenticate();
		if (!$project = $this->input->post('project')) {
			$this->_output_error("Missing Project ID");
		}
		if (!$worktype = $this->input->post('worktype')) {
			$this->_output_error("Missing Worktype ID");
		}
		$ticket = $this->input->post('ticket');
		$notes = htmlentities($this->input->post('notes'));
		if (!$duration = $this->input->post('duration')) {
			$this->_output_error("Missing Duration");
		}
		if (!$starttime = $this->input->post('starttime')) {
			$this->_output_error("Missing Start Time");
		}
		$this->load->model('segment_model');
		$data = array(
			'company_id' => $this->user['company_id'],
			'project_id' => $project,
			'user_id' => $this->user['id'],
			'worktype_id' => $worktype,
			'date' => date('Y-m-d'),
			'time_start' => $starttime . ':00',
			'duration' => $duration . ':00',
			'content' => $notes,
		);
		if ($ticket) {
			$data['ticket_id'] = $ticket;
		}
		if ($this->segment_model->insert($data)) {
			$this->_output_data(array('success' => 'Time was inserted successfully.'));
		} else {
			$this->_output_error('There was an error recording your time.');
		}
    }
    
    /**
     * Display the current version of the widget software
     */
    function version() {
		$data = array('version' => self::$WIDGET_VERSION);
        $this->_output_data($data);
    }
    
    /**
     * Get a list of tickets associated with a project
     */
    function tickets() {
        $this->_authenticate();
        if (!$project_id = $this->input->post('project')) {
            $this->_output_error('Missing Project ID');
        }
        
        $this->load->model('security_model');
        if (!$this->security_model->own_project($project_id, $this->user['company_id'])) {
            $this->_output_error('Project Doesn\'t Exist');
        }
        
        $this->load->model('ticket_model');

        $data = array('tickets' => $this->ticket_model->select_by_project($project_id));
        
        $this->_output_data($data);
        
    }
    
    function _authenticate() {
        $this->load->library('rm_user');
        $this->username = $this->input->post('username');
        $this->password = $this->input->post('password');
        
        if (!$this->username || !$this->password) {
            $this->_output_error('Missing Username or Password');
        }
        
        if (!$user_id = $this->rm_user->auth($this->username, $this->password)) {
            $this->_output_error('Invalid Username or Password');
        }
        
        $this->user = $this->rm_user->get($user_id, array(
            'id',
            'company_id',
        ));
        
        return true;
    }
    
    function _output_error($data) {
        $this->_output_data(array(
            'error' => $data
        ));
        exit();
    }
    
    function _output_data($data) {
        header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        #header('Content-type: application/json');
        echo json_encode($data);
    }
}
