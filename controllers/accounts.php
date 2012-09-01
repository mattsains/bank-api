<?php
class Accounts extends CI_Controller {
	/// returns the account ids of the logged in user
	public function index()
	{
		$this->load->model('user');
		$this->load->model('account');
		$uid=$this->user->get_uid();
		$uname=$this->user->get_name($uid);
		
		$accounts=$this->account->get_list($uid);
		$this->output
		  ->set_content_type('application/json')
		  ->set_output(json_encode($accounts));
	}
	/// returns the balance of an account
	public function balance($aid=null)
	{
		if ($aid!=null)
		{
			$this->load->model('user');
			$this->load->model('account');
			if ($this->user->get_uid()===$this->account->belongs_to($aid))
			{
				$this->output
				  ->set_content_type('application/json')
				  ->set_output(json_encode($this->account->get_balance($aid)));
			}
			else $this->output->set_status_header(401,"This isn't your account!");
		}
		else die ("Account does not exist.");
	}
}