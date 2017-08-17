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
		$arrprog = ['Registered','Complete Form','Complete Payment','Choose Schedule','Do Test','Test Result','Receive Certificate','Graduate'];

		$stat = $this->Mlogin->getuserstatus($this->session->userdata('user'));
		$tmpprog=''; $style = 'primary'; $badge = 'fa-check'; $details ='Completed'; $bgpanel='';
		foreach($arrprog as $k=>$v){
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
			if ($stat==$v) {
				$badge = 'fa-times'; 
				$style = 'danger';
				$details = 'Incomplete';
				$bgpanel = 'text-gray disabled';
			}
		}

		$data['arrprogress'] = $tmpprog;

		//========= template dashboard ========
		$this->load->model('Mtmp');
		$iddashtmp = $this->Msetting->getset('tmpdashmem');
		$arrtmp = $this->Mtmp->datatmp(array('tmpname','tmpcontent'),1,1,array('idtmplte'=>$iddashtmp));
		$data['tmptitle'] = $arrtmp[0]['tmpname'];
		$data['tmpcontent'] = htmlspecialchars_decode($arrtmp[0]['tmpcontent']);

		//============= populate articles ========
		$totatcl = $this->Msetting->getset('renderatcl');
		$this->load->model("Matcl");
		$tmpatcl = $this->Matcl->dataatcl(array('idarticle','a_date','a_title','a_isi','category','cat_icon','uname'),$totatcl,1);
		$dtatcl='';
		foreach ($tmpatcl as $k => $v) {
			$dtatcl .='<li class="item">
							<div class="product-img">
								<span class="fa fa-2x '.$v['cat_icon'].'" alt="'.$v['category'].'" title="'.$v['category'].'"></span>
							</div>
							<div class="product-info">
								<a href="'.base_url('Member/Article/read?id='.$v['idarticle']).'" class="product-title">'.$v['a_title'].'</a>
								<span class="label label-info fixed pull-right"><i class="fa fa-clock-o"></i> '.date('d-M-Y, H:i',strtotime($v['a_date'])).'</span>
								<br/>
								<small class="text-muted"><i class="fa fa-user"></i> '.$v['uname'].' <i class="fa fa-tags"></i> '.$v['category'].'</small>
								<span class="product-description">
								'.strip_tags(htmlspecialchars_decode($v['a_isi'])).'							
								</span>
							</div>
							</li>';
			
		}
		$data['dtatcl']= $dtatcl;


		//============= populate agendas ========
		$totagn = $this->Msetting->getset('renderagn');
		$header=['<i class="fa fa-info-circle"></i> Agenda','<i class="fa fa-calendar"></i> Date','<i class="fa fa-clock-o"></i> Time','<i class="fa fa-building"></i> Place','<i class="fa fa-sticky-note"></i> Details'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped table-responsive">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$this->load->model('Magn');
		$tmpagn = $this->Magn->showagn(array('agtitle','agdate','agtime','agplace','agdescript','idagenda'),$totagn,1);
		foreach ($tmpagn as $k => $v) {
			$tmpagn[$k]['agtitle'] = '<a href="'.base_url('Member/Agenda/read?id=').$v['idagenda'].'" title="'.$v['agtitle'].'">'.$v['agtitle'].'</a>';
			$tmpagn[$k]['agdate'] =  date('d-M-Y',strtotime($v['agdate']));
			unset($tmpagn[$k]['idagenda']);
			
		}
		$data['dtagn'] = $this->table->generate($tmpagn);


		//=============== Template ============
		$data['cssFiles'] = array(
							'h-timeline/h-timeline');
							
		$data['jsFiles'] = array(
							'');
		
		
		
		$data['title']="Dashboard";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/dashboard', $data, TRUE);
		$this->load->view ('template/main', $data);
	}

	 

}
