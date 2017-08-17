<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mdb extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	
	
	public function datadb($column = null, $per_page = null, $page = null,$col = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if (($col != null) AND ($filter != null)){$this->db->like($col,$filter);}
		$this->db->order_by('table_name','asc');
		$this->db->where('TABLE_SCHEMA',$this->db->database);
		$q = $this->db->get('INFORMATION_SCHEMA.TABLES');
		$qr = $q->result_array();	
		return $qr;
	}
	
	public function countdb($col = null, $filter = null){
	
		$this->db->where('TABLE_SCHEMA',$this->db->database);
		if (($col != null) AND ($filter != null)){
		$this->db->like($col,$filter);}
		return $this->db->count_all_results("INFORMATION_SCHEMA.TABLES");

	}
	
	public function restoredb($dtdb){
		//var store result
		$tot = 0;
		$fail = 0;
		$this->db->query('SET foreign_key_checks = 0');
		foreach ($dtdb as $query) {
           $res = $this->db->query($query);
		   if ($res){
		   $tot++;} else {
		   $fail++;}
		}
		$this->db->query('SET foreign_key_checks = 1');
		return ($tot.' Query(s) excuted successfully with '.$fail.' Query(s) failed.');
	}
	
	public function exportlogin($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('ucreated >=',$dtstart);
			$this->db->where('ucreated <=',$dtend);
			}
		$this->db->join('role','user.idrole=role.idrole','left');
		$this->db->order_by('ucreated','asc');
		return $this->db->get('user')->result_array();
	}

	public function deletedb($id){
	$this->db->query('SET FOREIGN_KEY_CHECKS = 0');
	$this->db->query('TRUNCATE '.$id);
	return $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
	}
	
	public function detaildb($col,$id){
	$this->db->select($col);
	return $this->db->get($id)->result_array();
	}
	
	public function updateacc($data=null,$id){
		$this->db->where('uuser',$id);
		return $this->db->update('user',$data);
	}
	
	public function addacc($data){
		return $this->db->insert('user',$data);
	}
	
	public function getrole(){
	$this->db->select('idrole,rolename');
	$q = $this->db->get('role');
	$return = array();
	$return[''] = 'Please select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idrole']] = $row['rolename'];
			}
		}
	return $return;
	}
	
	public function getallrole(){
	$this->db->select('idrole,rolename');
	return $this->db->get('role')->result_array();
	}
	
	public function checkmail($mail){
		$this->db->select('uemail');
		$this->db->where('uemail',$mail);
		$rt = $this->db->get('user')->num_rows();
		if ($rt>0){
		return 1;
		} else{
		return 0;
		}
	}
	
	public function checkuser($us){
		$this->db->select('uuser');
		$this->db->where('uuser',$us);
		$rt = $this->db->get('user')->num_rows();
		if ($rt>0){
		return 1;
		} else{
		return 0;
		}
	}
}