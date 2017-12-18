<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Msubject extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	
	public function datasbjct($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->join('quo_sbjct as q','test.idtest=q.idtest','left');
		$this->db->order_by('tcreated','desc');
		$q = $this->db->get('test');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datasubject($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->order_by('idsubject','desc');
		$q = $this->db->get('subject');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datasbjctbyid($column = null, $id){
		$this->db->select($column);
		$this->db->join('test as t','quo_sbjct.idtest=t.idtest','left');
		$this->db->join('subject as s','quo_sbjct.idsubject=s.idsubject','left');
		$q = $this->db->where('quo_sbjct.idtest',$id);
		$this->db->order_by('qsort','asc');
		$q = $this->db->get('quo_sbjct');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function detailtest($col,$id){
	$this->db->select($col);
	$this->db->where('idtest',$id);
	$this->db->join('user as a','test.uuser=a.uuser','left');
	$this->db->join('user as b','test.tuse_uuser=b.uuser','left');
	return $this->db->get('test')->result_array();
	}
	
	public function detailsubject($col,$id){
	$this->db->select($col);
	$this->db->where('idsubject',$id);
	return $this->db->get('subject')->result_array();
	}
	
	public function countsbjct($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
	$this->db->join('quo_sbjct as q','test.idtest=q.idtest','left');
	return $this->db->count_all_results("test");

	}
	
	public function countsubject($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
	return $this->db->count_all_results("subject");

	}
	
	public function updatesubjecttest($data=null,$id,$sub){
		$this->db->where('idtest',$id);
		$this->db->where('idsubject',$sub);
		return $this->db->update('quo_sbjct',$data);
	}
	
	public function addsubjecttest($data){
		return $this->db->insert('quo_sbjct',$data);
	}
	
	public function addsubject($data){
		return $this->db->insert('subject',$data);
	}
	
	public function deletesubjecttest($fs,$ft){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idtest',$ft);
		$this->db->where('idsubject',$fs);
		$r = $this->db->delete('quo_sbjct');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deletesubject($id){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idsubject',$id);
		$r = $this->db->delete('subject');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function gettotpercent($id){
		$this->db->select('sum(qpercent) as tot');
		$this->db->where('idtest',$id);
		return $this->db->get('quo_sbjct')->row()->tot;
	}
	
	public function optsbjctavail($id){
		$qu ='SELECT s.idsubject, subject FROM subject s WHERE NOT EXISTS (SELECT 1 FROM quo_sbjct t WHERE s.idsubject = t.idsubject AND t.idtest = '.$id.')';
		$q = $this->db->query($qu);
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idsubject']] = $row['subject'];
			}
		}
    return $return;
	}

	public function optsbjct(){
		$this->db->select(array('idsubject','subject'));
		$q = $this->db->get('subject');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idsubject']] = $row['subject'];
			}
		}
    return $return;
	}
	public function maxsort($id){
		$this->db->select('idsubject');
		$this->db->where('idtest',$id);
		return $this->db->count_all_results("quo_sbjct");
	}
}