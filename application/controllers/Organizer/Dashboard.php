<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney','Converttime','Gmail'));
		$this->load->helper(array('form','breadcrumb'));
		
		$this->load->model(array('Mlogin','Mchart','Mpds','Mpay','Msetting'));
    }

	public function index(){
		//catch heading data
		$data['totpds']= $this->Mpds->totalpds();
		$data['totfullpay']= $this->Mpds->totalfullpay();
		$data['totmoney']=$this->convertmoney->convert($this->Mpay->totalmoney());
		$progress = ($data['totfullpay']/$this->Msetting->getset('quota'))*100;
		$data['progressreg']=$progress;
		$data['thisperiod']=$this->Msetting->getset('period');
		
		//catch chart data
		$data['regfac'] = json_encode($this->Mchart->getregfac_yearly());
		$data['yearbyyear'] = json_encode($this->Mchart->getregyear());
		
		//catch latest data
		$dpds= $this->Mpds->getlatestpds();
		$dpay= $this->Mpay->getlatestpay();
		$collogin = ['uuser','uname','ulastlog'];
		$donline= $this->Mlogin->getlatestol($collogin);
		
		//manipulate data
		foreach ($dpds as $key=>$value){
			$dpds[$key]['ucreated']= $this->converttime->time_elapsed_string($value['ucreated']);		
		}
		foreach ($dpay as $key=>$value){
			$dpay[$key]['tpaid']= $this->convertmoney->convert($value['tpaid'],'IDR');		
			$dpay[$key]['tdate']= $this->converttime->time_elapsed_string($value['tdate'],true);		
		}
		foreach ($donline as $key=>$value){
			$donline[$key]['ulastlog']= $this->converttime->time_elapsed_string($value['ulastlog'],true);		
		}
		
		//create table
		$header = ['Name','NIM','Gender','Phone','Registered'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-strip">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['dpds']= $this->table->generate($dpds);
		
		$this->table->clear();
		
		$header = ['Transaction ID','Name','NIM','Amount Paid','Paid Time'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-strip">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['dpay']= $this->table->generate($dpay);
		
		$this->table->clear();
		
		$header = ['Username','Name','Last login'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-strip">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['donline']= $this->table->generate($donline);
		
		
		//=============== Template ============
		$data['cssFiles'] = array(
							'wysi/wysi.min');
							
		$data['jsFiles'] = array(
							'wysi/wysi.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		
		
		
		$data['title']="Dashboard";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/dashboard', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	 

}
