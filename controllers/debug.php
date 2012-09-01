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
}