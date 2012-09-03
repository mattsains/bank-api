<?php
class Account extends CI_Model {
    
    function __construct()
    {
        $this->load->database();
		parent::__construct();
    }
	/// returns a list of all accounts owned by a given user id
	/// format: list of integers-->aid's
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
	/// returns some important information about accounts
	function get_details($aid)
	{
		$aid=(int)$aid;
		$this->db->select(array('text','atid'));
		$this->db->where('aid',$aid);
		
		$row=$this->db->get('accounts')->row();
		return array('text'=>$row->text,
					 'atid'=>$row->atid,
					 'balance'=>$this->get_balance($aid));
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
	/// returns the UID of the accountholder. Returns false if the account does not exist
	function belongs_to($aid)
	{
		$aid=(int)$aid;
		if ($aid===0) return false; //special account which has no owner
		$this->db->select('uid');
		$this->db->where('aid',$aid);
		$result=$this->db->get('accounts');
		if ($result->num_rows<1) //non-existent account
			return false;
		else return $result->row()->uid;
	}
}