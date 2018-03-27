<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation','Convertcode','Encryption','gmail'));
		
		$this->load->helper(array('string','form','dpdf','file'));
		
		$this->load->model(array('Mpds','Msetting','Mlogin','Mtmp'));
		
		
    }
	
	
	public function index(){
		
		// ============== Form pds ============
		$fuser = array('name'=>'fuser',
						'id'=>'user',
						'title'=>'Your Username',
						'placeholder'=>'Username',
						'required'=>'required',
						'value'=>set_value('fuser'),
						'class'=>'form-control input-lg',
						'maxlength'=>'20');
		$data['inuser'] = form_input($fuser);

		$fnim = array('name'=>'fnim',
						'id'=>'nim',
						'title'=>'Your NIM',
						'placeholder'=>'NIM',
						'required'=>'required',
						'value'=>set_value('fnim'),
						'class'=>'form-control',
						'maxlength'=>'12');
		$data['innim'] = form_input($fnim);

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

		//============== data ==============
		$registphase = explode(" - ",$this->Msetting->getset('registphase'));
		$startregist = strtotime(str_replace('/', '-', $registphase[0]));
		$endregist =  strtotime(str_replace('/', '-', $registphase[1]));
		$today = strtotime(date("d-m-Y"));
		$data['registphase'] = (($today >= $startregist) and ($today <= $endregist)) ? true : false;
		$data['registdate'] = date("d-M-Y",$startregist)." until ".date('d-M-Y',$endregist);
		//=============== Template ============
		$data['jsFiles'] = array(
							'inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','inputmask/inputmask.numeric.extensions','validate/jquery.validate.min','icheck.min');
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
		$upaycode = $this->Mpds->getcode();			
		//================= save pds ================
		
		if(($this->session->userdata('captcha')==$this->input->post('fcaptcha'))) 
		{
			($this->input->post('fbd')!=null) ? $bdate = $this->input->post('fbd') : $bdate = '2000-12-12 00:00:00';
			$fuser = $this->input->post('fuser');
			$fnim = $this->input->post('fnim');
			$qpds = array (
				'uuser' => $fuser,
				'upass' => md5($fnim),
				'idfac' => $this->input->post('ffac'),
				'ucreated' => date('Y-m-d h:i:s'),
				'uname' => $this->input->post('fname'),
				'unim' => $fnim,
				'ubbm' => $this->input->post('fktp'),
				'ubplace' => $this->input->post('fbplc'),
				'ubdate' => $bdate,
				'uhp' => $this->input->post('fnohp'),
				'uemail' => $this->input->post('femail'),
				'uaddrnow' => $this->input->post('faddrnow'),
				'uaddhome' => $this->input->post('faddrhome'),
				'idjk' => $this->input->post('fjk'),
				//====== creating hash code email confirmation =========
				'uvalidcode'=>md5(date("Y-m-d H:i:s").$fuser.$bdate.$fnim.$this->input->post('fname')),
				'upaycode' => $upaycode,
				'uallow' =>'1',
				'idrole' => '3',
				'uvalidated'=>'0'
				);
		
			$sukses = $this->Mpds->addpds($qpds);
			
			if ($sukses){
				$this->session->sess_destroy();
				$this->session->set_flashdata('successregist',$qpds);
				
				//============ push notif =========

				$this->notifications->pushmynotif(
					array(
						'idnotif'=>$this->Msetting->getset('notifregistsuccess'),
						'uuser' => $fuser,
						'nlink'=>'#'
						)
				);

				$this->notifications->pushNotifToOrg(
					array(
						'idnotif'=>$this->Msetting->getset('notifnewsignup'),
						'uuser' => $fuser,
						'nlink' => base_url('Organizer/PDS')
						)
				);

				$this->registrationsuccess();
			}
		} else{
			$this->session->set_flashdata('failedregist','Your captcha is incorrect');
			$this->index();
		}
		
	}
	
	public function registrationsuccess(){
		$this->load->library(array('MY_Input','Convertcode','Gmail'));
		$this->load->model('Mtmp');
		$formdata = $this->session->userdata('successregist');
		$period = $this->Msetting->getset('period');
		$idtmp = htmlspecialchars_decode($this->Msetting->getset('mailtemplate'));
		$tmpcontent = htmlspecialchars_decode($this->Mtmp->gettmpdata($this->Msetting->getset('mailverify'))->tmpcontent);
		$rawtext = str_replace("{content_email}", $tmpcontent, $idtmp);
		
		// ============= email handler ===============
		$to = $formdata['uemail'];
		$ccmail=null;
		$bcfrom = "SEF Membership";
		$sub = 'Regular Class '.$period.' - Email Verification';
		$attfile = null;
		
		if ((null!=$to) and (null!=$sub)){
			
			//====== decode message ============
			$decode = $this->convertcode->decodemailmsg($rawtext,$to);	
			
			//================= gmail send ===========
			$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);
		}		

		// ============= data for view ===============
		//$data['pds']= $formdata;

		//============= view handler ==================
		$data['title'] = 'Registration Success';
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = '<section class="content-header"><h1>Registration Success</h1></section><section class="content">'.$this->convertcode->decodemailmsg(htmlspecialchars_decode($this->Mtmp->gettmpdata($this->Msetting->getset('formregistsuccess'))->tmpcontent),$to).'</section>';
		$this->load->view ('template/main', $data);
	
	
	}
	
	public function confirmregist($validcode=null){
		if ($validcode != ''){
			$iduser = $this->Mlogin->getuserformvalidcode($validcode);
			if(!empty($iduser)){
				$user = $iduser[0]->uuser;
				$dtuser = $this->Mlogin->detailacc(array('unim','uemail','uvalidated'),$user)[0];

				if ($dtuser['uvalidated']<>'1'){
					// email handler
					$period = $this->Msetting->getset('period');
					$idtmp = htmlspecialchars_decode($this->Msetting->getset('mailtemplate'));
					$tmpcontent = htmlspecialchars_decode($this->Mtmp->gettmpdata($this->Msetting->getset('mailregistsuccess'))->tmpcontent);
					$rawtext = str_replace("{content_email}", $tmpcontent, $idtmp);
					$to = 'pamma.cyber@gmail.com';//$dtuser['uemail'];
					$ccmail=null;
					$bcfrom = "SEF Membership";
					$sub = 'Regular Class '.$period.' - Registration Success';
					$attfile = null;
				
					if ((null!=$to) and (null!=$sub)){
						
						//====== decode message ============
						$decode = $this->convertcode->decodemailmsg($rawtext,$to);	
						
						//================= gmail send ===========
						$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);
					}		

					// data reset password
					$rstcode = md5($this->encryption->encrypt($user.$dtuser['unim'].$dtuser['uemail'].date('Y-m-d H:i:s')));
					$this->Mlogin->updateacc(array('uvalidated'=>'1','urstcode'=>$rstcode,'ursttime'=>date('Y-m-d H:i:s'),'upass'=>md5($dtuser['unim'])),$user);
					$data['rstcode']=$rstcode;
					$data['nim'] = $dtuser['unim'];
					$data['email'] = $dtuser['uemail'];
					
					// data view
					$this->session->set_flashdata('v','Email Validation Success');
					$data['title'] = 'Email Validation Success';
					$data['content'] = $this->load->view('home/confirmemail/emailvalid', $data, TRUE);
				} else {
					// data view
					$this->session->set_flashdata('x','Email has been validated');
					$data['title'] = 'Email Validation Failed';
					$data['content'] = $this->load->view('home/confirmemail/emailhasvalidated', NULL, TRUE);
				}
			} else {
				// data view
				$this->session->set_flashdata('x','Email Validation Failed, No Such Account Related to Your Validation Code');
				$data['title'] = 'Email Validation Failed';
				$data['content'] = $this->load->view('home/confirmemail/emailinvalid', NULL, TRUE);
			}
		} else {

			// data view
			$this->session->set_flashdata('x','Email Validation Failed');
			$data['title'] = 'Email Validation Failed';
			$data['content'] = $this->load->view('home/confirmemail/emailinvalid', NULL, TRUE);
		}
		//============= view handler ==================
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	

	public function revokeregist(){
		$this->load->view('home/failedregist');
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

	public function checkphone(){
		$us = $this->input->post('nohp');
		echo $this->Mlogin->checkhp($us);
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

	public function haha(){
		$data['title'] = 'Email Validation Success';
		$data['content'] = $this->load->view('home/email/emailvalid',null,true);
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		
		$this->load->view ('template/main', $data);
	}

	public function hahaha(){
		$this->load->library('Convertcode');
		$dt = $this->load->view('home/email/newtemplate',null,true);
		$r = $this->convertcode->decodemailmsg($dt,'pamma.cyber@gmail.com');
		print($r);
	}
	public function hahaha2(){
		$this->load->view('home/email/registsuccess');
	}
}