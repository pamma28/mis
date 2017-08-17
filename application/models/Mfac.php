<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mfac extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datafac($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('fac.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->order_by('fname','asc');
		$q = $this->db->get('fac');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countfac($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('fac.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		return $this->db->count_all_results("fac");

	}

	public function detailfac($col,$id){
	$this->db->select($col);
	$this->db->where('idfac',$id);
	return $this->db->get('fac')->result_array();		
	}
	
	public function deletefac($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idfac',$id);
			$r = $this->db->delete('fac');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function savefac($fdata = null){
		return $this->db->insert('fac',$fdata);
	}
	
	public function updatefac($fdata = null,$id){
		$this->db->where('idfac',$id);
		return $this->db->update('fac',$fdata);
	}
	
	
	
}