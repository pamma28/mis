<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation','table'));
		
		$this->load->model('Msetting');
    }

	public function index()
	{
		$data['jsFiles'] = array(
							'countdown/jquery.countdown.min');
							
		$data['r'] = $this->Msetting->allset();
		$data['title']="Home";
		// the "TRUE" argument tells it to return the content, rather than display it immediately
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/content', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	public function tryemail(){
	$fbbm = array('name'=>'fcode',
						'id'=>'bbm',
						'title'=>'Your LINE or BBM',
						'placeholder'=>'BBM ID',
						'value'=>set_value('fcode'),
						'class'=>'form-control',
						'size'=>'7');
	$data['incode'] = form_input($fbbm);
	$data['insend'] = form_submit();
	$this->load->view('sendemail',$data);
	}
	
	public function save(){
	$this->load->library('MY_Input');
	echo $this->input->post('fcode',false);
	}
	
	public function email(){
		$data['title']= 'try email';
		$this->load->view('home/emailsuccess',$data);
	}

	public function error(){
		$data['urlprev'] = $_SERVER['REQUEST_URI']; 
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/error', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function agendas(){
		//============= populate agendas ========
		$totagn = $this->Msetting->getset('renderagn');
		$header=['<i class="fa fa-info-circle"></i> Agenda','<i class="fa fa-calendar"></i> Date','<i class="fa fa-clock-o"></i> Time','<i class="fa fa-building"></i> Place','<i class="fa fa-sticky-note"></i> Details'];
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped table-responsive">');
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


		$data['title']="Agendas";
		// the "TRUE" argument tells it to return the content, rather than display it immediately
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/content', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function articles(){
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


		$data['title']="Articles";
		// the "TRUE" argument tells it to return the content, rather than display it immediately
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/content', $data, TRUE);
		$this->load->view ('template/main', $data);

	}

	
}
