<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/captcha/vendor/autoload.php";

use Gregwar\Captcha\CaptchaBuilder;;

class Captcha extends CaptchaBuilder{
	public function __construct() {
        parent::__construct();
		
    }
    

    public function createcaptcha(){
    	$builder = new CaptchaBuilder;
		$builder->build();
		return $builder; 
    }

}


?>