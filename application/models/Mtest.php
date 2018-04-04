<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mtest extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	
	public function datatest($column = null, $per_page = null, $page = null, $filter = null){
	
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
		$this->db->join('user as a','test.uuser=a.uuser','left');
		$this->db->join('user as b','test.tuse_uuser=b.uuser','left');
		$this->db->order_by('tcreated','desc');
		$q = $this->db->get('test');
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
	
	public function counttest($filter = null){
	
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
	$this->db->join('user as a','a.uuser=test.uuser','left');
	$this->db->join('user as b','b.uuser=test.tuse_uuser','left');
	return $this->db->count_all_results("test");

	}
	
	public function updatetest($data=null,$id){
		$this->db->where('idtest',$id);
		return $this->db->update('test',$data);
	}
	
	public function addtest($data){
		return $this->db->insert('test',$data);
	}
	
	public function deletetest($fid){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idtest',$fid);
		$r = $this->db->delete('test');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}

	// =========================== member section ========================
	public function datamytest($column = null, $per_page = null, $page = null, $filter = null,$expired= false){
	
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
		$this->db->join('jdwl_tes','jdwl_tes.idtest=test.idtest','left');
		$this->db->join('jdwl_mem','jdwl_mem.idjdwl=jdwl_tes.idjdwl','left');
		$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
		$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
		if (!$expired) {$this->db->where('jdate > CURDATE()');}
		$this->db->order_by('jdate','desc');
		$this->db->order_by('jmdate','desc');
		$q = $this->db->get('test');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countmytest($filter = null,$expired = false){
	
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
	$this->db->join('jdwl_tes','jdwl_tes.idtest=test.idtest','left');
	$this->db->join('jdwl_mem','jdwl_mem.idjdwl=jdwl_tes.idjdwl','left');
	$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
	if (!$expired) {$this->db->where('jdate > CURDATE()');}
	$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
	return $this->db->count_all_results("test");

	}

	public function detailmytest($col,$id){
	$this->db->select($col);
	$this->db->where('jdwl_mem.idjdwl',$id);
	$this->db->join('jdwl_tes','jdwl_tes.idtest=test.idtest','left');
	$this->db->join('jdwl_mem','jdwl_mem.idjdwl=jdwl_tes.idjdwl','left');
	$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
	$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
	return $this->db->get('test')->result_array();
	}
	
	public function bestquestioncode($arrcode,$idtest){
		$totalneeded = $this->sumqneeded($idtest);
			$prevtot = 0;
			$bestcode = $arrcode[0];
			foreach($arrcode as $v){
				$totalquest = $this->sumqavailable($idtest,$bestcode);
				$this->db->select('idtest');
				$this->db->where('resulttest.q_randcode',$v);
				$this->db->where('resulttest.idtest',$idtest);
				$numrow = $this->db->get('resulttest')->num_rows();
				if(($prevtot > $numrow) and ($totalneeded < $totalquest)){
					$bestcode = $v;
				} 
					$prevtot = $numrow;
			}
		return $bestcode;
	}

	public function getalluniquecodebyidtest($idtest){
		$this->db->distinct();
		$this->db->select('q_group');
		$this->db->join('quo_sbjct','quo_sbjct.idsubject = question.idsubject','left');
		$this->db->where('idtest',$idtest);
		return $this->db->get('question')->result_array();
	}

	public function sumqneeded($idtest){
		$this->db->select('SUM(qtot) as tot');
		$this->db->where('idtest',$idtest);
		return $this->db->get('quo_sbjct')->row()->tot;

	}

	public function sumqavailable($idsub,$code){
		$this->db->select('idq');
		$this->db->where('q_group',$code);
		$this->db->where('idsubject',$code);
		return $this->db->get('question')->num_rows();
	}

	public function countmethistest($idtest){
		$this->db->select('idtest');
		$this->db->where('idtest',$idtest);
		$this->db->where('uuser', $this->session->userdata('user'));
		return $this->db->count_all_results('resulttest');
	}

	public function savemyresult($data){
		return $this->db->insert('resulttest',$data);
	}

	public function getmeidresult($id){
		$this->db->select('idresult');
		$this->db->where('idtest',$id);
		$this->db->where('uuser', $this->session->userdata('user'));
		return $this->db->get('resulttest')->row()->idresult;
	}

	public function getdetailmyresult($col,$id){
		$this->db->select($col);
		$this->db->where('idresult',$id);
		return $this->db->get('resulttest')->result_array();
	}

	public function datamyresulttest($column = null, $per_page = null, $page = null, $filter = null){
	
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
		$this->db->join('test','test.idtest=resulttest.idtest','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=resulttest.idjdwl','right');
		$this->db->join('user a','a.uuser=resulttest.uuser','left');
		$this->db->join('level','level.idlevel=a.idlevel','left');
		$this->db->join('user b','b.uuser=resulttest.use_uuser','left');
		$this->db->where('resulttest.uuser',$this->session->userdata('user'));
		$this->db->order_by('jstart','desc');
		$q = $this->db->get('resulttest');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countmyresulttest($filter = null){
	
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
		$this->db->join('test','test.idtest=resulttest.idtest','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=resulttest.idjdwl','left');
		$this->db->join('user','user.uuser=resulttest.use_uuser','left');
		$this->db->where('resulttest.uuser',$this->session->userdata('user'));
		return $this->db->count_all_results("resulttest");

	}

	public function gettotalactivetest(){
		$this->db->select('idjdwl');
		$this->db->where('jactive','1');
		return $this->db->count_all_results("jdwl_tes");
	}

	public function gettotresulttest(){
		$this->db->select('idresult');
		$this->db->where('q_score',null);
		return $this->db->count_all_results("resulttest");
	}
}