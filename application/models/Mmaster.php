<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mmaster extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datafac($column = null, $per_page = null, $page = null,$col = null, $filter = null){
		$column[]='id_fakultas';//PRIMARY KEY
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if (($col != null) AND ($filter != null)){$this->db->like($col,$filter);}
		$this->db->order_by('nama_fakultas');
		$q = $this->db->get('fakultas');
		$qr = null;
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function full($idpds = null){
		$col = ['id_data','Date_pds','full_name','nim','fakultas.id_fakultas','fakultas.nama_fakultas','socmed','jk','tempat_lahir','tgl_lahir','no_hp','mail','bbm','alamat_now','alamat_home','code_pay'];
		$this->db->select($col);
		$this->db->from('pds');
		$this->db->join('fakultas','fakultas.id_fakultas=pds.id_fakultas','left');
		$this->db->where("id_data = '$idpds'");
		$q = $this->db->get();
		$qr = $q->result_array();
		return $qr;
	}
	
	public function countfac($col = null, $filter = null){
	
		if (($col != null) AND ($filter != null)){
		$this->db->like($col,$filter);}
		return $this->db->count_all_results("fakultas");

	}
	
	public function savepds($fdata = null, $fid = null){
		
		$this->db->where('id_fakultas',$fid);
		
		return $this->db->update('fakultas',$fdata);
	
	}
	
	public function editfac($fid=null){
		$this->db->select('id_fakultas,nama_fakultas');
		$this->db->where('id_fakultas',$fid);
		$r = $this->db->get('fakultas')->row_array();
		return $r;
		
	}
	
	public function deletepds($fid= null){
	
		$this->db->where('id_fakultas',$fid);
		
		return $this->db->delete('fakultas');
	}
	
}