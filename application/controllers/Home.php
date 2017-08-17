<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation'));
		
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
	
	
	
}
