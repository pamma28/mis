<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate extends Mem_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mcerti','Msetting'));
    }

	public function index(){
		//===================== check phase date =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$certiphase = explode(" - ",$this->Msetting->getset('certiphase'));
		$startcerti = strtotime(str_replace('/', '-', $certiphase[0]));
		$endcerti =  strtotime(str_replace('/', '-', $certiphase[1]));
		$today = strtotime(date("d-m-Y"));
		$data['date'] = (($today >= $startcerti) and ($today <= $endcerti)) ? true : false;
		$data['startdate'] = date('d-M-Y',$startcerti);
		$data['enddate'] = date('d-M-Y', $endcerti);
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['lvlname','nocerti','certidate','ctaken','clisten','cgrammar','cread','cwrite','cspeak'];
		$certi = $this->Mcerti->datamycerti($column,0,1)[0];
		unset($column[0],$column[1],$column[2],$column[3]);
		$retcolumn = $this->returncolomn($column);
		$header = ['<i class="fa fa-book"></i> Subject','<i class="fa fa-check"></i> Mark'];
		$tmpl = array ( 'table_open'  => '<table class="table table-striped table-hover" id="certilist">'
		 			);
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		
		//=============== data my certificate ==========
			foreach($retcolumn as $key=>$value){
				$tmp[] = array($value,$certi[$column[$key]]);
			}
		$data['mysche'] = $this->table->generate($tmp);
		$data['nocerti'] = $certi['nocerti'];
		$data['lvlname'] = $certi['lvlname'];
		$data['certidate'] = date("d M Y", strtotime($certi['certidate']));
		$data['cstatus'] = ($certi['ctaken']) ? 'Taken' : 'Available in HOS';
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'');  
		// =============== view handler ============
		$data['title']="My Certificate";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/certi/mycerti', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	public function previewmycertismall(){
		$col = ['certificate.idcerti'];
		$id = $this->Mcerti->datamycerti($col,0,1)[0]['idcerti'];
		$this->load->library('ImgGenerator');
		$this->imggenerator->imgcerti($id,15);
	}

	public function previewmycertireal(){
		$col = ['certificate.idcerti'];
		$id = $this->Mcerti->datamycerti($col,0,1)[0]['idcerti'];
		$this->load->library('ImgGenerator');
		$this->imggenerator->imgcerti($id,95);
	}
	
	public function preview(){
		//=============== Template ============
		$data['jsFiles'] = array(
							'elevatezoom/elevatezoom');
		$data['cssFiles'] = array(
							'');  
		// =============== view handler ============
		$data['title']="Preview Certificate";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/certi/previewcerti', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	public function returncolomn($header) {
	$find=['nocerti','certidate','cspeak','cread','clisten','cwrite','cgrammar'];
	$replace = ['Certificate Number','Issued Date','Speaking','Reading','Listening','Writing','Grammar'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
