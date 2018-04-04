<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Magn extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function dataagn($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','agenda.uuser=user.uuser','left');
		$this->db->order_by('agcreated','desc');
		$q = $this->db->get('agenda');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countagn($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','agenda.uuser=user.uuser','left');
		return $this->db->count_all_results("agenda");

	}

	public function detailagn($col,$id){
	$this->db->select($col);
	$this->db->where('idagenda',$id);
	$this->db->join('user','agenda.uuser=user.uuser','left');
	return $this->db->get('agenda')->result_array();		
	}
	
	public function deleteagn($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idagenda',$id);
			$r = $this->db->delete('agenda');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function saveagn($fdata = null){
		return $this->db->insert('agenda',$fdata);
	}
	
	public function updateagn($fdata = null,$id){
		$this->db->where('idagenda',$id);
		return $this->db->update('agenda',$fdata);
	}
	
	public function agnimportdata($dtxl){
		$tot = 0;
		$failed = array();
		foreach ($dtxl as $key=>$val) {
              $r =  $this->db->insert('agenda', $val);
				
			if ($r)	{
			$tot ++;
            } else {
				$failed[]=($key+1).'. '.$val['agtitle'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed));
	}
	
	public function exportagn($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(agcreated,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(agcreated,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=agenda.uuser','left');
		$this->db->order_by('agcreated','desc');
		return $this->db->get('agenda')->result_array();
	}
	

	public function showagn($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				
					$this->db->like($f,$v);			
			}
		}
		$this->db->where('agdate >= CURDATE()');
		$this->db->join('user','agenda.uuser=user.uuser','left');
		$this->db->order_by('agdate','desc');
		$this->db->order_by('agtime','desc');
		$q = $this->db->get('agenda');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}

	public function showprevagn($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				
					$this->db->like($f,$v);			
			}
		}
		$this->db->where('agdate < CURDATE()');
		$this->db->join('user','agenda.uuser=user.uuser','left');
		$this->db->order_by('agdate','desc');
		$this->db->order_by('agtime','desc');
		$q = $this->db->get('agenda');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	
}