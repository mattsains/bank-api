<?php
class Accounts extends CI_Controller {
	public function index($id)
	{
		$this->load->model('account');
		$this->load->model('users');
		
		$uname=$this->users->get_name($id);
		$accounts=$this->account->get_list($id);
		echo(json_encode(array('user'=>$uname, 'accounts'=>$accounts)));
	}
}