<?php
class Transaction extends CI_Model {
    // when inserting time, use time()
    function __construct($auth=true)
    {
        $this->load->database();
		parent::__construct();
    }
	/// Returns a list of transactions involving an account since the date specified.
	/// if no time is specified, returns all transactions.
	/// format: {{'tid':transaction id, 'timestamp':timestamp of transaction, 'fromid':aid the transaction comes from, 'toid':aid transaction goes to.},,}
	function get_since($aid, $timestamp=0)
	{
		$timestamp=(int)$timestamp;
		$aid=(int)$aid;
		
		$this->db->where('fromid',$aid);
		$this->db->or_where('toid',$aid);
		
		if ($timestamp!==0)
		  $this->db->where('timestamp >=',$timestamp);
		$return=array();
		foreach($this->db->get('transact')->result() as $row)
		{
			$return[]=array('tid'=>(int)$row->tid, 
			                'timestamp'=>(int)$row->timestamp, 
							'amount'=>(float)$row->amount, 
							'fromid'=>(int)$row->fromid, 
							'toid'=>(int)$row->toid,
							'text'=>$row->fromid===$aid?$row->fromtext:$row->totext);
		}
		return $return;
	}
	/// debits an account and credits another with an amount specified.
	/// VERY INSECURE AT THE MOMENT - anyone can transfer anything
	function transfer($from, $fromtext, $to, $totext, $amount)
	{
		$from=(int)$from;
		$to=(int)$to;
		$amount=abs($amount); //this line might need to change, but for testing, 'ya know
		
		$data=array('timestamp'=>time(),
					'amount'=>$amount,
					'toid'=>$to,
					'totext'=>$totext,
					'fromid'=>$from,
					'fromtext'=>$fromtext);
		$this->db->insert('transact',$data);
		return $this->db->insert_id();
	}
}