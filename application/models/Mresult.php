<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mresult extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }

	public function dataresult($column = null, $per_page = null, $page = null, $filter = null){
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
		$this->db->join('test','resulttest.idtest=test.idtest','left');
		$this->db->join('user as a','a.uuser=resulttest.uuser','left');
		$this->db->join('user as b','b.uuser=resulttest.use_uuser','left');
		$this->db->join('jdwl_tes','resulttest.idjdwl=jdwl_tes.idjdwl','left');
		$this->db->join('level','a.idlevel=level.idlevel','left');
		$this->db->where('a.uallow','1');
		$this->db->order_by('tcreated','desc');
		$this->db->order_by('mem','desc');
		$this->db->order_by('q_score','desc');
		$q = $this->db->get('resulttest');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function detailresult($col,$id){
		$this->db->select($col);
		$this->db->join('test','test.idtest=resulttest.idtest','left');
		$this->db->join('jdwl_tes','jdwl_tes.idjdwl=resulttest.idjdwl','right');
		$this->db->join('user a','a.uuser=resulttest.uuser','left');
		$this->db->join('level','level.idlevel=a.idlevel','left');
		$this->db->join('user b','b.uuser=resulttest.use_uuser','left');
		$this->db->where('resulttest.idresult',$id);
		$this->db->order_by('jstart','desc');
	return $this->db->get('resulttest')->result_array();
	}
	
	public function countresult($filter = null){
	
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
		$this->db->join('test','resulttest.idtest=test.idtest','left');
		$this->db->join('user as a','a.uuser=resulttest.uuser','left');
		$this->db->join('user as b','b.uuser=resulttest.use_uuser','left');
		$this->db->where('a.uallow','1');
		return $this->db->count_all_results("resulttest");

	}
	
	
	
	public function optuser(){
		$this->db->select('uuser,uname,uemail');
		$this->db->where('idrole','3');
		$this->db->where('ustatus',null);
		$this->db->where('user.idjk',null);
		$this->db->where('user.idfac',null);
		$this->db->where('user.uallow','1');
		$this->db->order_by('ucreated','desc');
		$q = $this->db->get('user');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['uuser']] = $row['uuser'].' ('.$row['uname'].' - '.$row['uemail'].')';
			}
		}
    return $return;
	}
	
	public function importdata($dtxl){
		$this->load->model('Mlogin');//load model
		$tot = 0;
		$faileduser = '';
		foreach ($dtxl as $key=>$val) {
            $val['ucreated'] = DATE('Y-m-d H:i:s');
            //check duplication row (username)
			$cuser = $this->Mlogin->checkuser($val['uuser']);           
			$cmail = $this->Mlogin->checkmail($val['uemail']);                      
            if (($cuser==0) and ($cmail==0)) {
                $this->db->insert('user', $val);
				$tot ++;
            } else {
				$faileduser[]=($key+1).'. '.$val['uuser'].' - '.$val['uemail'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$faileduser));
	}
	
	public function exportlogin($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
		$this->db->where('user.idrole','3');
		$this->db->where('user.idjk<>','');
		$this->db->where('user.idfac<>','');
		$this->db->where('ustatus<>','');
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(ucreated,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(ucreated,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('fac','user.idfac=fac.idfac','left');
		$this->db->join('jk','user.idjk=jk.idjk','left');
		$this->db->order_by('ucreated','desc');
		return $this->db->get('user')->result_array();
	}
	
	
	
	public function updateresult($data=null,$id,$idq){
		$this->db->where('idresult',$id);
		$this->db->where('idq',$idq);
		$this->db->update('resultqa',$data);
	}

	public function getScoreMember($id,$user){
		$this->db->where('idresult',$id);
		$this->db->update('resulttest',array('use_uuser'=>$user));
		$this->db->select(array('q_score','uuser'));
		$this->db->where('idresult',$id);
		return $this->db->get('resulttest')->row();
	}
	
	public function getLevel(){
		$this->db->select(array('idlevel','lvllow','lvlup'));
		$this->db->order_by('lvlup','desc');
		return $this->db->get('level')->result_array();
	}

	public function updateLevelMember($data,$id){
		$this->db->where('uuser',$id);
		return $this->db->update('user',$data);
	}


	public function deletepds($fid){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('uuser',$fid);
		$r = $this->db->delete('user');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}

	public function getmanualcorrectthistest($id){
		$this->db->select('question.idq');
		$this->db->where('idresult',$id);
		$this->db->where('qtype.qmanual',1);
		$this->db->join('question','question.idq=resultqa.idq','left');
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		return $this->db->get('resultqa')->result_array();
		 
	}
	
	
}