<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LangSwitch extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
 
    }
	
	function switchLanguage($language = "") {
        $language = ($language != "") ? $language : "english";
        $this->session->set_userdata('site_lang', $language);
        redirect($this->input->get('url'));
    }
}
