<?php
class User extends CI_Model {
    /// makes sure the user is authenticated unless FALSE is passed
    function __construct($auth=true)
    {
        parent::__construct();
		$this->load->database();
		if($auth){$this->process_auth();}
    }
	/// gets the uid given the username of the user
	function get_uid_from_uname($uname)
	{
		$this->db->select('uid');
		$this->db->where('uname',$uname);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return array();
		else return $r->row()->uid;
	}
	/// returns the human name of a given user id
	function get_name($uid){
		$uid=(int)$uid;
		$this->db->select('name');
		$this->db->where('uid',$uid);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return array();
		else return $r->row()->name;
	}
	/// returns true if the credentials provied are correct, else false
	function auth($uid,$pass)
	{
		if($this->config->item('no_auth')) return true;
		if (!is_int($uid)) $uid=$this->get_uid_from_uname($uid);
		$uid=(int)$uid;
		
		$this->db->select('salt, hash');
		$this->db->where('uid',$uid);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return false;
		
		$hash=sha1($r->row()->salt.$pass);
		if ($hash===$r->row()->hash) return true;
		else return false;
	}
	/// forces login
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