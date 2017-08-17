<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mgender extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datajk($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('jk.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->order_by('jkname','asc');
		$q = $this->db->get('jk');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countjk($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('jk.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		return $this->db->count_all_results("jk");

	}

	public function detailjk($col,$id){
	$this->db->select($col);
	$this->db->where('idjk',$id);
	return $this->db->get('jk')->result_array();		
	}
	
	public function deletejk($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idjk',$id);
			$r = $this->db->delete('jk');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function savejk($fdata = null){
		return $this->db->insert('jk',$fdata);
	}
	
	public function updatejk($fdata = null,$id){
		$this->db->where('idjk',$id);
		return $this->db->update('jk',$fdata);
	}
	
	
	
}