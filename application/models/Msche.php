<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Msche extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	
	public function datasche($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->join('user','user.uuser=jdwl_tes.uuser','left');
		$this->db->order_by('jdwl_tes.idjdwl','desc');
		$q = $this->db->get('jdwl_tes');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function detailsche($col,$id){
	$this->db->select($col);
	$this->db->where('jdwl_tes.idjdwl',$id);
	$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
	$this->db->join('user','user.uuser=jdwl_tes.uuser','left');	
	return $this->db->get('jdwl_tes')->result_array();
	}

	public function checkmeinsche($id,$user){
	$this->db->select('uuser');
	$this->db->where('jdwl_mem.uuser',$user);
	$this->db->where('jdwl_mem.idjdwl',$id);	
	return $this->db->count_all_results("jdwl_mem");
	}
	
	public function countsche($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
	$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->join('user','user.uuser=jdwl_tes.uuser','left');
	return $this->db->count_all_results("jdwl_tes");

	}
	
	public function populatemember($col,$id){
	$this->db->select($col);
	$this->db->join('jdwl_mem','jdwl_mem.uuser=user.uuser','left');
	$this->db->where('idjdwl',$id);
	$this->db->where('uallow','1');
	return $this->db->get('user')->result_array();
	}
	
	public function updatesche($data=null,$id){
		$this->db->where('idjdwl',$id);
		return $this->db->update('jdwl_tes',$data);
	}
	
	public function addsche($data){
		return $this->db->insert('jdwl_tes',$data);
	}
	
	public function deletesche($fid){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idjdwl',$fid);
		$r = $this->db->delete('jdwl_tes');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deletemysche($fid){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idjdwl',$fid);
		$this->db->where('uuser',$this->session->userdata('user'));
		$r = $this->db->delete('jdwl_mem');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function activate($fid,$var){
		$this->db->where('idjdwl',$fid);
		return $this->db->update('jdwl_tes',$var);
	}
	
	public function opttest(){
		$this->db->select('idtest,tname,tktrgn');
		$this->db->order_by('idtest');
		$q = $this->db->get('test');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idtest']] = $row['tname'].' - '.$row['tktrgn'];
			}
		}
    return $return;
	}
	
	public function exportsche($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(jdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(jdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=jdwl_tes.uuser','left');
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->order_by('jdwl_tes.idjdwl','desc');
		return $this->db->get('jdwl_tes')->result_array();
	}
	
	public function updateselected($dt,$val){
		$v=0;$x=0;
		foreach($dt as $t){
		$this->db->where('idjdwl',$t);
		$r = $this->db->update('jdwl_tes',array('jactive'=>$val));
			if ($r){$v++;} else{$x++;}
		}
		$hsl=array(
			"v"=>$v,
			"x"=>$x
			);
		return $hsl;
	}

	//=======================================================================================
	public function datapilsche($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->join('user','user.uuser=jdwl_tes.uuser','left');
		$this->db->order_by('jdwl_tes.idjdwl','desc');
		$this->db->where('jdate >= CURDATE()');
		$q = $this->db->get('jdwl_tes');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}

	public function countpilsche($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->join('user','user.uuser=jdwl_tes.uuser','left');
		$this->db->where('jdate >= CURDATE()');
	return $this->db->count_all_results("jdwl_tes");

	}

	public function datamysche($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=jdwl_mem.idjdwl','left');
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
		$this->db->where('jdate >= CURDATE()');
		$this->db->order_by('jdwl_mem.idjdwl','desc');
		$q = $this->db->get('jdwl_mem');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}

	public function datamyprevsche($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
				}
			}
		$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=jdwl_mem.idjdwl','left');
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
		$this->db->where('jdate <= CURDATE()');
		$this->db->order_by('jdwl_mem.idjdwl','desc');
		$q = $this->db->get('jdwl_mem');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countmysche($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(jdate,"%Y")',$v);
				} else if(($f=='ulunas') and ($v!='')){
					$this->db->like($f,$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','user.uuser=jdwl_mem.uuser','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=jdwl_mem.idjdwl','left');
		$this->db->join('test','test.idtest=jdwl_tes.idtest','left');
		$this->db->where('jdwl_mem.uuser',$this->session->userdata('user'));
		//$this->db->where('jdate >= CURDATE()');
		return $this->db->count_all_results("jdwl_mem");

	}

	public function choosesche($fdata){
		return $this->db->insert('jdwl_mem',$fdata);
	}
	
}