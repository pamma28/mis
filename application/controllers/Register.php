<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation'));
		
		$this->load->helper(array('string','form','dpdf','file'));
		
		$this->load->model(array('Mpds','Msetting','Mlogin'));
		
		
    }
	
	
	public function index(){
		$kode = $this->Mpds->getcode();
		$data['kode'] = $kode;
		
		// ============== Form pds ============
		$fuser = array('name'=>'fuser',
						'id'=>'user',
						'title'=>'Your NIM',
						'placeholder'=>'NIM',
						'required'=>'required',
						'value'=>set_value('fuser'),
						'class'=>'form-control input-lg',
						'maxlength'=>'12');
		$data['inuser'] = form_input($fuser);

		$fname = array('name'=>'fname',
						'id'=>'nama',
						'title'=>'Your Full Name',
						'placeholder'=>'Full Name',
						'required'=>'required',
						'value'=>set_value('fname'),
						'class'=>'form-control',
						'maxlength'=>'100');
		$data['innama'] = form_input($fname);
		
		
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'ktp',
						'placeholder'=>'Facebook/twitter or both link',
						'value'=>set_value('fsocmed'),
						'class'=>'form-control',
						'maxlength'=>'50');
		$data['insoc'] = form_input($fsoc);
		
			$optjk = $this->Mpds->optjk();
		$fgender = array('name'=>'fjk',
						'id'=>'gender',
						'title'=>'Your Gender',
						'required'=>'required',
						'placeholder'=>'Gender',
						'class'=>'form-control');
		$data['ingen'] = form_dropdown($fgender,$optjk,set_value('fjk'));
		
			$optfac= $this->Mpds->optfac();
		$ffac = array('name'=>'ffac',
						'id'=>'faculty',
						'title'=>'Your Faculty',
						'required'=>'required',
						'placeholder'=>'Faculty',
						'class'=>'form-control');
		$data['infac'] = form_dropdown($ffac,$optfac,set_value('ffac'));
		
		$fbirthplace = array('name'=>'fbplc',
						'id'=>'birthplace',
						'title'=>'Your Birthplace',
						'placeholder'=>'Birthplace',
						'value'=>set_value('fbplc'),
						'class'=>'form-control',
						'maxlength'=>'50');
		$data['inbplc'] = form_input($fbirthplace);
		
		$fbirthdate = array('name'=>'fbd',
						'id'=>'birthdate',
						'title'=>'Your Birthdate eg. 22/02/1998',
						'placeholder'=>'Birthdate format (dd/mm/yyyy), eg: 22/02/1998',
						'value'=>set_value('fbd'),
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd/mm/yyyy'",
						'datamask' => ''
						);
		$data['inbdt'] = form_input($fbirthdate);
		
		$fnohp = array('name'=>'fnohp',
						'id'=>'nohp',
						'placeholder'=>'Phone Number',
						'title'=>'Your Phone Number',
						'required'=>'required',
						'value'=>set_value('fnohp'),
						'class'=>'form-control',
						'maxlength'=>'13');
		$data['inhp'] = form_input($fnohp);
		
		$femail = array('name'=>'femail',
						'id'=>'email',
						'placeholder'=>'Email',
						'title'=>'Your Email',
						'value'=>set_value('femail'),
						'required'=>'required',
						'class'=>'form-control',
						'maxlength'=>'50',
						'type'=>'email');
		$data['inemail'] = form_input($femail);
		
		
		$faddrnow = array('name'=>'faddrnow',
						'id'=>'addresnow',
						'title'=>'Your Address Now',
						'placeholder'=>'Current Address',
						'rows'=>'5',
						'class'=>'form-control');
		$data['inaddn'] = form_textarea($faddrnow,set_value('faddrnow'));
		
		$faddrhome = array('name'=>'faddrhome',
						'id'=>'addreshome',
						'title'=>'Your Home Address',
						'placeholder'=>'Home Address',
						'rows'=>'5',
						'class'=>'form-control');
		$data['inaddh'] = form_textarea($faddrhome,set_value('faddrhome'));
		
		$textterm = $this->Msetting->getset('formregistterm');
		$data['interm'] = form_textarea(array(
						'name'=>'fterm',
						'id'=>'term',
						'title'=>'Term & Condition',
						'placeholder'=>'Term and Condition',
						'rows'=>'5',
						'readonly'=>'readonly',
						'class'=>'form-control'),$textterm);
		
		$data['inagree'] = form_checkbox(array(
							'name'=>'faggree',
							'id'=>'agree',
							'title'=>'Term & Condition',
							'checked'=>set_value('faggree'),
							'required'=>'required',
							'class'=>'checkbox icheck',
							'value'=>'1')
							);

		$fcaptcha = array('name'=>'fcaptcha',
						'id'=>'captcha',
						'placeholder'=>'Your Captcha',
						'title'=>'Insert Your Captcha',
						'value'=>null,
						'required'=>'required',
						'class'=>'form-control',
						'maxlength'=>'5',
						'type'=>'text');

		$data['incaptcha'] = form_input($fcaptcha);
		
		$data['inkode'] = form_hidden('fkode',$kode);
		
		$freset = array('id'=>'reset',
						'class'=>'btn btn-default',
						'type'=>'reset',
						'value'=>'reset');
		$data['inres'] = form_button($freset,'Reset');
		
		$fsend = array(	'id'=>'submit',
						'value'=>'submit',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['insend'] = form_submit($fsend);
		
		$data['period'] = $this->Msetting->getset('period');
		
		$this->form_validation->set_rules('fuser', 'NIM', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fname', 'Fulll Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fsocmed', 'Social Media', 'trim|xss_clean');
		$this->form_validation->set_rules('fjk', 'Gender', 'required|trim|xss_clean');
		$this->form_validation->set_rules('ffac', 'Faculty', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fbplc', 'Birth Place', 'trim|xss_clean');
		$this->form_validation->set_rules('fbd', 'Birth Date', 'trim|xss_clean');
		$this->form_validation->set_rules('femail', 'Email', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fnohp', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('faddrnow', 'Current Address', 'trim|xss_clean');
		$this->form_validation->set_rules('faddrhome', 'Home Address', 'trim|xss_clean');
		

		//=============== captcha ==============
		if ($this->form_validation->run()==false) {
		$this->load->library('captcha');
		$captcha = $this->captcha->createcaptcha();
		$this->session->set_userdata('imgcaptcha',$captcha->inline());
		$this->session->set_userdata('captcha',$captcha->getPhrase());
		}

		//=============== Template ============
		$data['jsFiles'] = array(
							'inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','inputmask/inputmask.numeric .extensions','validate/jquery.validate.min','icheck.min');
		$data['cssFiles'] = array(
							'form-wizard','icheck/blue'
							);
							
		//============== view handler ================
		$data['title'] = 'Registration';
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/form/registration', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	
	public function save(){
	
							
		//================= save pds ================
		
		if(($this->session->userdata('captcha')==$this->input->post('fcaptcha'))) 
		{
			($this->input->post('fbd')!=null) ? $bdate = $this->input->post('fbd') : $bdate = date('Y-m-d H:i:s');

			$qpds = array (
				'uuser' => $this->input->post('fuser'),
				'upass' => md5($kode),
				'idfac' => $this->input->post('ffac'),
				'ucreated' => date('Y-m-d h:i:s'),
				'uname' => $this->input->post('fname'),
				'unim' => $this->input->post('fuser'),
				'ubbm' => $this->input->post('fktp'),
				'ubplace' => $this->input->post('fbplc'),
				'ubdate' => $bdate,
				'uhp' => $this->input->post('fnohp'),
				'uemail' => $this->input->post('femail'),
				'uaddrnow' => $this->input->post('faddrnow'),
				'uaddhome' => $this->input->post('faddrhome'),
				'idjk' => $this->input->post('fjk'),
				'upaycode' => $kode
				);
		
			$sukses = $this->Mpds->addpds($qpds);
			
			if ($sukses){
				$this->session->sess_destroy();
				$this->session->set_flashdata('successregist',$qpds);
				redirect('Register/registrationsuccess');
			}
		} else{
			$this->session->set_flashdata('failedregist','Your captcha is incorrect');
			$this->index();
		}
		
	}
	
	public function registrationsuccess(){
		$this->load->library(array('MY_Input','Convertcode','Gmail'));
		$formdata = $this->session->userdata('successregist');
		$period = $this->Msetting->getset('period');
		$textmail = $this->Msetting->getset('formregistsuccess');		

		// ============= email handler ===============
		$to = 'pamma.cyber@gmail.com';//$formdata['uemail'];
		$ccmail=null;
		$bcfrom = "SEF Membership";
		$sub = 'Regular Class '.$period.' - Registration Success CODEPAY';//('.$formdata['upaycode'].')';
		$content = $textmail;
		$attfile = null;
		
		if ((null!=$to) and (null!=$sub)){
			
			//====== decode message ============
			$decode = $this->convertcode->decodemailmsg($content,$to);	
			
			//================= gmail send ===========
			$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);
		}		

		// ============= data for view ===============
		//$data['pds']= $formdata;

		//============= view handler ==================
		$data['title'] = 'Registration Success';
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/form/success', NULL, TRUE);
		$this->load->view ('template/main', $data);
	
	
	}
	
	public function recaptcha(){
		$this->load->library('captcha');
		$newcaptcha = $this->captcha->createcaptcha();
		$this->session->set_userdata('imgcaptcha',$newcaptcha->inline());
		$this->session->set_userdata('captcha',$newcaptcha->getPhrase());
		echo $newcaptcha->inline();
		return $newcaptcha->inline();
	}

	public function checkemail(){
		$em = $this->input->post('email');
		echo $this->Mlogin->checkmail($em);
	}
	
	public function checkuser(){
		$us = $this->input->post('user');
		echo $this->Mlogin->checkuser($us);
	}

	
	public function printmail(){
		$data['dt'] = $this->Msetting->getset('period');
		$this->load->view('home/form/printsuccess',$data);
	}
	
	public function coba(){
		$data['title'] = 'Registration Success';
		$data['topbar'] = '';
		$data['sidebar'] = '';
		$html = $this->load->view('home/form/printsuccess', NULL, TRUE);
		//$html = $this->load->view ('template/print', $data,TRUE);
		
		$filename='coba print';
		print_pdf($html,$filename);
		
		//$d= print_pdf($html,'',false);
		//write_file($filename,$d);
	}
	
	
	public function sms(){
		
		$sender = $this->input->get('sender');
		$detail = $this->input->get('form');
		$arrdetail = explode(' ',$detail);
		$valid = 0;
		foreach($arrdetail as $v){
			if($v=='') {
			echo "[RC SEF]Your format is invalid, try again (RCSEF NAME NIM YYYYMMDD EMAIL).";
			die();
			} else {
			$valid = 1;
			}
		}
		
		if ($valid==1){ 
		$this->load->Model('Mlogin');
		$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uuser' => strtoupper($arrdetail[1]),
					'upass' => md5($arrdetail[2]),
					'uname' => strtoupper($arrdetail[0]),
					'uupdate' => '',
					'uemail' => $arrdetail[3],
					'uhp' => $sender,
					'idrole' => '3',
					'uallow' => '1'
					);
		$r = $this->Mlogin->addacc($fdata);
		//($r) ? print('[RC SEF] Thank you for your registration, check your email ('.$arrdetail[3].') for more details.'):print('[RC SEF] We cannot process your registration due to duplication of email or NIM');
		}
		
	}
}