<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mpay extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datapay($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
					if (($f=='period')){
					$this->db->like('DATE_FORMAT(tdate,"%Y")',$v);
					} else if(($f=='uname') and ($v!='')){
						$this->db->like('a.uname',$v);			
					} else if(($f=='unim') and ($v!='')){
						$this->db->like('a.unim',$v);			
					}else if(($f=='ulunas') and ($v!='')){
						$this->db->like('a.ulunas',$v);			
					}else if(($f=='pic') and ($v!='')){
						$this->db->like('b.uname',$v);			
					}else if(($f=='idjnstrans') and ($v!='')){
						$this->db->like('transaksi.idjnstrans',$v);			
					}else{
						$this->db->like($f,$v);			
					}
				}
			}
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
		$this->db->join('jns_trans as c','c.idjnstrans=transaksi.idjnstrans','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uallow','1');
		$this->db->order_by('tdate','desc');
		$q = $this->db->get('transaksi');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countpay($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(tdate,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else if(($f=='ulunas') and ($v!='')){
					$this->db->like('a.ulunas',$v);			
				}else if(($f=='pic') and ($v!='')){
					$this->db->like('b.uname',$v);			
				}else if(($f=='idjnstrans') and ($v!='')){
					$this->db->like('transaksi.idjnstrans',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','transaksi.uuser=a.uuser','left');
		$this->db->join('user as b','transaksi.use_uuser=b.uuser','left');
		$this->db->join('jns_trans as c','c.idjnstrans=transaksi.idjnstrans','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uallow','1');
		return $this->db->count_all_results("transaksi");

	}
	
	public function detailpay($col,$id){
	$this->db->select($col);
	$this->db->join('user as a','a.uuser=transaksi.uuser','left');
	$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
	$this->db->join('jns_trans','transaksi.idjnstrans=jns_trans.idjnstrans','left');
	$this->db->where('idtrans',$id);
	return $this->db->get('transaksi')->result_array();
	}
	
	public function savetransaction($fdata = null){
		return $this->db->insert('transaksi',$fdata);
	}
	
	public function savepayment($fdata = null){
		return $this->db->insert('transaksi',$fdata);
	}
	
	public function updatetransbyidpay($fid= null){
		$arr = array('ttapprove'=>'0', 'ttdateapp'=>date('Y-m-d H:i:s'));
		$this->db->where('idtrans',$fid);
		return $this->db->update('ttransfer',$arr);
	}
	
	public function optjtrans(){
		
		$this->db->select('idjnstrans,transname');
		$this->db->order_by('idjnstrans');
		$q = $this->db->get('jns_trans');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idjnstrans']] = $row['transname'];
			}
		}
    return $return;
	}
	
	public function optalluser(){
		$this->db->select('DATE_FORMAT(ucreated,"%Y") as dt,uuser,uname,uemail');
		$this->db->where('idrole','3');
		$this->db->where('uallow','1');
		$this->db->order_by('uuser');
		$q = $this->db->get('user');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['uuser']] = ''.$row['dt'].' ~ '.$row['uname'].' ('.$row['uuser'].' - '.$row['uemail'].')';
			}
		}
    return $return;
	}
	
	public function importdata($dtxl){
		$tot = 0;
		$faileduser = '';
		foreach ($dtxl as $key=>$val) {
            $val['tdate'] = DATE('Y-m-d H:i:s');
            //check duplication row (username)
			$cno = $this->Mpay->checknotrans($val['tnotrans']);                        
            if (($cno==0)) {
                $this->db->insert('transaksi', $val);
				$tot ++;
            } else {
				$faileduser[]=($key+1).'. '.$val['uuser'].' - '.$val['tpaid'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$faileduser));
	}
	
	public function exportpay($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(tdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(tdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
		$this->db->join('jns_trans as c','c.idjnstrans=transaksi.idjnstrans','left');
		$this->db->where('a.idrole','3');
		$this->db->order_by('tdate','desc');
		return $this->db->get('transaksi')->result_array();
	}
	
	public function getidpayment($id = null){
		$this->db->select('id_payment');
		$this->db->where('id_data',$id);
		$r = $this->db->get('payment');
		foreach($r->result_array() as $t){
		$ret = $t['id_payment'];}
		
		return $ret;
	}
	
	public function checkpay($id = null){
		$this->db->where('id_data',$id);
		$r = $this->db->get('payment');
		return $r->num_rows();
	}
	
	public function gettransaction($id= null){
		
		$this->db->where('id_transaksi',$id);	
		return $this->db->get('transaksi')->result_array();	
	}
	
	public function updatepay($data,$id){
		$this->db->where('idtrans',$id);
		return $this->db->update('transaksi',$data);
	}
	
	public function deletepay($id){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('idtrans',$id);
		$r = $this->db->delete('transaksi');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	public function totalmoney(){
		$y= $this->Msetting->getset('period');
		$this->db->where('DATE_FORMAT(tdate,"%Y")',$y);
		$this->db->select_sum('tpaid');
		return $this->db->get('transaksi')->row()->tpaid;
		
	}
	
	public function getlatestpay(){
		$this->db->select('tnotrans,a.uname,a.unim,tpaid,tdate');
		$this->db->from('transaksi');
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
		$this->db->order_by('tdate','desc');
		$this->db->limit(5,0);
		return $this->db->get()->result_array();
	}
	
	public function fulldetailpay($id=null){
		$this->db->select("idtrans,tdate,tnotrans,a.uuser as us,a.uname as mname,a.unim,a.ulunas,a.uemail,transname,tnomi,tpaid,tchange,b.uname as rname,valid_to,(select sum(tpaid) from transaksi where uuser=us) as 'totpaid',(select count(idtrans) from transaksi where uuser=us) as 'tottrans'");
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
		$this->db->join('jns_trans','transaksi.idjnstrans=jns_trans.idjnstrans','left');
		$this->db->where('a.uuser',$id);
		return $this->db->get('transaksi')->result_array();
	}
	
	public function checkfullpaid($us){
		$this->db->select('sum(tpaid) as totpaid');
		$this->db->where('a.uuser',$us);
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		return $this->db->get('transaksi')->row()->totpaid;
	
	}
	
	public function updatefullpaid($us,$val){
		$this->db->where('uuser',$us);
		return $this->db->update('user',array('ulunas'=>$val));
	}
	
	public function getpaycode($id=null){
		$this->db->select('upaycode');
		$this->db->where('uuser',$id);
		return $this->db->get('user')->row()->upaycode;
	}
	
	public function checknotrans($no){
		$this->db->select('tnotrans');
		$this->db->where('tnotrans',$no);
		return $this->db->count_all_results('transaksi');
	}
	
	public function getnotrans($id){
		$this->db->select('tnotrans');
		$this->db->where('idtrans',$id);
		return $this->db->get('transaksi')->row()->tnotrans;
	}
	
	public function getusertrans($id){
		$this->db->select('uuser');
		$this->db->where('idtrans',$id);
		return $this->db->get('transaksi')->row()->uuser;
	}
	
	public function getalluser(){
		$this->db->select('uuser');
		$this->db->where('idrole','3');
		$this->db->where('uallow','1');
		$this->db->where('idjk<>',null);
		$this->db->where('idfac<>',null);
		$this->db->where('ustatus<>',null);
		return $this->db->get('user')->result_array();
	}

	public function myinvoice($col,$id){
	$this->db->select($col);
	$this->db->join('user as a','a.uuser=transaksi.uuser','left');
	$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
	$this->db->join('jns_trans','transaksi.idjnstrans=jns_trans.idjnstrans','left');
	$this->db->where('tnotrans',$id);
	return $this->db->get('transaksi')->result_array();
	}

	public function datamypay($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
					if (($f=='period')){
					$this->db->like('DATE_FORMAT(tdate,"%Y")',$v);
					} else if(($f=='uname') and ($v!='')){
						$this->db->like('a.uname',$v);			
					} else if(($f=='unim') and ($v!='')){
						$this->db->like('a.unim',$v);			
					}else if(($f=='ulunas') and ($v!='')){
						$this->db->like('a.ulunas',$v);			
					}else if(($f=='pic') and ($v!='')){
						$this->db->like('b.uname',$v);			
					}else if(($f=='idjnstrans') and ($v!='')){
						$this->db->like('transaksi.idjnstrans',$v);			
					}else{
						$this->db->like($f,$v);			
					}
				}
			}
		$this->db->join('user as a','a.uuser=transaksi.uuser','left');
		$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
		$this->db->join('jns_trans as c','c.idjnstrans=transaksi.idjnstrans','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uuser',$this->session->userdata('user'));
		$this->db->where('a.uallow','1');
		$this->db->order_by('tdate','desc');
		$q = $this->db->get('transaksi');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countmypay($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(tdate,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else if(($f=='ulunas') and ($v!='')){
					$this->db->like('a.ulunas',$v);			
				}else if(($f=='pic') and ($v!='')){
					$this->db->like('b.uname',$v);			
				}else if(($f=='idjnstrans') and ($v!='')){
					$this->db->like('transaksi.idjnstrans',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','transaksi.uuser=a.uuser','left');
		$this->db->join('user as b','transaksi.use_uuser=b.uuser','left');
		$this->db->join('jns_trans as c','c.idjnstrans=transaksi.idjnstrans','left');
		$this->db->where('a.idrole','3');
		$this->db->where('a.uuser',$this->session->userdata('user'));
		$this->db->where('a.uallow','1');
		return $this->db->count_all_results("transaksi");

	}

	public function mydetailpay($col,$id,$user){
	$this->db->select($col);
	$this->db->join('user as a','a.uuser=transaksi.uuser','left');
	$this->db->join('user as b','b.uuser=transaksi.use_uuser','left');
	$this->db->join('jns_trans','transaksi.idjnstrans=jns_trans.idjnstrans','left');
	$this->db->where('tnotrans',$id);
	$this->db->where('a.uuser',$user);
	return $this->db->get('transaksi')->result_array();
	}

}