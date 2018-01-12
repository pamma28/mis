<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mcerti extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datadesign($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(desdateup,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);			
				}else if(($f=='ulunas') and ($v!='')){
					$this->db->like('a.ulunas',$v);			
				}else if(($f=='pic') and ($v!='')){
					$this->db->like('b.uname',$v);			
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','certidesign.uuser=a.uuser','left');
		$this->db->join('user as b','certidesign.use_uuser=b.uuser','left');
		$this->db->order_by('desdateup','desc');
		$q = $this->db->get('certidesign');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datacerti($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);					
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','user.uuser=certificate.uuser','left');
		$this->db->join('level','level.idlevel=user.idlevel','left');
		$this->db->order_by('certidate','desc');
		$q = $this->db->get('certificate');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countdesign($filter = null){
	
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(desdateup,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);	
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user as a','certidesign.uuser=a.uuser','left');
		$this->db->join('user as b','certidesign.use_uuser=b.uuser','left');
		return $this->db->count_all_results("certidesign");

	}
	
	public function countcerti($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(ucreated,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);					
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','certificate.uuser=user.uuser','left');
		$this->db->join('level','level.idlevel=user.idlevel','left');
		return $this->db->count_all_results("certificate");

	}
	
	public function detaildesign($col,$id){
	$this->db->select($col);
	$this->db->where('iddes',$id);
	return $this->db->get('certidesign')->result_array();
	}
	
	public function detailcerti($col,$id){
	$this->db->select($col);
	$this->db->where('certificate.idcerti',$id);
	$this->db->join('user','certificate.uuser=user.uuser','left');
	$this->db->join('level','level.idlevel=user.idlevel','left');
	return $this->db->get('certificate')->result_array();
	}
	
	public function savecerti($fdata = null){
		return $this->db->insert('certificate',$fdata);
	}
	
	public function savedesign($fdata = null){
		$this->db->insert('certidesign',$fdata);
		return $this->db->insert_id(); 
	}
	
	public function deletecerti($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->select('uuser');
			$this->db->where('idcerti',$id);
			$user = $this->db->get('certificate')->row()->uuser;
		
			$this->db->where('uuser',$user);
			$us = $this->db->update('user',array('idcerti'=>null));
		
			$this->db->where('idcerti',$id);
			$r = $this->db->delete('certificate');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function getoptlevel(){
		
		$this->db->select('idlevel,lvlname');
		$this->db->order_by('idlevel');
		$q = $this->db->get('level');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idlevel']] = $row['lvlname'];
			}
		}
    return $return;
	}
	
	public function getoptpage(){
		
		$this->db->select('idtmplte,tmpname');
		$this->db->order_by('idtmplte');
		$q = $this->db->get('template');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idtmplte']] = $row['tmpname'];
			}
		}
    return $return;
	}
	
	public function getoptuser(){
		$this->db->select('uuser,uname,unim,lvlname');
		$this->db->where('idrole','3');
		$this->db->where('uallow','1');
		$this->db->where('user.idlevel<>',null);
		$this->db->where('idcerti',null);
		$this->db->order_by('uname','asc');
		$this->db->order_by('lvlname','asc');
		$this->db->join('level','user.idlevel=level.idlevel','left');
		$q = $this->db->get('user');
		$return = array();
		$return[''] = 'Please Select';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['uuser']] = ''.$row['uname'].' - '.$row['lvlname'].' ('.$row['unim'].')';
			}
		}
    return $return;
	}
	
	public function importdata($dtxl){
		$tot = 0;
		$failed = '';
		foreach ($dtxl as $key=>$val) {
            $val['certidate'] = DATE('Y-m-d H:i:s');
            //check duplication row (username)
			$cno = $this->Mcerti->checknocerti($val['nocerti']);                        
            if (($cno==0)) {
                $this->db->insert('certificate', $val);
				
				$idcerti = $this->Mcerti->findidcerti($val['uuser']);
				$this->Mcerti->updatecertiuser($idcerti,$val['uuser']);
				$tot ++;
            } else {
				$failed[]=($key+1).'. '.$val['uuser'].' - '.$val['nocerti'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed));
	}
	
	public function exportcerti($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(certidate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(certidate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=certificate.uuser','left');
		$this->db->join('level','level.idlevel=user.idlevel','left');
		$this->db->order_by('certidate','desc');
		return $this->db->get('certificate')->result_array();
	}
	
	public function getidpayment($id = null){
		$this->db->select('id_payment');
		$this->db->where('id_data',$id);
		$r = $this->db->get('payment');
		foreach($r->result_array() as $t){
		$ret = $t['id_payment'];}
		
		return $ret;
	}
	
	public function maxnocerti($filter = null){
		$this->db->select('nocerti');
		$this->db->like('nocerti',$filter);
		$r = $this->db->get('certificate')->result_array();
		$highest=0;
		foreach($r as $val){
			$temp = explode('/',$val['nocerti']);
			($temp[0] > $highest) ? $highest = $temp[0]:null;
		}
		return $highest;
	}
	
	public function findidcerti($user){
		$this->db->select('idcerti');	
		$this->db->where('uuser',$user);	
		return $this->db->get('certificate')->row()->idcerti;	
	}
	
	public function updatecerti($data,$id){
		$this->db->where('idcerti',$id);
		return $this->db->update('certificate',$data);
	}
	
	public function updatecertiuser($data,$user){
		$this->db->where('uuser',$user);
		return $this->db->update('user',array('idcerti'=>$data));
	}
	
	public function updatedefault($id){
		$this->db->where('cerdefault','1');
		$this->db->update('certidesign',array('cerdefault'=>'0'));
		$this->db->where('iddes',$id);
		return $this->db->update('certidesign',array('cerdefault'=>'1'));
	}
	
	public function deletedes($id){
		$this->db->query('SET foreign_key_checks = 0');
		$this->db->where('iddes',$id);
		$r = $this->db->delete('certidesign');
		$this->db->query('SET foreign_key_checks = 1');
		return $r;
	}
	
	
	public function checknocerti($no){
		$this->db->select('nocerti');
		$this->db->where('nocerti',$no);
		return $this->db->get('certificate')->num_rows();
	}
	
	public function updatedes($dt,$id){
		$this->db->where('iddes',$id);
		return $this->db->update('certidesign',$dt);
	}
	
	public function getnamedes($id){
		$this->db->select('desfile');
		$this->db->where('iddes',$id);
		return $this->db->get('certidesign')->row()->desfile;
	}
	
	public function getDefault($id){
		$this->db->select('cerdefault');
		$this->db->where('iddes',$id);
		return $this->db->get('certidesign')->row()->cerdefault;
	}

	public function fileDefault(){
		$this->db->select('desfile');
		$this->db->where('cerdefault',true);
		return $this->db->get('certidesign')->row()->desfile;
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
}