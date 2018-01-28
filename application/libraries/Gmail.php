<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/google/vendor/autoload.php";
 
class Gmail extends Google_Client {
    public function __construct() {
        parent::__construct();
		
    }
	private $id= '126281380190-delh8i7ad6617nhbv58vkjictdc097nt.apps.googleusercontent.com';
	private $secret= '4_ctFkQnzwWuydiwYCixX9Lm';
	
	public function authorize($redirect){
		// Google Client Configuration
        $gClient = new Google_Client();
        $gClient->setApplicationName('SEF Membership');
        $gClient->setScopes(array(
				"https://mail.google.com/",
				"https://www.googleapis.com/auth/userinfo.profile",
				"https://www.googleapis.com/auth/userinfo.email",
				"https://www.googleapis.com/auth/gmail.readonly",
				"https://www.googleapis.com/auth/gmail.send"));
        $gClient->setClientId($this->id);
        $gClient->setClientSecret($this->secret);
        $gClient->setRedirectUri($redirect);
        $gClient->setAccessType('offline');		
		$urlauth = $gClient->createAuthUrl();
		$CI =& get_instance();
		$CI->load->model('Msetting');
		
		if (($CI->input->get('code')) and ($CI->Msetting->getset('sendmailcode')==null)){	
			if($gClient->authenticate($CI->input->get('code'))){
				$dtset=array(
					'sendermail'=>'',
					'sendmailcode'=>$CI->input->get('code'),
					'sendmailtoken'=>$gClient->getAccessToken(),
					'sendmailrefreshtoken'=>json_decode($gClient->getAccessToken())->refresh_token
					);
				//get userinfo email
				$gClient->setAccessToken($dtset['sendmailtoken']); 
				$plus = new Google_Service_Plus($gClient);
				$me= $plus->people->get("me");
				$dtset['sendermail']= $me['emails'][0]['value'];
				$CI->Msetting->savesetting($dtset);
				$CI->session->set_flashdata('v','Configure Gmail (Sender) success');
			} else if ($gClient->isAccessTokenExpired()) {
				$retoken = $CI->Msetting->getset('sendmailrefreshtoken');
				if ($gClient->refreshToken($retoken)){
				$dtset=array(
				'sendmailtoken'=>$gClient->getAccessToken()
				);
				$CI->Msetting->savesetting($dtset);
				}
			}	
		}
		return $urlauth;
	}
	
