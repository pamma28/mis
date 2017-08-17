<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation'));
		$this->load->helper(array('form','breadcrumb'));
		
		$this->load->model(array('Msetting'));
    }

	public function index(){
		$this->parameter();				
	}
	
	public function parameter(){
	
		//=============== Template ============
		$data['jsFiles'] = array(
							'inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
	
		//================ save handler ============
	
		if ($this->input->post()!= null)	
		{
		$set = array (
				'period' => $this->input->post('fperiod'),
				'price' => $this->input->post('fprice'),
				'no_atm' => $this->input->post('fatm'),
				'an_atm' => $this->input->post('fan'),
				'jns_bank' => $this->input->post('fjatm'),
				'quota' => $this->input->post('fquo'),
				'begin' => $this->input->post('fbegin'),
				'expired' => $this->input->post('fover'),
				'cp' => $this->input->post('fnohp'),
				'bbm' => $this->input->post('fbbm'),
				'email' => $this->input->post('femail')
				);
		
			$this->Msetting->savesetting($set);
		} else {
	
			$set = $this->Msetting->getset();
			
		}
		
		// ============== Form setting ============
		
		$fp = array('name'=>'fperiod',
						'id'=>'period',
						'placeholder'=>'',
						'value'=>$set['period'],
						'type'=>'number',
						'class'=>'form-control',
						'size'=>'100');
		$data['inper'] = form_input($fp);
		
		$fpr = array('name'=>'fprice',
						'id'=>'price',
						'placeholder'=>'Price',
						'value'=>$set['price'],
						'type'=>'number',
						'class'=>'form-control',
						'size'=>'20');
		$data['inpr'] = form_input($fpr);
		
		$fatm = array('name'=>'fatm',
						'id'=>'atm',
						'placeholder'=>'Bank Account Number',
						'value'=>$set['no_atm'],
						'type'=>'text',
						'class'=>'form-control',
						'size'=>'50');
		$data['inatm'] = form_input($fatm);
		
		$fan = array('name'=>'fan',
						'id'=>'atasnama',
						'placeholder'=>'Bank Account belong (a.n)',
						'value'=>$set['an_atm'],
						'class'=>'form-control',
						'size'=>'50');
		$data['inan'] = form_input($fan);
		
		$fjatm = array('name'=>'fjatm',
						'id'=>'banktype',
						'placeholder'=>'Bank Name',
						'value'=>$set['jns_bank'],
						'class'=>'form-control',
						'size'=>'50');
		$data['injatm'] = form_input($fjatm);
		
		$fq = array('name'=>'fquo',
						'id'=>'quota',
						'placeholder'=>'Quota',
						'value'=>$set['quota'],
						'type'=>'number',
						'class'=>'form-control',
						'size'=>'3');
		$data['inq'] = form_input($fq);
		
		
		$fbgn = array('name'=>'fbegin',
						'id'=>'begindate',
						'placeholder'=>'Begin Registration',
						'value'=>$set['begin'],
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'20',
						'data-inputmask' => "'alias': 'yyyy/mm/dd'",
						'datamask' => ''
						);
		$data['inbgn'] = form_input($fbgn);
		
		$fov = array('name'=>'fover',
						'id'=>'overdate',
						'placeholder'=>'End Registration',
						'value'=>$set['expired'],
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'20',
						'data-inputmask' => "'alias': 'yyyy/mm/dd'",
						'datamask' => ''
						);
		$data['inov'] = form_input($fov);
		
		$fnohp = array('name'=>'fnohp',
						'id'=>'nohp',
						'placeholder'=>'CP Number',
						'value'=>$set['cp'],
						'class'=>'form-control',
						'size'=>'12');
		$data['inhp'] = form_input($fnohp);
		
		$femail = array('name'=>'femail',
						'id'=>'email',
						'placeholder'=>'Email',
						'value'=>$set['email'],
						'class'=>'form-control',
						'size'=>'50',
						'type'=>'email');
		$data['inemail'] = form_input($femail);
		
		$fbbm = array('name'=>'fbbm',
						'id'=>'bbm',
						'placeholder'=>'CP BBM',
						'value'=>$set['bbm'],
						'class'=>'form-control',
						'size'=>'7');
		$data['inbbm'] = form_input($fbbm);
		
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Update Setting',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['insend'] = form_submit($fsend);
		
		
		//================ save handler ============
	
		date_default_timezone_set('Asia/Jakarta');
		$tgl = date('m/d/Y h:i:s a', time());
		
			$qset = array (
				'period' => $this->input->post('fperiod'),
				'price' => $this->input->post('fprice'),
				'no_atm' => $this->input->post('fatm'),
				'an_atm' => $this->input->post('fan'),
				'jns_atm' => $this->input->post('fjatm'),
				'quota' => $this->input->post('fquo'),
				'begin' => $this->input->post('fbegin'),
				'expired' => $this->input->post('fover'),
				'cp' => $this->input->post('femail'),
				'bbm' => $this->input->post('fbbm'),
				'email' => $this->input->post('fnohp')
				);
		
			$this->config->set_item('rc', $qset);
			
		
		
		
		// =============== view handler ============
		$data['title']="PDS New";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/setting/setting', $data, TRUE);
		$this->load->view ('template/main', $data);
	
	}
	
}
