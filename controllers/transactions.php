<?php
class Transactions extends CI_Controller {
	public function index($aid)
	{
		$this->load->model('transaction');
		$this->load->model('user');
		
		$transactions=$this->transaction->get_since($aid);
		echo(json_encode(array('aid'=>$aid, 'trans'=>$transactions)));
	}
}