<?php
class Account extends CI_Model {
    
    function __construct()
    {
        $this->load->database();
		parent::__construct();
    }
	/// returns a list of all accounts owned by a given user id
	function get_list($uid)
	{
	    $uid=(int)$uid;
	    $this->db->select('aid');
		$this->db->where('uid',$uid);
		$r=$this->db->get('accounts')->result();
		$returnable=array();
		foreach($r as $row){
		    $returnable[]=(int)$row->aid;
		}
		return $returnable;
	}
	/// returns the name of a given account
	function get_text($aid)
	{
		$aid=(int)$aid;
		$this->db->select('text');
		$this->db->where('aid',$aid);
		return $this->db->get('accounts')->row()->text;
    }
}