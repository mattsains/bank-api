<?php
class Transaction extends CI_Model {
    // when inserting time, use: UNIX_TIMESTAMP(UTC_TIMESTAMP())
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
		return $this->db->get('transact')->result();
	}
	/// debits an account and credits another with an amount specified.
	/// VERY INSECURE AT THE MOMENT - anyone can transfer anything
	function transfer($from, $to, $amount)
	{
		$from=(int)$from;
		$to=(int)$to;
		$amount=abs($amount); //this line might need to change, but for testing, 'ya know
		
		$data=array('timestamp'=>'UNIX_TIMESTAMP(UTC_TIMESTAMP())',
					'amount'=>$amount,
					'toid'=>$to,
					'fromid'=>$from);
		$this->db->insert('transact',$data);
		return $this->db->insert_id();
	}
}