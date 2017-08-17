<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mnotif extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datanotif($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('notif.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','notif.uuser=user.uuser','left');
		$this->db->order_by('npublish','desc');
		$q = $this->db->get('notif');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countnotif($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('notif.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','notif.uuser=user.uuser','left');
		return $this->db->count_all_results("notif");

	}

	public function detailnotif($col,$id){
	$this->db->select($col);
	$this->db->where('idnotif',$id);
	$this->db->join('user','notif.uuser=user.uuser','left');
	return $this->db->get('notif')->result_array();		
	}
	
	public function deletenotif($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idnotif',$id);
			$r = $this->db->delete('notif');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function savenotif($fdata = null){
		return $this->db->insert('notif',$fdata);
	}
	
	public function updatenotif($fdata = null,$id){
		$this->db->where('idnotif',$id);
		return $this->db->update('notif',$fdata);
	}
	
	
	
}