<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mq extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	
	public function datasubject($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
					$this->db->like($f,$v);
				}
			}
		$this->db->order_by('subject','asc');
		$q = $this->db->get('subject');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function dataquest($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
					$this->db->like($f,$v);
				}
			}
		$this->db->join('user','user.uuser=question.uuser','left');
		$this->db->join('subject','subject.idsubject=question.idsubject','left');
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->order_by('subject','asc');
		$this->db->order_by('q_group','asc');
		$this->db->order_by('qtype','desc');
		$this->db->order_by('q_bundle','asc');
		$q = $this->db->get('question');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function dataqtype($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
					$this->db->like($f,$v);
				}
			}
		$this->db->order_by('idqtype','desc');
		$q = $this->db->get('qtype');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datatestbyid($column = null, $id){
		$this->db->select($column);
		$this->db->join('test as t','quo_sbjct.idtest=t.idtest','inner');
		$this->db->where('quo_sbjct.idsubject',$id);
		$this->db->order_by('tname','asc');
		$this->db->limit(5,0); //5 row
		$q = $this->db->get('quo_sbjct');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function dataquestbyid($column = null, $id){
		$this->db->select($column);
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->where('question.idsubject',$id);
		$this->db->order_by('idq','asc');
		$this->db->limit(5,0);
		$q = $this->db->get('question');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countquestbyid($id){
		$this->db->where('idsubject',$id);
		return $this->db->count_all_results("question");
	}
	
	public function counttestbyid($id){
		$this->db->where('idsubject',$id);
		return $this->db->count_all_results("quo_sbjct");
	}
	
	public function getqtotbyid($id){
		$this->db->select('max(qtot) as tot');
		$this->db->where('idsubject',$id);
		return $this->db->get("quo_sbjct")->row()->tot;
	}
	
	public function checkattach($id){
		$this->db->select('q_file');
		$this->db->where('idattach',$id);
		return $this->db->get('ques_attach')->row()->q_file;
	}
	
	public function populatequest($col,$id){
		$this->db->select($col);
		$this->db->join('user','user.uuser = question.uuser','left');
		$this->db->join('ques_attach','ques_attach.idattach = question.idattach','left');
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->where('idsubject',$id);
		$this->db->order_by('q_group','asc');
		$this->db->order_by('q_bundle','asc');
		$this->db->order_by('qtype','desc');
		return $this->db->get('question')->result_array();
		
	}
	
	public function populateanswer($col,$id){
		$this->db->select($col);
		$this->db->where('idq',$id);
		$this->db->order_by('idans','asc');
		return $this->db->get('answer')->result_array();
		
	}
	
	public function checkkeyexist($id){
		$this->db->select('max(key_ans) as k');
		$this->db->where('idq',$id);
		return $this->db->get('answer')->row()->k;
		
	}
	
	public function populatetest($col,$id){
		$this->db->select($col);
		$this->db->join('quo_sbjct','quo_sbjct.idtest = test.idtest','left');
		$this->db->join('user','user.uuser = test.uuser','left');
		$this->db->where('quo_sbjct.idsubject',$id);
		$this->db->order_by('tname','asc');
		return $this->db->get('test')->result_array();
		
	}
	
	public function getattach($col,$qid){
		$this->db->select($col);
		$this->db->where('idattach',$qid);
		return $this->db->get('ques_attach')->result_array();
	}
	
	public function getattachbyq($col,$qid){
		$this->db->select($col);
		$this->db->where('idq',$qid);
		return $this->db->get('ques_attach')->result_array();
	}
	
	public function detailsubject($col,$id){
	$this->db->select($col);
	$this->db->where('subject.idsubject',$id);
	$this->db->join('quo_sbjct','quo_sbjct.idsubject=subject.idsubject','left');
	return $this->db->get('subject')->result_array();
	}
	
	public function detailquest($col,$id){
	$this->db->select($col);
	$this->db->where('question.idq',$id);
	return $this->db->get('question')->result_array();
	}
	
	public function detailallquest($col,$id){
	$this->db->select($col);
	$this->db->join('user','user.uuser = question.uuser','left');
	$this->db->join('ques_attach','ques_attach.idattach = question.idattach','left');
	$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
	$this->db->where('question.idq',$id);
	return $this->db->get('question')->result_array();
	}
	
	
	public function getanswer($col,$id){
	$this->db->select($col);
	$this->db->where('idans',$id);
	return $this->db->get('answer')->result_array();
	}
	
	public function countsubject($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
					$this->db->like($f,$v);
			}
		}
	return $this->db->count_all_results('subject');
	}
	
	public function countqtype($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				$this->db->like($f,$v);			
			}
		}
	return $this->db->count_all_results('qtype');
	}
	
	public function countquest($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				$this->db->like($f,$v);			
			}
		}
	$this->db->join('user','user.uuser=question.uuser','left');
	$this->db->join('subject','subject.idsubject=question.idsubject','left');
	$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
	return $this->db->count_all_results("question");
	}
	
	public function updatequestiontest($data=null,$idq){
		$this->db->where('idq',$idq);
		return $this->db->update('question',$data);
	}
	
	public function updateanswer($data=null,$id){
		$this->db->where('idans',$id);
		return $this->db->update('answer',$data);
	}
	
	public function updateqtype($data=null,$id){
		$this->db->where('idqtype',$id);
		return $this->db->update('qtype',$data);
	}
	
	public function updateattach($data=null,$id){
		$this->db->where('idattach',$id);
		return $this->db->update('ques_attach',$data);
	}
	
	public function updatequest($data=null,$id){
		$this->db->where('idq',$id);
		return $this->db->update('question',$data);
	}
	
	public function addsubjecttest($data){
		return $this->db->insert('quo_sbjct',$data);
	}
	
	public function addanswer($data){
		return $this->db->insert('answer',$data);
	}
	
	public function addquest($data){
		return $this->db->insert('question',$data);
	}
	
	public function addquesttype($data){
		return $this->db->insert('qtype',$data);
	}
	
	public function addattach($data){
		return $this->db->insert('ques_attach',$data);
	}
	
	public function deletesubject($fs,$ft){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idtest',$ft);
		$this->db->where('idsubject',$fs);
		$r = $this->db->delete('quo_sbjct');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deleteattach($idt){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idattach',$idt);
		$r = $this->db->delete('ques_attach');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deleteanswer($idt){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idans',$idt);
		$r = $this->db->delete('answer');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deleteallanswer($idt){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idq',$idt);
		$r = $this->db->delete('answer');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deletequest($idq){
		//$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idq',$idq);
		$r = $this->db->delete('question');
		//$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function deleteqtype($id){
		//$this->db->query('SET foreign_key_checks = 0');
			//delete answer
			$this->db->where('idqtype',$id);
			$r = $this->db->delete('qtype');
		
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
	
	public function getqtype($col){
		$this->db->select($col);
		return $this->db->get('qtype')->result_array();
	}
	
	public function getquesttype($col,$id){
		$this->db->select($col);
		$this->db->where('idqtype',$id);
		return $this->db->get('qtype')->result_array();
	}
	
	public function getattachfile($idt){
		$this->db->select('q_file');
		$this->db->where('idattach',$idt);
	return	$this->db->get('ques_attach')->row()->q_file;
	}
	
	public function optqtype(){
		$this->db->select('idqtype,qtype');
		$this->db->order_by('idqtype','asc');
		$q = $this->db->get('qtype');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idqtype']] = $row['qtype'];
			}
		}
    return $return;
	}
	
	public function optsub(){
		$this->db->select('idsubject,subject');
		$this->db->order_by('idsubject','asc');
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
	
	public function importdata($dtxl,$dtans){
		$totq = 0;
		$totans=0;
		$faileduser = '';
		foreach ($dtxl as $key=>$val) {
            //check max id question
			$val['idq'] = ($this->getmaxidq())+1;
            $cins = $this->db->insert('question', $val);
            if (($cins)) {
				//insert all answers
				foreach($dtans['answer'][$key] as $k=>$v){
				$qans['idq'] = $val['idq'];
				$qans['answer'] = $v;
				$qans['key_ans'] = $dtans['key'][$key][$k];
					if($v!=null){
					$rans = $this->db->insert('answer', $qans);
					($rans)? $totans++ : $failans[]=$v;
					}
				}
				$tot ++;
            } else {
				$failed[]=($key+1).mb_substr($val['question'],0,20,'utf-8');
			}
		}
		return array('success'=>$tot,'sucans'=>$totans,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed),'failans'=>implode('<br/>',$failans));
	}
	
	public function getmaxidq(){
		$this->db->select('MAX(idq) as q');
		return $this->db->get('question')->row()->q;
	}
	
	public function exportquest($dcolumn = null){
		$this->db->select($dcolumn);
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->join('subject','subject.idsubject=question.idsubject','left');
		$this->db->order_by('subject','asc');
		$this->db->order_by('idq','asc');
		$this->db->order_by('q_group','asc');
		$this->db->order_by('q_bundle','asc');
		return $this->db->get('question')->result_array();
	}


	//======================== member section ====================
	public function getalluniqiebundlebyidsub($idsubject,$code){
		$this->db->distinct();
		$this->db->select('q_bundle');
		$this->db->where('idsubject',$idsubject);
		$this->db->where('q_group',$code);
		return $this->db->get('question')->result_array();
	}

	public function populatequestbysubbundle($column = null, $subject,$bundle,$code){
		$this->db->select($column);
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->join('subject','subject.idsubject=question.idsubject','left');
		$this->db->where('question.idsubject',$subject);
		$this->db->where('q_bundle',$bundle);
		$this->db->where('q_group',$code);
		$this->db->order_by('qtype','asc');
		$q = $this->db->get('question');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}

	public function pickquest($id){
		$this->db->select($column);
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->where('question.idsubject',$id);
		$this->db->order_by('idsub','asc');
		$this->db->order_by('qtype','asc');
		$this->db->order_by('q_group','asc');
		$q = $this->db->get('question');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}

	public function getmyquestdetail($col,$id){
		$this->db->select($col);
		$this->db->join('subject','subject.idsubject=question.idsubject','left');
		$this->db->join('qtype','qtype.idqtype=question.idqtype','left');
		$this->db->where('question.idq',$id);
		return ($this->db->get('question')->result());
	}

	public function insertqa($data){
		$this->db->insert('resultqa',$data);
	}

	public function checkgeneratedanswer($q,$idresult){
		$this->db->select('rand_answer');
		$this->db->where('idresult',$idresult);
		$this->db->where('idq',$q);
		return $this->db->get('resultqa')->num_rows();
	}

	public function getrandanswer($idq,$idresult){
		$this->db->select('rand_answer');
		$this->db->where('idresult',$idresult);
		$this->db->where('idq',$idq);
		return $this->db->get('resultqa')->row()->rand_answer;
	}

	public function getpickedanswer($idq,$idresult){
		$this->db->select('rpickanswer');
		$this->db->where('idresult',$idresult);
		$this->db->where('idq',$idq);
		return $this->db->get('resultqa')->row()->rpickanswer;
	}

	public function checkcorrectanswer($idans){
		$this->db->select('key_ans');
		$this->db->where('idans',$idans);
		return $this->db->get('answer')->row()->key_ans;
	}

	public function getmarkanswer($idq,$idres){
		$this->db->select('rtrue');
		$this->db->where('idresult',$idres);
		$this->db->where('idq',$idq);
		return $this->db->get('resultqa')->row()->rtrue;
	}

	public function updateqa($data,$idres,$idq){
		$this->db->where('idresult',$idres);
		$this->db->where('idq',$idq);
		return $this->db->update('resultqa',$data);
	}
}