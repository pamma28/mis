<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['emailspam']['conf']= Array(
     'useragent' => 'CodeIgniter',
     'protocol' => 'smtp',
     'smtp_host' => 'ssl://smtp.googlemail.com',
	 'smtp_timeout' => '7',
     'smtp_port' => 465,
     'smtp_user' => 'pamma.cyber@gmail.com', 
     'smtp_pass' => "idon'tknow", 
     'mailtype' => 'html',
     'charset' => 'iso-8859-1',
     'validate' => false,
     'priority' => 3,
     'newline' => "\r\n",
     'crlf' => "\r\n",
     'wordwrap' => TRUE
  ); 
