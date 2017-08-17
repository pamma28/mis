<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mrole extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datarole($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('role.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->order_by('rolename','asc');
		$q = $this->db->get('role');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countrole($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('role.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		return $this->db->count_all_results("role");

	}

	public function detailrole($col,$id){
	$this->db->select($col);
	$this->db->where('idrole',$id);
	return $this->db->get('role')->result_array();		
	}
	
	public function deleterole($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idrole',$id);
			$r = $this->db->delete('role');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function saverole($fdata = null){
		return $this->db->insert('role',$fdata);
	}
	
	public function updaterole($fdata = null,$id){
		$this->db->where('idrole',$id);
		return $this->db->update('role',$fdata);
	}
	
	
	
}