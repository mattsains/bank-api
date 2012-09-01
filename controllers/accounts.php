<?php
class Accounts extends CI_Controller {
	public function index($id)
	{
		$this->load->model('account');
		$this->load->model('user');
		
		$uname=$this->user->get_name($id);
		$accounts=$this->account->get_list($id);
		echo(json_encode(array('user'=>$uname, 'accounts'=>$accounts)));
	}
	public function balance($id)
	{
		$id=(int)$id;
		$this->load->model('account');
		
		echo($this->account->get_balance($id));
	}
}