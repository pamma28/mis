<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Matcl extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->helper('string');
    }
	
	public function dataatcl($column = null, $per_page = null, $page = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->where('article.idcat',$v);
				} else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','article.uuser=user.uuser','left');
		$this->db->join('cat_artcle','article.idcat=cat_artcle.idcat','left');
		$this->db->order_by('a_date','desc');
		$q = $this->db->get('article');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countatcl($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='idcat')){
				$this->db->like('article.idcat',$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		$this->db->join('user','article.uuser=user.uuser','left');
		$this->db->join('cat_artcle','article.idcat=cat_artcle.idcat','left');
		return $this->db->count_all_results("article");

	}

	public function detailatcl($col,$id){
	$this->db->select($col);
	$this->db->where('idarticle',$id);
	$this->db->join('user','article.uuser=user.uuser','left');
	$this->db->join('cat_artcle','article.idcat=cat_artcle.idcat','left');
	return $this->db->get('article')->result_array();		
	}
	
	public function deleteatcl($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idarticle',$id);
			$r = $this->db->delete('article');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function saveatcl($fdata = null){
		return $this->db->insert('article',$fdata);
	}
	
	public function updateatcl($fdata = null,$id){
		$this->db->where('idarticle',$id);
		return $this->db->update('article',$fdata);
	}
	
	public function tmpimportdata($dtxl){
		$tot = 0;
		$failed = array();
		foreach ($dtxl as $key=>$val) {
              $r =  $this->db->insert('article', $val);
				
			if ($r)	{
			$tot ++;
            } else {
				$failed[]=($key+1).'. '.$val['a_title'];
			}
		}
		return array('success'=>$tot,'failed'=>(count($dtxl)-$tot),'faillist'=>implode("<br/> ",$failed));
	}
	
	public function exportatcl($dtstart = null,$dtend = null, $dcolumn = null){
		$this->db->select($dcolumn);
			if (($dtstart <> null) and ($dtend)<>null){
			$this->db->where('DATE_FORMAT(a_date,"%Y-%m-%d") >=',$dtstart);
			$this->db->where('DATE_FORMAT(a_date,"%Y-%m-%d") <=',$dtend);
			}
		$this->db->join('user','user.uuser=article.uuser','left');
		$this->db->join('cat_artcle','cat_artcle.idcat=article.idcat','left');
		$this->db->order_by('a_date','desc');
		return $this->db->get('article')->result_array();
	}
	
	public function datacat($column = null, $per_page = null, $page = null, $filter = null){
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
		$this->db->order_by('idcat','desc');
		$q = $this->db->get('cat_artcle');
		$qr = array();
		if ($q->num_rows() > 0){	
			$qr = $q->result_array();
			}
	return $qr;
	}
	
	public function countcat($filter = null){
		if ($filter != null){
			foreach($filter as $f=>$v){
				if (($f=='smstype')){
				$this->db->like('bcket','Type: '.$v);
				}else{
					$this->db->like($f,$v);			
				}
			}
		}
		return $this->db->count_all_results("cat_artcle");

	}

	public function getoptatclcat(){
		$this->db->select('idcat,category');
		$this->db->order_by('idcat');
		$q = $this->db->get('cat_artcle');
		$return = array();
		$return[''] = 'Choose Category';
		if($q->num_rows() > 0){
        foreach($q->result_array() as $row){
			 $return[$row['idcat']] = $row['category'];
			}
		}
    return $return;
	}
	
	public function savecat($fdata = null){
		return $this->db->insert('cat_artcle',$fdata);
	}
	
	public function updatecat($fdata = null,$id){
		$this->db->where('idcat',$id);
		return $this->db->update('cat_artcle',$fdata);
	}
	
	
	public function getcat(){
		$this->db->select('idcat,category');
		return $this->db->get('cat_artcle')->result_array();
	}
	
	public function detailcat($col,$id){
	$this->db->select($col);
	$this->db->where('idcat',$id);
	return $this->db->get('cat_artcle')->result_array();		
	}
	
	
	public function deletecat($id){
		$this->db->query('SET foreign_key_checks = 0');
			$this->db->where('idcat',$id);
			$r = $this->db->delete('cat_artcle');
		$this->db->query('SET foreign_key_checks = 1');
		return ($r);
	}
	
	public function incrementatcl($id){
		$this->db->where('idarticle',$id);
		$this->db->set('a_view', 'a_view+1', FALSE);
		return $this->db->update('article');
	}
	
}