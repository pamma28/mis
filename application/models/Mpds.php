<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mpds extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	public function getlatestpds(){
		$this->db->select('uname,unim,jk.jkname,uhp,ucreated');
		$this->db->join('jk','user.idjk=jk.idjk','left');
		$this->db->where('idrole','3');
		$this->db->where('ustatus<>',null);
		$this->db->where('user.idjk<>',null);
		$this->db->where('user.idfac<>',null);
		$this->db->limit(5,0); //5 latest
		$this->db->order_by('ucreated','desc');
		return $this->db->get('user')->result_array();		
	}
	
	public function datapds($column = null, $per_page = null, $page = null, $filter = null){
	
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
		$this->db->join('fac','user.idfac=fac.idfac','left');
		$this->db->where('idrole','3');
		$this->db->where('ustatus<>',null);
		$this->db->where('user.idjk<>',null);
		$this->db->where('user.idfac<>',null);
		$this->db->where('user.uallow','1');
		$this->db->order_by('ucreated','desc');
		$q = $this->db->get('user');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function detailpds($col,$id){
	$this->db->select($col);
	$this->db->where('uuser',$id);
	$this->db->join('fac','user.idfac=fac.idfac','left');
	$this->db->join('jk','user.idjk=jk.idjk','left');
	return $this->db->get('user')->result_array();
	}
	
	public function countpds($filter = null){
	
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
		$this->db->where('idrole','3');
		$this->db->where('ustatus<>',null);
		$this->db->where('user.idjk<>',null);
		$this->db->where('user.idfac<>',null);
		$this->db->where('user.uallow','1');
		return $this->db->count_all_results("user");

	}
	
	public function totalpds(){
		$y= $this->Msetting->getset('period');
		$this->db->where('DATE_FORMAT(ucreated,"%Y")',$y);
		$this->db->where('idrole','3');
		$this->db->where('ustatus<>',null);
		$this->db->where('user.idjk<>',null);
		$this->db->where('user.idfac<>',null);
		return $this->db->count_all_results('user');
		
	}
	
	public function totalfullpay(){
		$y= $this->Msetting->getset('period');
		$this->db->where('DATE_FORMAT(ucreated,"%Y")',$y);
		$this->db->where('idrole','3');
		$this->db->where('ulunas',true);
		$this->db->where('ustatus<>',null);
		$this->db->where('user.idjk<>',null);
		$this->db->where('user.idfac<>',null);
		return $this->db->count_all_results('user');
		
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
	
	public function optjk(){
		$this->db->select('idjk,jkname');
		$this->db->order_by('idjk','asc');
		$q = $this->db->get('jk');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idjk']] = $row['jkname'];
			}
		}
    return $return;
	}
	
	public function optfac(){
		$this->db->select('idfac,fname');
		$this->db->order_by('fname','asc');
		$q = $this->db->get('fac');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idfac']] = $row['fname'];
			}
		}
    return $return;
	}
	
	public function updatepds($data=null,$id){
		$this->db->where('uuser',$id);
		return $this->db->update('user',$data);
	}
	
	public function addpds($data){
		return $this->db->insert('user',$data);
	}
	
	public function deletepds($fid){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('uuser',$fid);
		$r = $this->db->delete('user');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function getallfac(){
		return $this->db->get('fac')->result_array();
	}
	
	public function getalljk(){
		return $this->db->get('jk')->result_array();
	}
	
	public function detailuser($id=null){
		$this->db->select('uuser,uname,uemail,uhp');
		$this->db->where('uuser',$id);
		return $this->db->get('user')->row();
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
}