	public function sendmail($to,$cc=null,$subject,$alias=null,$code,$listfile = null){
		$gClient = new Google_Client();
		$gClient->setClientId($this->id);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri(base_url('Organizer/Dashboard'));
		$gClient->setScopes(array(
				"https://mail.google.com/",
				"https://www.googleapis.com/auth/gmail.readonly",
				"https://www.googleapis.com/auth/gmail.send"));
		$CI =& get_instance();
		$CI->load->model('Msetting'); 
		$token = $CI->Msetting->getset('sendmailtoken');
		$sender = $CI->Msetting->getset('sendermail');
		if ($token!=null) {
			$gClient->setAccessToken($token);            
			$objGMail = new Google_Service_Gmail($gClient);
			
			
			//setting email content
			$boundary = uniqid(rand(), true);
			$strRawMessage = "From: ".$this->encodeRecipients($alias." <".$sender.">")."\r\n";
			$strRawMessage .= "To: <".$to.">\r\n";
			(null!=$cc) ? $strRawMessage .= "CC: <".$cc.">\r\n":null;
			$strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
			$strRawMessage .= "MIME-Version: 1.0\r\n";
			
			//check if any attachment
			//prepare file
			if ($listfile != ''){
			$arrfile = $listfile;
			$strAttMessage = '';
			$totsize = 0;
				foreach ($arrfile as $k=>$v){
					$filePath = FCPATH.'temp_upload/'.$v;
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mimeType = finfo_file($finfo, $filePath);
					$fileSize = filesize($filePath);
					$totsize = $totsize+$fileSize;
					$fileName = $v;
					$fileData = base64_encode(file_get_contents($filePath));
					
					//file attachment
					$strAttMessage .= "\r\n--{$boundary}\r\n";
					$strAttMessage .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";            
					$strAttMessage .= 'Content-ID: <' .$sender. '>' . "\r\n";            
					$strAttMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
					$strAttMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath). ';' . "\r\n";
					$strAttMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
					$strAttMessage .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
				}
			$strRawMessage .= 'Content-type: Multipart/related\r\n';
			$strRawMessage .= 'Content-Length: '.($totsize).';boundary="' . $boundary . '"' . "\r\n";
			$strRawMessage .= $strAttMessage;
			$strRawMessage .= "\r\n--{$boundary}\r\n";
			} else {
				$strAttMessage = '';
			}
			
			//send text (html)
			$strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
			$strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
			$strRawMessage .= $code;
			
			
			//Users.messages->send - Requires -> Prepare the message in message/rfc822
			try {
				// The message needs to be encoded in Base64URL
				$mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
				$msg = new Google_Service_Gmail_Message();
				$msg->setRaw($mime);
		 
				//The special value **me** can be used to indicate the authenticated user.
				$objSentMsg = $objGMail->users_messages->send("me", $msg);
				return true;
			} catch (Exception $e) {
				return $e->getMessage();
			}
		} else{
			return 'no token';
		}
	
	}
	
	private function encodeRecipients($recipient){
		$recipientsCharset = 'utf-8';
		if (preg_match("/(.*)<(.*)>/", $recipient, $regs)) {
			$recipient = '=?' . $recipientsCharset . '?B?'.base64_encode($regs[1]).'?= <'.$regs[2].'>';
		}
		return $recipient;
	}
	
	public function getemail(){
		$gClient = new Google_Client();
		$gClient->setClientId($this->id);
		$gClient->setClientSecret($this->secret);
		$gClient->setRedirectUri(base_url('Organizer/Dashboard'));
		$gClient->setScopes(array(
				"https://mail.google.com/",
				"https://www.googleapis.com/auth/userinfo.profile",
				"https://www.googleapis.com/auth/userinfo.email"));
		$CI =& get_instance();
		$CI->load->model('Msetting'); 
		$token = $CI->Msetting->getset('sendmailtoken');
		if ($token!=null) {
			//get email authorized
				$gClient->setAccessToken($token); 
				$plus = new Google_Service_Plus($gClient);
				$optParams = [];
				$optParams['fields']= 'emails';
				$me = $plus->people->get("me",$optParams);
				print($me['emails'][0]['value']);
		} else {
			return "no token";
		}
	}
	/*
	if (($this->session->userdata('gtoken')==null) and (null!=($this->input->get('code')))){
			$gClient->authenticate($this->input->get('code'));
			$this->session->set_userdata(array('gtoken'=>$gClient->getAccessToken()));
			$gClient->setAccessToken($this->session->userdata('gtoken'));
		}
		
		$data['token']=$this->session->userdata('gtoken');
		if ($this->session->userdata('gtoken')!=null){
			if ($gClient->isAccessTokenExpired()) {
				//$objtoken = json_decode($this->session->userdata('gtoken'));
				//$gClient->refreshToken($objtoken->refresh_token);
				//$this->session->set_userdata('gtoken',$gClient->getAccessToken());
			  }

		$gClient->setAccessToken($this->session->userdata('gtoken'));}
		
		if($gClient->getAccessToken()){
			$service = new Google_Service_Gmail($gClient);
			$optParams = [];
            $optParams['maxResults'] = 1; // Return Only 5 Messages
            $optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
            $messages = $service->users_messages->listUsersMessages('me',$optParams);
            $m= $messages->getMessages();
			
			
			 $messageId = $m[0]->getId(); // Grab first Message


                $optParamsGet = [];
                $optParamsGet['fields'] = "id,internalDate,payload,raw,snippet,threadId" ;
                $optParamsGet['format'] = 'metadata'; // Display message in payload
                $message = $service->users_messages->get('me',$messageId,$optParamsGet);
                $messagePayload = $message->getPayload();
                $headers = $message->getPayload()->getHeaders();
                $parts = $message->getPayload()->getParts();

               // $body = $parts[0]['body'];
               // $rawData = $body->data;
               // $sanitizedData = strtr($rawData,'-_', '+/');
               // $decodedMessage = base64_decode($sanitizedData);
			//date, subject, from
			$data['msg'] = date("d-m-Y",strtotime($headers[9]['value'])).$headers[10]['value'].$headers[13]['value'];
		
		}
	
	*/
	
	
}