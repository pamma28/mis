<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mbc extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function databcmail($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='mailtype')){
				$this->db->like('bcket','Type: '.$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->order_by('bcdate','desc');
		$this->db->where('bctype','Mail');
		$q = $this->db->get('broadcast');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function databcsms($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->order_by('bcdate','desc');
		$this->db->where('bctype','SMS');
		$q = $this->db->get('broadcast');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datatmpsms($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','template.uuser=user.uuser','left');
		$this->db->order_by('tmpdate','desc');
		$this->db->where('tmptype','SMS');
		$q = $this->db->get('template');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function datainmail($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(bcdate,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);					
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->order_by('bcdate','desc');
		$this->db->where('bctype','inmail');
		$q = $this->db->get('broadcast');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countbcmail($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='mailtype')){
				$this->db->like('bcket','Type: '.$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->where('bctype','Mail');
		return $this->db->count_all_results("broadcast");

	}
	
	public function countbcsms($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->where('bctype','Sms');
		return $this->db->count_all_results("broadcast");

	}
	
	public function counttmpsms($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','template.uuser=user.uuser','left');
		$this->db->where('tmptype','SMS');
		return $this->db->count_all_results("template");

	}
	
	public function countinmail($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='period')){
				$this->db->like('DATE_FORMAT(bcdate,"%Y")',$v);
				} else if(($f=='uname') and ($v!='')){
					$this->db->like('a.uname',$v);			
				} else if(($f=='unim') and ($v!='')){
					$this->db->like('a.unim',$v);					
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','broadcast.uuser=user.uuser','left');
		$this->db->where('bctype','inmail');
		return $this->db->count_all_results("broadcast");

	}
	
	public function detailbcmail($col,$id){
	$this->db->select($col);
	$this->db->where('idbcast',$id);
	$this->db->join('user','broadcast.uuser=user.uuser','left');
	return $this->db->get('broadcast')->result_array();
	}
	
	public function savebc($fdata = null){
		return $this->db->insert('broadcast',$fdata);
	}
	
	public function deletebc($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idbcast',$id);
			$r = $this->db->delete('broadcast');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function getoptlevel(){
		
		$this->db->select('idlevel,lvlname');
		$this->db->order_by('idlevel');
		$q = $this->db->get('level');
		$return = array();
		$return[''] = 'All Level';
		$return['0'] = 'No Level';
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
	
	public function getoptrole(){
		$this->db->select('idrole,rolename');
		$this->db->where('idrole<>','1');
		$this->db->order_by('idrole','asc');
		$q = $this->db->get('role');
		$return = array();
		$return[''] = 'All Role';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['idrole']] = ''.$row['rolename'];
			}
		}
    return $return;
	}
	
	public function getoptyear(){
		$this->db->distinct();
		$this->db->select('DATE_FORMAT(ucreated,"%Y") as dt');
		$this->db->where('idrole<>','1');
		$this->db->order_by('dt','asc');
		$q = $this->db->get('user');
		$return = array();
		$return[''] = 'All Year';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
            $return[$row['dt']] = ''.$row['dt'];
			}
		}
    return $return;
	}
	
	public function getrole($id){
		$this->db->select('rolename');
		$this->db->where('idrole',$id);
		return $this->db->get('role')->row()->rolename;
	}
	
	public function getlevel($id){
		$this->db->select('lvlname');
		$this->db->where('idlevel',$id);
		return $this->db->get('level')->row()->lvlname;
	}
	
	public function getcontact($r=null,$y=null,$s=null,$l=null){
		$this->db->select('uname as name,uemail as email,rolename as role,lvlabre as level,DATE_FORMAT(ucreated, "%Y") as year');
		$this->db->where('user.idrole <>','1');
		$this->db->order_by('ucreated','asc');
		$this->db->where('uallow',true);
		$this->db->join('role','user.idrole=role.idrole','left');
		$this->db->join('level','level.idlevel=user.idlevel','left');
		($r!=null) ? $this->db->where('user.idrole',$r):null;
		($y!=null) ? $this->db->where('DATE_FORMAT(ucreated,"%Y")',$y):null;
		($s!=null) ? $this->db->where('ulunas',$s):null;
		if ($l!=null) {
		($l=='0') ? $this->db->where('user.idlevel',null):$this->db->where('user.idlevel',$l);
		}
		return $this->db->get('user')->result_array();
	}
	
	public function getphone($r=null,$y=null,$s=null,$l=null){
		$this->db->select('uname as name,uhp as phone,rolename as role,lvlabre as level,DATE_FORMAT(ucreated, "%Y") as year');
		$this->db->where('user.idrole <>','1');
		$this->db->order_by('ucreated','asc');
		$this->db->where('uallow',true);
		$this->db->join('role','user.idrole=role.idrole','left');
		$this->db->join('level','level.idlevel=user.idlevel','left');
		($r!=null) ? $this->db->where('user.idrole',$r):null;
		($y!=null) ? $this->db->where('DATE_FORMAT(ucreated,"%Y")',$y):null;
		($s!=null) ? $this->db->where('ulunas',$s):null;
		if ($l!=null) {
		($l=='0') ? $this->db->where('user.idlevel',null):$this->db->where('user.idlevel',$l);
		}
		return $this->db->get('user')->result_array();
	}
	
	public function importdata($dtxl){
		$tot = 0;
		$failed = array();
		foreach ($dtxl as $key=>$val) {
              $r =  $this->db->insert('broadcast', $val);
				
			if ($r)	{
			$tot ++;
            } else {
				$failed[]=($key+1).'. '.$val['bcfrom'].' - '.$val['bctitle'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed));
	}
	
	public function exportbcmail($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(bcdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(bcdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=broadcast.uuser','left');
		$this->db->where('bctype','Mail');
		$this->db->order_by('bcdate','desc');
		return $this->db->get('broadcast')->result_array();
	}
	
	public function exportbcsms($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(bcdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(bcdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=broadcast.uuser','left');
		$this->db->where('bctype','SMS');
		$this->db->order_by('bcdate','desc');
		return $this->db->get('broadcast')->result_array();
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

//----------------------------- TEMPLATE FUNCTION ---------------------------
	public function detailtmpsms($col,$id){
	$this->db->select($col);
	$this->db->where('idtmplte',$id);
	$this->db->join('user','template.uuser=user.uuser','left');
	return $this->db->get('template')->result_array();		
	}
	
	public function deletetmp($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idtmplte',$id);
			$r = $this->db->delete('template');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function savetmp($fdata = null){
		return $this->db->insert('template',$fdata);
	}
	
	public function updatetmp($fdata = null,$id){
		$this->db->where('idtmplte',$id);
		return $this->db->update('template',$fdata);
	}
	
	public function tmpimportdata($dtxl){
		$tot = 0;
		$failed = array();
		foreach ($dtxl as $key=>$val) {
              $r =  $this->db->insert('template', $val);
				
			if ($r)	{
			$tot ++;
            } else {
				$failed[]=($key+1).'. '.$val['tmpname'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed));
	}
	
	public function exporttmpsms($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(tmpdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(tmpdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=template.uuser','left');
		$this->db->where('tmptype','SMS');
		$this->db->order_by('tmpdate','desc');
		return $this->db->get('template')->result_array();
	}
	
	public function getopttmp($var){
		$this->db->select('idtmplte,tmpname,tmpcontent');
		$this->db->where('tmptype',$var);
		$this->db->order_by('idtmplte');
		$q = $this->db->get('template');
		$return = array();
		$return[''] = 'Choose to use template';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
			(strlen($row['tmpcontent'])>50) ? $tmpcontent = mb_substr($row['tmpcontent'],0,50).'...': $tmpcontent = $row['tmpcontent'];
			
            $return[$row['idtmplte']] = '('.$row['tmpname'].') '.$tmpcontent;
			}
		}
    return $return;
	}
	
	public function gettmpdata($id){
		$this->db->select('tmpcontent');
		$this->db->where('idtmplte',$id);
		return $this->db->get('template')->row();
	}
	
}