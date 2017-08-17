<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mregister extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	
	public function getcode(){
		do {
			$code = random_string('alpha',3).random_string('nozero',3);
			}
		while ($this->checkcode($code)== false);
		
		return $code;
	}
	
	private function checkcode($code){
		$this->db->select('upaycode');
        $this->db->from('user');
        $this->db->where("upaycode='$code'");
        $q = $this->db->get();
		$hasil = $q->num_rows();
		if ($hasil > 0) {
			return false;
		} else {
			return true;
		}
	
	}
	
	public function optfac(){
		$this->db->select();
		$this->db->order_by('idfac');
		$q = $this->db->get('fac');
		$return = array();
		$return[''] = 'please select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idfac']] = $row['fname'];
			}
		}
    return $return;
	}
	
	public function savepds($qpds){
		$r = $this->db->insert('pds',$qpds);
		return $r;
	}
	
}