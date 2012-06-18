<?php
class Segment extends App_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model("segment_model");
	}

	function list_items($page = 0) {
		/* Probably not going to use this */
	}

	function list_by_project($project_id, $page = 0) {
		$this->load->helper('table_sort_helper');
		if ($this->security_model->own_project($project_id)) {
			$data['segments'] = $this->segment_model->select_multiple_project($project_id, $page, $this->pref_user['per_page'], TRUE);
			$data['total'] = $this->segment_model->get_total_project($project_id);
			$data['page'] = $page;
			$data['per_page'] = $this->pref_user['per_page'];
			$data['project_id'] = $project_id;
			$this->load->view('segment/xhr_list_items_project', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function list_by_user($user_id, $page = 0) {
		$this->load->helper('table_sort_helper');
		if ($this->security_model->own_user($user_id)) {
			$data['segments'] = $this->segment_model->select_multiple_user($user_id, $page, $this->pref_user['per_page'], TRUE);
			$data['total'] = $this->segment_model->get_total_user($user_id);
			$data['page'] = $page;
			$data['per_page'] = $this->pref_user['per_page'];
			$data['user_id'] = $user_id;
			# data['user_name'] = something
			$this->load->view('segment/xhr_list_items_user', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function view($segment_id) {
		if ($this->security_model->own_segment($segment_id)) {
			$data['segment'] = $this->segment_model->select_single($segment_id);
			$this->load->view('segment/xhr_view', $data);
		} else {
			$data['error'] = $this->lang->line('error_select_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete($segment_id) {
		if ($this->security_model->own_segment($segment_id) && $this->perm_user->segment->delete) {
			$data['segment'] = $this->segment_model->select_single($segment_id);
			$this->load->view('segment/xhr_delete', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_submit($segment_id) {
		if ($this->security_model->own_segment($segment_id) && $this->perm_user->segment->delete && $this->segment_model->delete($segment_id)) {
			$data['message'] = $this->lang->line('segment_deleted');
			$this->load->view('segment/xhr_delete_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_multiple($segment_ids) {
		$data['segment_ids'] = explode(':', $segment_ids);
		if ($this->security_model->own_segments($data['segment_ids']) && $this->perm_user->segment->delete) {
			$data['count'] = count($data['segment_ids']);
			$data['segment_ids'] = $segment_ids;
			$this->load->view('segment/xhr_delete_multiple', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_segments');
			$this->load->view('xhr_error', $data);
		}
	}

	function delete_multiple_submit($segment_ids) {
		$data['segment_ids'] = explode(':', $segment_ids);
		if ($this->security_model->own_segments($data['segment_ids']) && $this->perm_user->segment->delete && $this->segment_model->delete_multiple($data['segment_ids'])) {
			$data['message'] = $this->lang->line('segments_deleted');
			$this->load->view('segment/xhr_delete_multiple_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_delete_segments');
			$this->load->view('xhr_error', $data);
		}
	}

	function add($project_id = 0) {
		if ($this->perm_user->segment->create) {
			$this->load->model('ticket_model');
			$data['tickets'] = $this->ticket_model->select_relevant($this->session->userdata('company_id'), $this->session->userdata('id'), $this->session->userdata('usergroup_id'));
			$data['project_dropdown'] = dropdown_generic('project', $project_id, 'project_id', 'company_id', 0, 'id', 'name', array('active' => 1));
			$data['worktype_dropdown'] = dropdown_generic('worktype', null, 'worktype_id');
			$data['time_start_dropdown'] = dropdown_time('time_start_hour', 'time_start_minute', date('G'), 0);
			$data['time_end_dropdown'] = dropdown_time('time_end_hour', 'time_end_minute', date('G')+2, 0);
			$data['project_id'] = $project_id;
			$this->load->view('segment/xhr_add', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

	/*
	 * @todo fix up this code, make it more stable
	 */
	function add_submit() {
		$data['date'] = $this->input->post('date');
		$data['project_id'] = (int) $this->input->post('project_id');
		$data['worktype_id'] = (int) $this->input->post('worktype_id');
		$data['ticket_id'] = (int) $this->input->post('ticket_id') ? : NULL;
		$data['billable'] = post_checkbox('billable');
		$data['time_start'] = (int) $this->input->post('time_start_hour') . ':' . (int) $this->input->post('time_start_minute') . ':00';
		$time_end_received = (int) $this->input->post('time_end_hour') . ':' . (int) $this->input->post('time_end_minute') . ':00';
		$time_end = strtotime($data['date'] . ' ' . $time_end_received);
		$time_start = strtotime($data['date'] . ' ' . $data['time_start']);
		$raw_difference = $time_end - $time_start;
		$hour_difference = floor($raw_difference / 3600);
		$minute_difference = ($raw_difference - $hour_difference * 3600) / 60;
		$data['duration'] = str_pad($hour_difference, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute_difference, 2, '0', STR_PAD_LEFT) . ':00';
		$data['content'] = htmlentities($this->input->post('content'));
		$data['company_id'] = (int) $this->session->userdata('company_id');
		$data['user_id'] = (int) $this->session->userdata('id');
		$dur_start = $this->input->post('time_start_hour') . $this->input->post('time_start_minute');
		$dur_end = $this->input->post('time_end_hour') . $this->input->post('time_end_minute');
		$dur = (int) $dur_start - (int) $dur_end < 0;
		if ($dur && $data['project_id'] && $data['worktype_id'] && $this->perm_user->segment->create && $this->segment_model->insert($data)) {
			$data['message'] = $this->lang->line('segment_added');
			$this->load->view('segment/xhr_add_submit', $data);
		} else {
			$data['error'] = $this->lang->line('error_create_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	function edit($segment_id) {
		if ($this->security_model->own_segment($segment_id) && $this->perm_user->segment->update) {
			$this->load->model('ticket_model');
			$data['tickets'] = $this->ticket_model->select_relevant($this->session->userdata('company_id'), $this->session->userdata('id'), $this->session->userdata('usergroup_id'));
			$data['segment'] = $this->segment_model->select_single($segment_id);
			$data['project_dropdown'] = dropdown_generic('project', $data['segment']['project_id'], 'project_id');
			$data['worktype_dropdown'] = dropdown_generic('worktype', $data['segment']['worktype_id'], 'worktype_id');
			$temp = explode(':', $data['segment']['time_start']);
			$start_hour = $temp[0];
			$start_minute = $temp[1];
			$temp = explode(':', $data['segment']['time_end']);
			$end_hour = $temp[0];
			$end_minute = $temp[1];
			$data['time_start_dropdown'] = dropdown_time('time_start_hour', 'time_start_minute', $start_hour, $start_minute);
			$data['time_end_dropdown'] = dropdown_time('time_end_hour', 'time_end_minute', $end_hour, $end_minute);
			$this->load->view('segment/xhr_edit', $data);
		} else {
			$data['error'] = $this->lang->line('error_edit_segment');
			$this->load->view('xhr_error', $data);
		}
	}

	/*
	 * @todo fix up this code, make it more stable
	 */
	function edit_submit($segment_id) {
		$data['date'] = $this->input->post('date');
		$data['project_id'] = (int) $this->input->post('project_id');
		$data['worktype_id'] = (int) $this->input->post('worktype_id');
		$data['ticket_id'] = (int) $this->input->post('ticket_id') ? : NULL;
		$data['billable'] = post_checkbox('billable');
		$data['time_start'] = (int) $this->input->post('time_start_hour') . ':' . (int) $this->input->post('time_start_minute') . ':00';
		$time_end_received = (int) $this->input->post('time_end_hour') . ':' . (int) $this->input->post('time_end_minute') . ':00';
		$time_end = strtotime($data['date'] . ' ' . $time_end_received);
		$time_start = strtotime($data['date'] . ' ' . $data['time_start']);
		$raw_difference = $time_end - $time_start;
		$hour_difference = floor($raw_difference / 3600);
		$minute_difference = ($raw_difference - $hour_difference * 3600) / 60;
		$data['duration'] = str_pad($hour_difference, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute_difference, 2, '0', STR_PAD_LEFT) . ':00';
		$data['content'] = htmlentities($this->input->post('content'));
		$data['company_id'] = (int) $this->session->userdata('company_id');
		$data['user_id'] = (int) $this->session->userdata('id');
		if ($this->security_model->own_segment($segment_id) && $this->perm_user->segment->update && $this->segment_model->update($segment_id, $data)) {
			$this->load->view('segment/xhr_edit_submit');
		} else {
			$data['error'] = $this->lang->line('error_update_segment');
			$this->load->view('xhr_error', $data);
		}
	}
}