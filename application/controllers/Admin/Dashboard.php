<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','form_validation','Converttime','Convertmoney'));
		$this->load->helper(array('form','breadcrumb'));
		
		$this->load->model(array('Mlogin','Mchart','Mpds','Mpay'));
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
		$data['regfac'] = json_encode($this->Mchart->getregfac());
		$data['userol'] = json_encode($this->Mchart->getuseronline());
		
		//catch latest data
		$dpds= $this->Mpds->getlatestpds();
		$dpay= $this->Mpay->getlatestpay();
		$donline= $this->Mlogin->getlatestol();
		
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
		
		$header = ['Name','NIM','Payment Code','Amount Paid','Paid Time'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-strip">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['dpay']= $this->table->generate($dpay);
		
		$this->table->clear();
		
		$header = ['User','Name','Last IP','Role Access','Last login'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-strip">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['donline']= $this->table->generate($donline);
		
		
		$data['title']="Dashboard";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/dashboard', $data, TRUE);
		$this->load->view ('template/main', $data);				
	}
	
	
}
