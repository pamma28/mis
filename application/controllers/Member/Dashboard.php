<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Mem_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney','Converttime','Gmail'));
		$this->load->helper(array('form','breadcrumb'));
		
		$this->load->model(array('Mlogin','Mchart','Mpds','Mpay','Msetting'));
    }

	public function index(){
		//============= data progress =========
		$arrprog = ['Registered','Completed Data','Completed Payment','Choosen Schedule','Done Test','Test Result','Received Certificate','Graduated'];

		$stat = explode(',', $this->Mlogin->getuserstatus($this->session->userdata('user')));
		$tmpprog=''; $style = 'primary'; $badge = 'fa-check'; $details ='Completed'; $bgpanel='';
		foreach($arrprog as $k=>$v){
			if (!in_array($v, $stat)) {
				$badge = 'fa-times'; 
				$style = 'danger';
				$details = 'Incomplete';
				$bgpanel = 'text-gray disabled';
			}
			$tmpprog .='<li class="timeline-item">
							<div class="timeline-badge '.$style.'"><i class="fa '.$badge.'"></i></div>
							<div class="timeline-panel '.$bgpanel.'">
								<div class="timeline-body">
									<div class="text-center"><b>'.($k+1).'. '.$v.'</b></div>
									<hr/>
									<small><i><span class="text text-'.$style.'"><span class="fa fa-info-circle"></span> Status: '.$details.'</span></i></small>
								</div>
							</div>
						</li>';
		}

		$data['arrprogress'] = $tmpprog;

		//========= template dashboard ========
		$this->load->model('Mtmp');
		$iddashtmp = $this->Msetting->getset('tmpdashmem');
		$arrtmp = $this->Mtmp->datatmp(array('tmpname','tmpcontent'),1,1,array('idtmplte'=>$iddashtmp));
		$data['tmptitle'] = $arrtmp[0]['tmpname'];
		$data['tmpcontent'] = htmlspecialchars_decode($arrtmp[0]['tmpcontent']);

		//============= populate pds ========
		$colpds = array('ucreated','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','ulunas');
		$tmppds = $this->Mpds->detailpds($colpds,$this->session->userdata('user'));
		$lunas = $tmppds[0]['ulunas'];
		//set row title
		$row = $this->returncolomn($colpds);
		unset($row[12]);
		//set table template
		$tmpl = array ( 'table_open'  => '<table class="table table-striped table-hover">',
					'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<td>',
                    'heading_cell_end'    => '</td>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>'

					);
		$this->table->set_template($tmpl);
		$a=0;
		foreach($row as $key)
		{
			$dtpds[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$tmppds[0][$colpds[$a]]
				);
			
			if (($key=='Birthdate')){
					$dtpds[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y',strtotime($tmppds[0][$colpds[$a]]))
						);
					}
			
			
			$a++;
		}
		$data['dtpds']= $this->table->generate($dtpds);


		//============= populate agendas ========
		$colpay=['tdate','tnotrans','transname','tpaid'];
		$headerpay = $this->returncolomn($colpay);
		$tmplpay = array ( 'table_open'  => '<table class="table table-hover">');
		$this->table->set_template($tmplpay);
		$this->table->set_heading($headerpay);
		$totpay = $this->Mpay->countmypay();
		$tmppay = $this->Mpay->datamypay($colpay,'all',1,null);
		$data['dtpay'] = $this->table->generate($tmppay);
		$data['lunas'] = ($lunas) ? 'Fully Paid':'Not Fully Paid Yet';
		$data['totpay'] = $this->convertmoney->convert($this->Mpay->datamypay('sum(tpaid) as totpay','all',1,null)[0]['totpay']);


		//============= populate phase ========
		$colpha=['registphase','paymentphase','schedulephase','certiphase'];
		$headerpha = $this->returncolomn($colpha);
		$tmplpha = array('table_open'=>'<table class="table table-bordered text-center bg-info">');
		$this->table->set_template($tmplpha);
		$this->table->set_heading($headerpha);
		foreach ($colpha as $k => $v) {
			$t = explode(' - ', $this->Msetting->getset($v));
			$phase = array();
			foreach ($t as $val) {
				$phase[] = '<span class="text-primary"><b>'.date('d-M-Y',strtotime(str_replace('/', '-',$val))).'</b></span>';
			}
			$vpha[$k] = implode(' <span class="text-info"><b><i>until</i></b></span> ', $phase);
		}
		$dtpha[] = $vpha;
		$data['dtphase'] = $this->table->generate($dtpha);



		//=============== Template ============
		$data['cssFiles'] = array(
							'h-timeline/h-timeline');
							
		$data['jsFiles'] = array(
							'amchart/amcharts','amchart/serial','amchart/themes/light');
		
		$data['title']="Dashboard";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/dashboard', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	 
	public function allnotification(){
		//===================== table handler =============
		$this->load->library("Notifications");
		$data["listdata"]=$this->notifications->getallmynotif();


		//=============== Template ============
		$data['jsFiles'] = array('selectpicker/select.min'
							);
		$data['cssFiles'] = array('selectpicker/select.min'
							);  
		// =============== view handler ============
		$data['title']="All Notification";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/mynotif', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	public function returncolomn($header) {
	$find=['ucreated','uname','jkname','idjk','ubplace','ubdate','unim','uemail','uhp','ufoto','ubbm','uaddrnow','uaddhome','tdate','tnotrans','transname','tpaid','registphase','paymentphase','schedulephase','certiphase'];
	$replace = ['Date Registered','Full Name','Gender','Gender','Birthplace','Birthdate','NIM','Email','Phone Number','Photo','Social Media','Current Address','Home Address','Transaction Date','Invoice Number','Transaction Type','Nominal Paid','Registration','Payment','Test Schedule','Certificate'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
}
