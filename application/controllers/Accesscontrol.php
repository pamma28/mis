<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accesscontrol extends Logged_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('session'));
		
		$this->load->model('');
    }

	public function index(){
		switch($this->role)
			{
				case 1: redirect ("Admin/Dashboard"); break;
				case 2: redirect ("Organizer/Dashboard"); break;
				case 3: redirect ("Member/Dashboard"); break;
				default: ; break;
			}
	}
	
}
