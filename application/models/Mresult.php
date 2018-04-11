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
				$this->db->like('DATE_FORMAT(q_submitted,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else if(($f=='idlevel') and ($v!='')){
					$this->db->like('a.idlevel',$v);			
				}else if(($f=='pic') and ($v!='')){
					$this->db->like('b.uname',$v);
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
		$this->db->order_by('q_submitted','desc');
		$this->db->order_by('tcreated','desc');
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
				$this->db->like('DATE_FORMAT(q_submitted,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else if(($f=='idlevel') and ($v!='')){
					$this->db->like('a.idlevel',$v);			
				}else if(($f=='pic') and ($v!='')){
					$this->db->like('b.uname',$v);
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
	
	
	public function exportresult($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(q_submitted,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(q_submitted,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('test','resulttest.idtest=test.idtest','left');
		$this->db->join('user as a','a.uuser=resulttest.uuser','left');
		$this->db->join('user as b','b.uuser=resulttest.use_uuser','left');
		$this->db->join('jdwl_tes','resulttest.idjdwl=jdwl_tes.idjdwl','left');
		$this->db->join('level','a.idlevel=level.idlevel','left');
		$this->db->where('a.uallow','1');
		$this->db->order_by('q_submitted','desc');
		return $this->db->get('resulttest')->result_array();
	}
	
	
	
	public function updateresult($data=null,$id,$idq){
		$this->db->where('idresult',$id);
		$this->db->where('idq',$idq);
		$this->db->update('resultqa',$data);
	}

	

	public function deleteresult($fid){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idresult',$fid);
		$r = $this->db->delete('resulttest');
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

	public function determinefinalscore($id){
		return $this->db->query("
		UPDATE `resulttest`
		SET q_score = (
		   SELECT SUM(tot) as tmpscore from (
		    SELECT ((SUM(rtrue)/count(*))*(select qpercent from quo_sbjct WHERE idsubject=subject.idsubject and idtest=resulttest.idtest)) as tot
		    FROM resultqa
		    LEFT JOIN `question` ON question.idq = resultqa.idq
		    LEFT JOIN `qtype` ON qtype.idqtype = question.idqtype
		    LEFT JOIN `subject` ON subject.idsubject = question.idsubject
		    LEFT JOIN `resulttest` ON resultqa.idresult = resulttest.idresult
		    WHERE resultqa.idresult = $id
		    GROUP BY question.idsubject) as b
		    )   
		    WHERE resulttest.idresult = $id;
		");
	}
	
}