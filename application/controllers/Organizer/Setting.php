<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mpds','Msetting','Mtmp','Mnotif'));
    }

	public function index(){
	
		//===================== table handler =============
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		
	
		//========== data manipulation setting system =========
		$column=['period','price','quota','cp','email','bbm'];
		$label = ['Period','Price','Quota','Contact Person','Email','Social Media'];
		$arrinput = ['text','number','number','text','email','text'];
		foreach ($column as $k => $v) {		
		$temp[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$column[$k],
						'name' => $column[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>$arrinput[$k],
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp),
									'title' => 'Setting System',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		
		//========== data manipulation setting phase =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnphase=['registphase','paymentphase','schedulephase','certiphase'];
		$label = ['Registration<br/>Phase','Payment<br/>Phase','Schedule Confirmatin<br/>Phase','Certificate<br/>Phase'];
		foreach ($columnphase as $k => $v) {		
		$temp2[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$columnphase[$k],
						'name' => $columnphase[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>'text',
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp2),
									'title' => 'Setting Phase',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		
		//=============== setting registration phase ============
			$registphase = $this->Msetting->getset('registphase');
			$arrregist = explode(' ', $registphase);

			$data['fregist']= form_input(array('id'=>'registrange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fregistphase',							
								'placeholder'=>'Registration Phase',							
								'value'=>$arrregist[0].' - '.$arrregist[1],							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Setting/savesetting');



		//========== data manipulation registration form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnregist=['formregistterm','formregistsuccess','mailregistsuccess'];
		$label = ['Term & Condition (No Special Code)','Template Success Registration (Use Special code)','Email Content (Use Special Code)'];
		$opttmp = $this->Mtmp->getopttmp();
		$temp3 =array(
					array($label[0],
					form_input(array('name'=>'f'.$columnregist[0],
						'id'=>$columnregist[0],
						'required'=>'required',
						'value'=>$this->Msetting->getset('formregistterm'),
						'class'=>'form-control'))),

					array($label[1],
					form_dropdown(array('name'=>'fregistsuccess',
						'id'=>'registsuccess',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker'),$opttmp,$this->Msetting->getset('formregistsuccess'))),

					array($label[2],
					form_dropdown(array('name'=>'fmailregistsuccess',
						'id'=>'mailregistsuccess',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker'),$opttmp,$this->Msetting->getset('mailregistsuccess'))),
				);
		
		$data['registform']=array('table' => $this->table->generate($temp3),
									'title' => 'Registration Form',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		




		//========== data manipulation notification member form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotifmem=['notifbcmail','notifbcsms','notifcertiavailable','notifcertitaken','notifemailvalidated','notifpayconfirmfailed','notifpayconfirmsuccess','notifpaymentphase','notifpayproofuploaded','notifpayment','notifpdscomplete','notifpdsincomplete','notifregistsuccess','notifschedulechosen','notifschedulephase','notiftestactive','notiftestsubmitted','notiftestresult','notifwelcomemem'];
		$labelmem = ['New Broadcast Mail','New Broadcast SMS','Certificate Available','Certificate Phase','Certificate Has Taken','Payment Rejected','Payment Success','Payment Phase','Payment Uploaded','Payment Made','PDS Completed','PDS Incomplete','Registration Success','Schedule Chosen','Schedule Phase','Test Is Active','Test Is Submitted','Test Result Is Available','Welcome Message (Member)'];
		$optnotif = $this->Mnotif->getoptnotif();
		foreach ($columnnotifmem as $k => $v) {		
			$temp4[$k] =array($labelmem[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotifmem[$k],
							'name' => $columnnotifmem[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notifmemform']=array('table' => $this->table->generate($temp4),
									'title' => 'Notification Member',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		
		//========== data manipulation notification org form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotiforg=['notifbcby','notifnewpayproof','notifnewsignup','notifnewtestresult','notifresetpassword','notiftestactivatedby','notifwelcomeorg'];
		$labelorg = ['Broadcast Message','New Payment Confirmation Requested','New Member Registration','New Test Result Submitted','Reset Password','Test Activated','Welcome Message (Org)'];
		foreach ($columnnotiforg as $k => $v) {		
			$temp5[$k] =array($labelorg[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotiforg[$k],
							'name' => $columnnotiforg[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notiforgform']=array('table' => $this->table->generate($temp5),
									'title' => 'Notification Organizer',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		

		//========== data manipulation notification org Admin =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotifadm=['notifnewloginorg','notifwelcomeadm'];
		$labeladm = ['New Login Organizer','Welcome Message Admin'];
		foreach ($columnnotifadm as $k => $v) {		
			$temp6[$k] =array($labeladm[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotifadm[$k],
							'name' => $columnnotifadm[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notifadmform']=array('table' => $this->table->generate($temp6),
									'title' => 'Notification Administrator',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		

				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','summernote/summernote');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Setting System";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/setting/settinglist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 
	public function previewTemplate()
	{
		if ($this->input->post('idtmp')!=''){
			$idtmp = $this->input->post('idtmp');
			$content = $this->Mtmp->detailtmp(array('tmpcontent'),$idtmp)[0];
			echo htmlspecialchars_decode($content['tmpcontent']);
		} else{
			echo 'error';
		}
	}
	
	public function previewNotification()
	{
		if ($this->input->post('idnotif')!=''){
			$idnotif = $this->input->post('idnotif');
			$content = $this->Mnotif->detailnotif(array('ncontent'),$idnotif)[0];
			echo ($content['ncontent']);
		} else{
			echo 'error';
		}
	}
	
	
	public function updatepds(){
		if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => $this->input->post('fstats')
					);
		$r = $this->Mpds->updatepds($fdata,$us);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	
	
	public function getdetailuser(){
		$id = $this->input->post('user');
		echo json_encode($this->Mpds->detailuser($id));
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fregistphase')){
			$dtrange = $this->input->post('fregistphase');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
		$dtset=array(
				'beginregist'=>$dtstart,
				'endregist'=>$dtend
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Registration Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Registration Phase Failed.");
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function returncolomn($header) {
	$find=['period','price','quota','cp','email','bbm','registphase','paymentphase','schedulephase','certiphase'];
	$replace = ['Period','Price','Quota','Contact Person','Email','Social Media','Registration<br/>Phase','Payment<br/>Phase','Schedule Confirmatin<br/>Phase','Certificate<br/>Phase'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
