<?php
class Statistics extends App_Controller {
	public $colors;

	function __construct() {
		parent::__construct();
		$this->load->helper("gchart");
		$this->load->model("statistics_model");
		$this->colors = array("ff7f00", "007fff", "7fff00", "ff007f", "00ff7f", "7f00ff", "7f7f00", "7f007f", "007f7f", "ff0000", "00ff00", "0000ff", "ffff00", "ff00ff", "00ffff");
	}
	
	function index() {
		if ($this->perm_user->reports->access) {
			/* Start Overall Worktype */
			$this->load->model('worktype_model');
			$worktypes = $this->worktype_model->select_multiple($this->session->userdata('company_id'));
			$values = array();
			$legends = array();
			$labels = array();
			foreach($worktypes AS $worktype) {
				if ($worktype['segment_count']) {
					$labels[] = $worktype['name'];
					$legends[] = $worktype['name'] . ' (' . $worktype['hour_float'] . ')';
					$values[] = $worktype['hour_float'];
				}
			}
			$overall_worktype_chart = new gPieChart(500, 200);
			#$overall_worktype_chart->set3D(true);
			$overall_worktype_chart->addDataSet($values);
			$overall_worktype_chart->setLegend($legends);
			$overall_worktype_chart->setLabels($labels);
			$overall_worktype_chart->setColors($this->colors);
			$data['worktypes'] = $overall_worktype_chart->getUrl();
			
			/* Start User Group By Time */
			$usertimes = $this->statistics_model->select_users_groupby_time($this->session->userdata('company_id'));
			$values = array();
			$legends = array();
			$labels = array();
			foreach($usertimes AS $usertime) {
				if ($usertime['segment_count']) {
					$labels[] = $usertime['user_name'];
					$legends[] = $usertime['user_name'] . ' (' . $usertime['hour_float'] . ')';
					$values[] = $usertime['hour_float'];
				}
			}

			$overall_usertime_chart = new gPieChart(500, 200);
			#$overall_worktype_chart->set3D(true);
			$overall_usertime_chart->addDataSet($values);
			$overall_usertime_chart->setLegend($legends);
			$overall_usertime_chart->setLabels($labels);
			$overall_usertime_chart->setColors($this->colors);
			$data['usertimes'] = $overall_usertime_chart->getUrl();

			$this->load->view('statistics/xhr_overview', $data);
		} else {
			$data['error'] = $this->lang->line('error_low_perm');
			$this->load->view('xhr_error', $data);
		}
	}

}