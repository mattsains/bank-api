<?php
class Transaction extends CI_Model {
    // when inserting time, use time()
    function __construct($auth=true)
    {
        $this->load->database();
		parent::__construct();
    }
	/// Returns a list of transactions involving an account since the date specified.
	/// if no time is specified, returns all transactions
	/// if alltext is true, this function will return data raw from the database, and will not generate new text if neccessary
	/// format: who knows
	function get_since($aid, $timestamp=0,$alltext=false)
	{
		$timestamp=(int)$timestamp;
		$aid=(int)$aid;
		$alltext=(bool)$alltext;
		$this->db->select('tid,timestamp,amount,frombankid,fromid,fromtext,tobankid,toid,totext');//everything except the log text
		$where='((fromid='.$aid.' AND frombankid=0) OR (toid='.$aid.' AND tobankid=0)) AND timestamp>='.$timestamp;
		$this->db->where($where);
		$return=array();
		if ($alltext)
	    {
			foreach($this->db->get('transact')->result() as $row)
				$return[]=array('tid'=>(int)$row->tid, 'timestamp'=>(int)$row->timestamp, 'amount'=>(float)$row->amount, 'frombankid'=>(int)$row->frombankid, 'fromid'=>(int)$row->fromid, 'fromtext'=>$row->fromtext, 'tobankid'=>(int)$row->tobankid, 'toid'=>(int)$row->toid, 'totext'=>$row->totext);
			return $return;
		}
		else
		{
			foreach ($this->db->get('transact')->result() as $row)
			{
				$returnrow=array('tid'=>(int)$row->tid, 'timestamp'=>(int)$row->timestamp);
				if ($row->fromid==$aid && $row->frombankid==0)
				{
					$returnrow['amount']=-$row->amount;
					if ($row->fromtext=='') $returnrow['text']=$this->get_text($row->tid,true);
					else $returnrow['text']=$row->fromtext;
				}
				elseif ($row->toid==$aid && $row->tobankid==0)
				{
					$returnrow['amount']=$row->amount;
					if ($row->totext=='') $returnrow['text']=$this->get_text($row->tid,false);
					else $returnrow['text']=$row->totext;
				}
				$return[]=$returnrow;
			}
			return $return;
		}
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
	///gets bigtext log of transaction
	function get_log($tid)
	{
		$tid=(int)$tid;
		
		$this->db->select('log');
		$this->db->where('tid',$tid);
		$result=$this->db->get('transact');
		if ($result->num_rows()<1) return false;
		return $result->row()->log;
	}
	/// figures out the transaction text
	/// $isfrom: true->return fromtext; false->return totext
	function get_text($tid, $isfrom)
	{
	$isfrom=(bool)$isfrom;
	$tid=(int)$tid;
	
	if ($isfrom) $this->db->select('toid,fromid,fromtext AS text');
	else $this->db->select('totext AS text');
	$this->db->where('tid',$tid);
	$result=$this->db->get('transact');
	if ($result->num_rows<1) return false;//transaction doesn't exist
	$row=$result->row();
	if ($row->text!='') return $row->text;//text already exists
	
	//if we get here, we need to make text and cache it in the database.
	if ($isfrom)
		$text='bank transfer to '.$row->toid;
	else $text='bank transfer from '.$row->fromid;
	
	$this->db->where('tid',$tid);
	$this->db->update('transact',array($isfrom?'fromtext':'totext'=>$text));
	return $text;
	}
}