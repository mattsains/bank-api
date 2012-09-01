<?php
class Debug extends CI_Controller {
	public function hash($pass)
	{
		$this->load->helper('passwd');
		var_dump(salted_hash($pass));
	}
	public function auth($usr,$pass)
	{
		$this->load->model('user');
		if ($this->user->auth($usr,$pass))
		echo('Authenticated.');
		else echo('Incorrect.');
	}
	public function addmoneys($aid)
	{
		$this->load->model('transaction');
		$this->transaction->transfer(0,$aid,500);
		echo "done.";
	}
}