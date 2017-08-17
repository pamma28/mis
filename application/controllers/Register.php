<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation'));
		
		$this->load->helper(array('string','form','dpdf','file'));
		
		$this->load->model(array('Mregister','Msetting'));
		
		
    }
	
	
	public function index(){
		$this->save();
		
	}
	
	
	public function save(){
	
							
			$kode = $this->Mregister->getcode();
		$data['kode'] = $kode;
		// ============== Form pds ============
		
		$fname = array('name'=>'fname',
						'id'=>'nama',
						'title'=>'Your Full Name',
						'placeholder'=>'Full Name',
						'required'=>'required',
						'value'=>set_value('fname'),
						'class'=>'form-control',
						'size'=>'100');
		$data['innama'] = form_input($fname);
		
		$fnim = array('name'=>'fnim',
						'id'=>'nim',
						'title'=>'Your NIM',
						'placeholder'=>'NIM',
						'required'=>'required',
						'value'=>set_value('fnim'),
						'class'=>'form-control',
						'size'=>'12');
		$data['innim'] = form_input($fnim);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'ktp',
						'placeholder'=>'Facebook/twitter or both link',
						'value'=>set_value('fsocmed'),
						'class'=>'form-control',
						'size'=>'25');
		$data['insoc'] = form_input($fsoc);
		
			$optktp = array (
						''=>'Please Select',
						'L'=>'Male',
						'P'=>'Female',
						'O'=>'Other',
						);
		$fgender = array('name'=>'fjk',
						'id'=>'gender',
						'title'=>'Your Gender',
						'placeholder'=>'Gender',
						'class'=>'form-control');
		$data['ingen'] = form_dropdown($fgender,$optktp,set_value('fjk'));
		
			$optfac= $this->Mregister->optfac();
		$ffac = array('name'=>'ffac',
						'id'=>'faculty',
						'title'=>'Your Faculty',
						'placeholder'=>'Faculty',
						'class'=>'form-control');
		$data['infac'] = form_dropdown($ffac,$optfac,set_value('ffac'));
		
		$fbirthplace = array('name'=>'fbplc',
						'id'=>'birthplace',
						'title'=>'Your Birthplace',
						'placeholder'=>'Birthplace',
						'value'=>set_value('fbplc'),
						'class'=>'form-control',
						'size'=>'50');
		$data['inbplc'] = form_input($fbirthplace);
		
		$fbirthdate = array('name'=>'fbd',
						'id'=>'birthdate',
						'title'=>'Your Birthdate eg. 1997/01/29',
						'placeholder'=>'Birthdate format (yyyy/mm/dd), eg: 1996/09/16',
						'value'=>set_value('fbd'),
						'required'=>'required',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'yyyy/mm/dd'",
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
						'size'=>'12');
		$data['inhp'] = form_input($fnohp);
		
		$femail = array('name'=>'femail',
						'id'=>'email',
						'placeholder'=>'Email',
						'title'=>'Your Email',
						'value'=>set_value('femail'),
						'required'=>'required',
						'class'=>'form-control',
						'size'=>'50',
						'type'=>'email');
		$data['inemail'] = form_input($femail);
		
		$fbbm = array('name'=>'fbbm',
						'id'=>'bbm',
						'title'=>'Your LINE or BBM',
						'placeholder'=>'BBM ID',
						'value'=>set_value('fbbm'),
						'class'=>'form-control',
						'size'=>'7');
		$data['inbbm'] = form_input($fbbm);
		
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
		
		$data['interm'] = form_textarea(array(
						'name'=>'fterm',
						'id'=>'term',
						'title'=>'Term & Condition',
						'placeholder'=>'Term and Condition',
						'rows'=>'5',
						'readonly'=>'readonly',
						'class'=>'form-control'),'blablabla');
		
		$data['inaggree'] = form_checkbox(array(
							'name'=>'faggree',
							'id'=>'agree',
							'title'=>'Term & Condition',
							'value'=>set_value('faggree'),
							'required'=>'required',
							'value'=>'1')
							);
		
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
		
		$this->form_validation->set_rules('fname', 'name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fnim', 'NIM', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fsocmed', 'Social Media', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fjk', 'Gender', 'required|trim|xss_clean');
		$this->form_validation->set_rules('ffac', 'Faculty', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fbplc', 'Birth Place', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fbd', 'Birth Date', 'required|trim|xss_clean');
		$this->form_validation->set_rules('femail', 'Email', 'required|trim|xss_clean');
		$this->form_validation->set_rules('fbbm', 'BBM Pin', 'trim|xss_clean');
		$this->form_validation->set_rules('fnohp', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('faddrnow', 'Current Address', 'required|trim|xss_clean');
		$this->form_validation->set_rules('faddrhome', 'Home Address', 'required|trim|xss_clean');
		
		//================= save pds ================
		date_default_timezone_set('Asia/Jakarta');
		$tgl = date('m/d/Y h:i:s a', time());
		if($this->form_validation->run()== true)
		{
			$qpds = array (
				'id_fakultas' => $this->input->post('ffac'),
				'Date_pds' => date('Y-m-d h:i:s'),
				'FULL_NAME' => $this->input->post('fname'),
				'NIM' => $this->input->post('fnim'),
				'socmed' => $this->input->post('fktp'),
				'TEMPAT_LAHIR' => $this->input->post('fbplc'),
				'TGL_LAHIR' => $this->input->post('fbd'),
				'NO_HP' => $this->input->post('fnohp'),
				'MAIL' => $this->input->post('femail'),
				'BBM' => $this->input->post('fbbm'),
				'ALAMAT_NOW' => $this->input->post('faddrnow'),
				'ALAMAT_HOME' => $this->input->post('faddrhome'),
				'JK' => $this->input->post('fjk'),
				'Code_pay' => $this->input->post('fkode')
				);
		
			$sukses = $this->Mregister->savepds($qpds);
			
			if ($sukses){
				$this->session->set_flashdata('pds',$qpds);
				redirect('Register/registration_success');
			}
		} 
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','validate/jquery.validate.min');
		$data['cssFiles'] = array(
							'form-wizard'
							);
							
		//============== view handler ================
		$data['title'] = 'Registration';
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/form/registration', $data, TRUE);
		$this->load->view ('template/main', $data);
	
	

	}
	
	public function registration_success(){
		// ============= email handler ===============
		
		$this->load->config('email');
		$conf = $this->config->item('conf','email');
		$this->load->library('email',$conf);
		$this->email->set_newline("\r\n");
		
		$data['pds'] = $this->session->userdata('pds');
		$data['content'] = $this->load->view('home/form/printsuccess', $data, TRUE);
		$msg = $this->load->view ('template/print', $data,true);
		$this->email->from("education@sefunsoed.org", "SEF Membership");
		$this->email->to($data['pds']['MAIL']);
		$this->email->subject("Registration SEF Membership Code (".$data['pds']['Code_pay'].")");
		$this->email->message($msg);
		
		if (!$this->email->send()){
			show_error($this->email->print_debugger());
		}
		
		//============= view handler ==================
		$data['title'] = 'Registration Success';
		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/form/success', NULL, TRUE);
		$this->load->view ('template/main', $data);
	
	
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