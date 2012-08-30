<?php
class Users extends CI_Model {
    
    function __construct($auth=true)
    {
        $this->load->database();
		if($auth){$this->process_auth();}
		parent::__construct();
    }
	function get_uid_from_uname($uname)
	{
		$this->db->select('uid');
		$this->db->where('uname',$uname);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return array();
		else return $r->row()->uid;
	}
	function get_name($uid){
		$uid=(int)$uid;
		$this->db->select('name');
		$this->db->where('uid',$uid);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return array();
		else return $r->row()->name;
	}
	function auth($uid,$pass)
	{
		if($this->config->item('no_auth')) return true;
		if (!is_int($uid)) $uid=$this->get_uid_from_uname($uid);
		$uid=(int)$uid;
		
		$this->db->select('salt, hash');
		$this->db->where('uid',$uid);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return 0;
		
		$hash=sha1($r->row()->salt.$pass);
		if ($hash===$r->row()->hash) return $uid;
		else return 0;
	}
	function process_auth()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			echo('Login required.');
			die();
		}
		else if($this->auth($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])==false)
		{
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			echo('Login required.');
			die();
		}
	}
}