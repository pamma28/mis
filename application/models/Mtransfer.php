<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mtransfer extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
		$this->load->model('Msetting');
		
    }
	
	
	public function datatransfer($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ttdate,"%Y")',$v);
				} else if(($f=='ttapprove') and ($v!='')){
					$this->db->like($f,'"'.$v.'"');			
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
		$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
		$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uallow','1');
		$this->db->order_by('ttdaterequest','desc');
		$q = $this->db->get('ttransfer');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function detailtransfer($col,$id){
	$this->db->select($col);
	$this->db->where('idttrans',$id);
	$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
	$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
	$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
	return $this->db->get('ttransfer')->result_array();
	}
	
	public function counttransfer($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ttdate,"%Y")',$v);
				} else if(($f=='ttapprove') and ($v!='')){
					$this->db->like($f,$v);			
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
		$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
		$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uallow','1');
		return $this->db->count_all_results("ttransfer");

	}
	
	public function updatetransfer($data=null,$id){
		$this->db->where('idttrans',$id);
		return $this->db->update('ttransfer',$data);
	}
	
	public function updateselected($dt,$val){
		$v=0;$x=0;
		foreach($dt as $t){
		$this->db->where('idttrans',$t);
		$r = $this->db->update('ttransfer',array('ttapprove'=>$val));
			if ($r){$v++;} else{$x++;}
		}
		$hsl=array(
			"v"=>$v,
			"x"=>$x
			);
		return $hsl;
	}
	
	public function getidtransbyid($id){
		$this->db->select('idtrans');
		$this->db->where('idttrans',$id);
		return $this->db->get('ttransfer')->row()->idtrans;
	}
	
	public function getidtransbyno($no){
		$this->db->select('idtrans');
		$this->db->where('tnotrans',$no);
		return $this->db->get('transaksi')->row()->idtrans;
	}
	
	public function getmemuser($id){
		$this->db->select('uuser');
		$this->db->where('idttrans',$id);
		return $this->db->get('ttransfer')->row()->uuser;
	}

	public function countmytransfer($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ttdate,"%Y")',$v);
				} else if(($f=='ttapprove') and ($v!='')){
					$this->db->like($f,$v);			
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
		$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uallow','1');
		$this->db->where('ttapprove IS NULL');
		return $this->db->count_all_results("ttransfer");

	}

	public function savetransfer($data){
		return $this->db->insert('ttransfer',$data);

	}

	public function mydetailtransfer($col,$id,$user){
	$this->db->select($col);
	$this->db->where('idttrans',$id);
	$this->db->where('a.uuser',$user);
	$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
	$this->db->join('jns_trans','transaksi.idjnstrans=jns_trans.idjnstrans','left');
	$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
	$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
	return $this->db->get('ttransfer')->result_array();
	}
	
	public function datamytransferdata($column = null, $per_page = null, $page = null, $filter = null){
	
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ttdate,"%Y")',$v);
				} else if(($f=='ttapprove') and ($v!='')){
					$this->db->like($f,'"'.$v.'"');			
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
		$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
		$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uuser',$this->session->userdata('user'));
		$this->db->where('a.uallow','1');
		$this->db->order_by('ttdaterequest','desc');
		$q = $this->db->get('ttransfer');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countmytransferdata($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ttdate,"%Y")',$v);
				} else if(($f=='ttapprove') and ($v!='')){
					$this->db->like($f,$v);			
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','ttransfer.uuser=a.uuser','left');
		$this->db->join('transaksi','ttransfer.idtrans=transaksi.idtrans','left');
		$this->db->join('user as b','ttransfer.use_uuser=b.uuser','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uuser',$this->session->userdata('user'));
		$this->db->where('a.uallow','1');
		return $this->db->count_all_results("ttransfer");

	}

	public function getoptbank(){
		$arran = explode(',', $this->Msetting->getset('an_atm'));
		$arrno = explode(',', $this->Msetting->getset('no_atm'));
		$arrjb = explode(',', $this->Msetting->getset('jns_bank'));
		$return = array();
		$return[''] = 'Choose Bank';
		if(count($arran) > 0){
        foreach($arran as $k => $v){
            $return[$arrjb[$k]] = '(<b>'.$arrjb[$k].' - '.$arrno[$k].'</b>) '.$arran[$k];
			}
		}
    return $return;
	}

	public function gettotaltransfernotconfirm(){
		$this->db->select('idttrans');
		$this->db->where('ttapprove',null);
		return $this->db->count_all_results("ttransfer");
	}
}