<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mlvl extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datalevel($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('level.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->order_by('idlevel','desc');
		$q = $this->db->get('level');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countlevel($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('level.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		return $this->db->count_all_results("level");

	}

	public function detaillevel($col,$id){
	$this->db->select($col);
	$this->db->where('idlevel',$id);
	return $this->db->get('level')->result_array();		
	}

	public function optlevel(){
		$this->db->select('idlevel,lvlname');
		$this->db->order_by('idlevel','desc');
		$q = $this->db->get('level');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idlevel']] = $row['lvlname'];
			}
		}
    return $return;
	}
	
	public function deletelevel($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idlevel',$id);
			$r = $this->db->delete('level');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function savelevel($fdata = null){
		return $this->db->insert('level',$fdata);
	}
	
	public function updatelevel($fdata = null,$id){
		$this->db->where('idlevel',$id);
		return $this->db->update('level',$fdata);
	}
	
	
	
}