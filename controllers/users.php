<?php
class Users extends CI_Controller {
	public function index($uid=-1)
	{
		$this->load->model('user');
		if ($uid==-1) $uid=$this->user->get_uid();
		$uid=(int)$uid;
		
		if (($this->user->is_staff() && $this->user->exists($uid)) || ($this->user->get_uid()===$uid))
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->user->get_data($uid)));
		else 
			$this->output
				->set_status_header(403,"You don't have permission to see this data.")
				->set_output("You don't have permission to see this data.");
	}
	public function lock()
	{
		$this->load->model('user');
		$uid=(int)$this->input->post('uid');
		$newlock=(bool)$this->input->post('lock');
		if ($this->user->is_allowed('canlockuser') && $this->user->exists($uid))
		{
			$this->user->set_lockstate($uid,$newlock);
			$data=$this->user->get_data($uid);
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($data['lockstate']));
		}
		else
			$this->output
				->set_status_header(403,"You don't have permission to lock a user.")
				->set_output("You don't have permission to lock a user.");
	}
}