<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Msetting extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	
	public function fullset(){
		$q = $this->db->get('setting');
		return $q->result_array();
	}	
	
	public function allset(){
		$q = $this->db->get('setting');
		foreach($q->result_array() as $t){
		$r[$t['setname']] = $t['setval'];
		}
		return $r;
	}
	
	public function checkexist($val){
		$this->db->select('setval');
		$this->db->where('setname',$val);
		$r = ($this->db->count_all_results('setting')>0) ? true: false;
		return $r;
	}

	public function addsetting($key, $val){
		$this->db->insert('setting',array('setname'=>$key,'setval'=>$val,'uuser'=>$this->session->userdata('user')));
	} 

	public function getset($dt){
		$this->db->select('setval');
		$this->db->where('setname',$dt);
		return $this->db->get('setting')->row()->setval;
	}
	
	public function savesetting($qset){
		foreach($qset as $key => $value){
			$this->db->update('setting',array('setval' =>$value,'uuser'=>$this->session->userdata('user')),"setname = '$key'");
		}
	}

	public function updatesetting($name,$value){
		$this->db->where('setname',$name);
		return $this->db->update('setting',array('setval' =>$value,'uuser'=>$this->session->userdata('user')));
	}
}