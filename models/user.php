<?php
class User extends CI_Model {
    /// makes sure the user is authenticated unless FALSE is passed
	private $uid;
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
		if (!is_int($uid)) $uid=$this->get_uid_from_uname($uid);
		
		$this->db->select('salt, hash');
		$this->db->where('uid',$uid);
		$r=$this->db->get('users');
		if ($r->num_rows()<1) return false;
		
		$hash=sha1($r->row()->salt.$pass);
		if ($hash===$r->row()->hash)
		{
			$this->uid=$uid;
			return true;
		}
		else return false;
	}
	/// either gives the current uid of the logged in user, or false if nobody is logged in.
	function get_uid()
	{
		if (ISSET($this->uid))
			return $this->uid;
		else return false;
	}
	/// resolves permissions
	/// pass it a uid, and a permission (string as in db),
	/// and it'll tell you whether this user is allowed that permission
	function is_allowed($uid,$perm)
	{
		$uid=(int)$uid;
		$pids=$this->get_pids($uid);
		if ($pids===false) return false;
		
		$query='SHOW COLUMNS FROM perms';
		$perms=array();
		foreach($this->db->query($query)->result() as $col)
		{
			if (!in_array($col->Field,array('pid','text','movelimit')))//fields of the db to ignore - they're not permissions
				$perms[]=$col->Field;
		}
		if (!in_array($perm,$perms))
			return false;
			
		$this->db->select('sum('.$perm.') as votes');
		$this->db->where_in('pid',$pids);
		$result=$this->db->get('perms');
		if ($result->num_rows<1)
			return false;
		return $result->row()->votes>0;
	}
	/// gets the move limit of a user
	function get_movelimit($uid)
	{
		$uid=(int)$uid;
		$pids=$this->get_pids($uid);
		if ($pids===false) return false;
		
		$this->db->select('movelimit');
		$this->db->where_in('pid',$pids);
		$result=$this->get('perms');
		if ($result->num_rows<1) return 0; //not a bank employee
		$maxlimit=0;
		foreach($result as $row)
		{
			if ($row->movelimit==-1)
			{
				$maxlimit=-1;
				break;
			} else $maxlimit=$row->movelimit>$maxlimit?$row->movelimit:$maxlimit;
		}
		return $maxlimit;
	}
	/// returns an array of permission ids of a user
	function get_pids($uid)
	{
		$uid=(int)$uid;
		$this->db->select('pids');
		$this->db->where('uid',$uid);
		$result=$this->db->get('users');
		if ($result->num_rows<1)
			return false;
			
		return explode(',',$result->row()->pids);
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