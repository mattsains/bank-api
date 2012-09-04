<?php
class Accounts extends CI_Controller {
	/// returns the account ids of the logged in user
	public function index($uid=-1)
	{
		$uid=(int)$uid;
		$this->load->model('user');
		$this->load->model('account');
		if ($uid==-1) $uid=$this->user->get_uid();
		
		if (($this->user->is_staff() && $this->user->exists($uid)) || $uid===$this->user->get_uid()) 
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->account->get_list($uid)));
		else
			$this->output
				->set_status_header(403,"This isn't your account!")
			    ->set_output("This isn't your account!");
	}
	/// returns the balance of an account
	public function balance($aid=null)
	{
		if ($aid!=null)
		{
			$aid=(int)$aid;
			$this->load->model('user');
			$this->load->model('account');
			if (($this->user->is_staff() && $this->account->exists($aid)) || ($this->account->belongs_to($aid)===$this->user->get_uid()))
				$this->output
				  ->set_content_type('application/json')
				  ->set_output(json_encode($this->account->get_balance($aid)));
			else
				$this->output
					->set_status_header(403,"This isn't your account!")
					->set_output("This isn't your account!");
		}
		else $this->output->set_status_header(400,"No account specified.");
	}
}