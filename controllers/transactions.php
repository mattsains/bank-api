<?php
class Transactions extends CI_Controller {
	public function index($aid)
	{
		$this->load->model('user');
		$this->load->model('transaction');
		$this->load->model('account');
		
		$owner=$this->account->belongs_to($aid);
		
		if (!$owner)
		{
		    $this->output->set_status_header(404,"Account does not exist.");
			$this->output->set_output("Account does not exist.");
		}
		else if ($owner!=$this->user->get_uid())
			$this->output->set_status_header(403,"This isn't your account!");
		else
		{
			$transactions=$this->transaction->get_since($aid);
			echo(json_encode($transactions));
		}
	}
}