<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/htmldomparser/simple_html_dom.php";

class Curl {

	public function curlpost($url,$params)
		{
		  $postData = '';
		   //create name value pairs seperated by &
		   foreach($params as $k => $v) 
		   { 
			  $postData .= $k . '='.$v.'&'; 
		   }
		   $postData = rtrim($postData, '&');
		 
			$ch = curl_init();  
		 
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER, false); 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
			curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
			curl_setopt($ch, CURLOPT_POST, count($postData));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
	
			$output=curl_exec($ch);
		 
			curl_close($ch);
			return $output;
		 
		}
  }
 
?>