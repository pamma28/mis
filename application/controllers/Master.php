<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends Admin_Controller {
	
	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation'));
		
		$this->load->helper(array());
		
		$this->load->model(array('Mmaster'));
		
		
    }
	
	
	public function index(){
		$this->save();
		
	}
	
	
	public function savefac(){
	
		// ========== catch id ============
		
		if($this->input->get('idfac')!=null){
			$id = $this->input->get('idfac');
			$temp = $this->Mmaster->editfac($id);
			$fac = $temp['nama_fakultas'];
			$data['title'] = 'Edit Faculty';
		} else {
			$data['title'] = 'Add Faculty';
			$id = '';
			$fac=set_value('ffac');
		}
		
		// ============== Form fac ============
		
		$fname = array('name'=>'ffac',
						'id'=>'namafac',
						'placeholder'=>'Faculty Name',
						'value'=>$fac,
						'class'=>'form-control',
						'size'=>'100');
		$data['infac'] = form_input($fname);
		
		$data['inid'] = form_hidden('fid',$id);
		
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
		
		$this->form_validation->set_rules('ffac', 'Faculty Name', 'required|trim|xss_clean');
		
		//================= save pds ================
		if($this->form_validation->run()== true)
		{
			$qpds = array (
				'id_fakultas' => $this->input->post('fid'),
				'nama_fakultas' => $this->input->post('ffac')
				);
		
			$sukses = $this->Mmaster->savefac($qpds);
			
			if ($sukses){
				$this->session->set_flashdata('v',$data['title']);
			}
		} 
		
		//============== view handler ================
		
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/master/fac', $data, TRUE);
		$this->load->view ('template/main', $data);
	
	

	}
	
	public function registration_success(){
		// ============= email handler ===============
		
		$this->load->config('email');
		$conf = $this->config->item('conf','email');
		$this->load->library('email',$conf);
		
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
}