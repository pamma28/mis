<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mtmp extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function datatmp($column = null, $per_page = null, $page = null, $filter = null){
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
		$this->db->where('tmptype','CONTENT');
		$q = $this->db->get('template');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function counttmp($filter = null){
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
		$this->db->where('tmptype','CONTENT');
		return $this->db->count_all_results("template");

	}

	public function detailtmp($col,$id){
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
	
	public function exporttmp($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(tmpdate,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(tmpdate,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=template.uuser','left');
		$this->db->where('tmptype','CONTENT');
		$this->db->order_by('tmpdate','desc');
		return $this->db->get('template')->result_array();
	}
	
	public function getopttmp(){
		$this->db->select('idtmplte,tmpname,tmpcontent');
		$this->db->order_by('idtmplte');
		$q = $this->db->get('template');
		$return = array();
		$return[''] = 'Choose to use template';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
        	$tmpcontent = strip_tags(htmlspecialchars_decode($row['tmpcontent']));
			(strlen($tmpcontent)>50) ? $content = mb_substr($tmpcontent,0,50).'...': $content = $tmpcontent;
			
            $return[$row['idtmplte']] = '('.$row['tmpname'].') '.$content;
			}
		}
    return $return;
	}

	public function getopttmpsms(){
		$this->db->select('idtmplte,tmpname,tmpcontent');
		$this->db->where('tmptype','SMS');
		$this->db->order_by('idtmplte');
		$q = $this->db->get('template');
		$return = array();
		$return[''] = 'Choose to use template';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
        	$tmpcontent = $row['tmpcontent'];
			(strlen($tmpcontent)>50) ? $content = mb_substr($tmpcontent,0,50).'...': $content = $tmpcontent;
			
            $return[$row['idtmplte']] = '('.$row['tmpname'].') '.$content;
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