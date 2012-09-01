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
	/// returns the balance on an account at a specific time. 
	/// If no time is specified, then current balance is returned.
	function get_balance($aid,$timestamp=0)
	{
		$aid=(int)$aid;
		$timestamp=(int)$timestamp;
		
		//get debits
		$this->db->select('sum(amount) as debits');
		$this->db->where('fromid',$aid);
		if ($timestamp!=0)
		   $this->db->where('timestamp <=',$timestamp);
		$debits=$this->db->get('transact')->row()->debits;
		
		//get credits
		$this->db->select('sum(amount) as credits');
		$this->db->where('toid',$aid);
		if ($timestamp!=0)
		   $this->db->where('timestamp <=',$timestamp);
		$credits=$this->db->get('transact')->row()->credits;
		return $credits-$debits;
	}
}