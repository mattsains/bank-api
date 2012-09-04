<?php
class Transactions extends CI_Controller {
	public function index($aid)
	{
		$aid=(int)$aid;
		$this->load->model('user');
		$this->load->model('transaction');
		$this->load->model('account');
				
		if(($this->user->is_staff() && $this->account->exists($aid)) || ($this->account->belongs_to($aid)===$this->user->get_uid()))
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->transaction->get_since($aid)));
		else
			$this->output
				->set_status_header(403,"This isn't your account!")
				->set_output("This isn't your account!");
	}
}