<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');		

class Convertcode {

	function decodemailmsg($code,$id) {
		$CI =& get_instance();
		$CI->load->model('Mlogin');
		
		$dtuser = $CI->Mlogin->getdetailbyemail(array('uname','unim','uemail','lvlname','ulunas','uhp','DATE_FORMAT(ucreated,"%Y") as period','fname','ubdate','idjk'),$id);
		
		(($dtuser->ulunas!= null) and ($dtuser->ulunas== '1')) ? $lunas = 'Fully Paid' : $lunas= "Not Yet";
		($dtuser->idjk == '1') ? $jk = 'Mr.' : $jk= "Ms.";
		
		$find=['{honor}','{name}','{NIM}','{email}','{level}','{payment}','{phone}','{period}','{faculty}','{birthdate}'];
		$replace = [$jk,$dtuser->uname,$dtuser->unim,$dtuser->uemail,$dtuser->lvlname,$lunas,$dtuser->uhp,$dtuser->period,$dtuser->fname,$dtuser->ubdate];
		$result = str_replace($find, $replace, $code);
		return $result;
		
	}
	
	function decodesmsmsg($code,$id) {
		$CI =& get_instance();
		$CI->load->model('Mlogin');
		
		$dtuser = $CI->Mlogin->getdetailbyphone(array('uname','unim','uemail','lvlname','ulunas','uhp','DATE_FORMAT(ucreated,"%Y") as period','fname','ubdate','idjk'),$id);
		
		($dtuser->ulunas) ? $lunas = 'Fully Paid' : $lunas= "Not Yet";
		($dtuser->idjk == '1') ? $jk = 'Mr.' : $jk= "Ms.";
		
		$find=['{honor}','{name}','{NIM}','{email}','{level}','{payment}','{phone}','{period}','{faculty}','{birthdate}'];
		$replace = [$jk,$dtuser->uname,$dtuser->unim,$dtuser->uemail,$dtuser->lvlname,$lunas,$dtuser->uhp,$dtuser->period,$dtuser->fname,$dtuser->ubdate];
		$result = str_replace($find, $replace, $code);
		return $result;
		
	}
	
}