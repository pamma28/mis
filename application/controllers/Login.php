<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('form_validation',"encryption",'getinfophp','Convertcode','gmail'));
		
		$this->load->helper(array('cookie'));
		
		$this->load->model(array('Mlogin','Msetting','Mtmp'));
    }
	
	public function index()
	{
		if(!$this->session->userdata('logged'))
		{
			 $this->auth();
        }
			else{
                redirect('Accesscontrol/');
            }					
		
	}
	
	public function auth()
    {  
		$data['title'] ="Login";
		
		$data['cssFiles'] = array(
							'icheck/blue');  
	
		$data['jsFiles'] = array(
							'icheck.min');
		
		
		// Save set_value
		$valuser = set_value("fuser");
		($this->input->post('rdr')!='') ? $rdr = $this->input->post('rdr'):null;
		($this->input->get('rdr')!='') ? $this->session->set_flashdata('rdr','Your login session has expired. Please login again.'):null;

		// check cookies
		$getcoo = get_cookie("rememberme");
		
		if ((isset($getcoo))){
		$tempcoo = $this->encryption->decrypt($getcoo);
		$tempcoo = stripslashes($tempcoo);
		$tempcoo = json_decode($tempcoo,true);
		
		
			if ($tempcoo["logged"])
			{
				$rdr = $_SERVER['REQUEST_URI'];
				$valuser = $tempcoo["user"];
				$valpass = $this->encryption->decrypt($tempcoo["pass"]);
				$auth = $this->Mlogin->auth($valuser, $valpass);
					if($auth){
					$sess = $this->Mlogin->fetchuserdata($valuser,$valpass);
					$this->session->set_userdata($sess);
					
					// add log login
					$this->insertlogstat($rdr);
					}
			}
		
		}
		
		// ============== Form login ============
		$fuser = array('name'=>'fuser',
						'id'=>'username',
						'placeholder'=>"Username/email",
						'value'=>$valuser,
						'class'=>'form-control',
						'autocomplete'=>'off',
						'size'=>'100');
		$data['inuser'] = form_input($fuser);
		
		$fpass = array('name'=>'fpass',
						'id'=>'password',
						'placeholder'=>'Password',
						'value'=>set_value("fpass"),
						'type'=>'password',
						'class'=>'form-control',
						'size'=>'50');
		$data['inpass'] = form_input($fpass);
		$data['rdr'] = form_hidden('rdr',$this->input->get('rdr'));
		
		// form validation
        $this->form_validation->set_rules('fuser', 'username', 'required|trim|xss_clean');
        $this->form_validation->set_rules('fpass', 'password', 'required|trim|xss_clean');
        
    	if($this->form_validation->run()== false){
            $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
			$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
			$data['content'] = $this->load->view('home/login', $data, TRUE);
			$this->load->view ('template/main', $data);
        }else{
			$user = $this->input->post('fuser');
			$pass = $this->input->post('fpass');
			$exist = $this->Mlogin->checkexist($user);
			if ($exist){
				$allowed = $this->Mlogin->checkallow($user);
				if($allowed){
					$valid = $this->Mlogin->checkvalid($user);
					if($valid){
						$auth = $this->Mlogin->auth($user, md5($pass));
						if($auth){
							$sess = $this->Mlogin->fetchuserdata($user,md5($pass));
											
							//create cookie
							$rem = $this->input->post('remember');
							if($rem){
								
								$valcoo = json_encode(array("user"=>$user,
										"pass"=>$this->encryption->encrypt(md5($pass)),
										"logged"=>true));
								$cookie = array(
									'name'   => 'rememberme' ,
									'value'  => $this->encryption->encrypt($valcoo),
									'expire' => '2592000',
									'prefix' => '',
									'secure' => false
								);
								set_cookie($cookie);
								$this->session->sess_expiration = 2592000; //1 month
								$this->session->sess_expire_on_close = false;
								}
							//set session
							$this->session->set_userdata($sess);
							
							// add log login
							$this->insertlogstat($rdr);
						} else {
							$this->session->set_flashdata("xlogin","Username or Password is incorrect");
						}
					} else{
						$this->session->set_flashdata("xlogin","You need to validate your email");
					}
				} else{
					$this->session->set_flashdata("xlogin","Your account has no longer access");
				}
			} else {
				$this->session->set_flashdata("xlogin","Username or Email is not exist");
			}
        
        $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('home/login', $data, TRUE);
		$this->load->view ('template/main', $data);
        }
	}
	
	
	public function auth_fb()
	{
		
		redirect($login_url);
		
	}
	
	public function logout(){
		$getcoo = get_cookie("rememberme");
        if (isset($getcoo)){
			// catch previous cookie
			$tempcoo = $this->encryption->decrypt($getcoo);
			$tempcoo = stripslashes($tempcoo);
			$tempcoo = json_decode($tempcoo,true);
			$valuser = $tempcoo["user"];
			$valpass = $this->encryption->decrypt($tempcoo["pass"]);
			
			
			$valcoo = json_encode(array(
							"user"=>$valuser,
							"pass"=> $valpass = $this->encryption->encrypt($valpass),
							"logged"=>false));
					$cookie = array(
						'name'   => 'rememberme' ,
						'value'  => $this->encryption->encrypt($valcoo),
						'expire' => '2592000',
						'prefix' => '',
						'secure' => false
					);
				set_cookie($cookie);
		}

        $this->session->sess_destroy();
        $this->session->set_flashdata('v','You are logged out');
        $this->auth();
    }
	
    private function insertlogstat($rdr=null){
		$logstat = array (
					"uuser" =>$this->session->userdata('user'),
					"logdate" =>date("Y-m-d H:i:s"),
					"logip" =>$_SERVER['REMOTE_ADDR'],
					"logdevice" =>$this->getinfophp->getOS(),
					"logbrowser" =>$this->getinfophp->getBrowser()
					);
		$this->Mlogin->addlogstat($logstat);
		($rdr!='') ? header("Location:".$rdr) : redirect("Accesscontrol/");
    }

    public function reset(){
    	// ============== Form login ============
		$femail = array('name'=>'femail',
						'id'=>'femail',
						'required'=>'required',
						'placeholder'=>"Your Email",
						'value'=>set_value("femail"),
						'class'=>'form-control',
						'type'=>'email');
		$data['inemail'] = form_input($femail);
		

    	// form validation
        $this->form_validation->set_rules('femail', 'Email', 'required|valid_email|trim|xss_clean');
        
    	if($this->form_validation->run()== false){
			
			
            $data['title'] ="Reset Account";	
            $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
			$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
			$data['content'] = $this->load->view('home/reset/reset', $data, TRUE);
			$this->load->view ('template/main', $data);
        }else{
    		
			$email = $this->input->post('femail');
			$exist = $this->Mlogin->checkmail($email);
			$dtuser = $this->Mlogin->getdetailbyemail(array('unim','uuser'),$email);
			if($exist>0){
				$randcode = md5($this->encryption->encrypt($dtuser->uuser.$dtuser->unim.$email.date('Y-m-d H:i:s')));
				$setreset = array(
							'urstcode'=>$randcode,
							'ursttime'=>date('Y-m-d H:i:s')
							);
				$r =$this->Mlogin->updatereset($setreset,$email);
				if ($r){
					//email handler
					$period = $this->Msetting->getset('period');
					$idtmp = htmlspecialchars_decode($this->Msetting->getset('mailtemplate'));
					$tmpcontent = htmlspecialchars_decode($this->Mtmp->gettmpdata($this->Msetting->getset('mailresetaccount'))->tmpcontent);
					$rawtext = str_replace("{content_email}", $tmpcontent, $idtmp);
					$to = $email;
					$ccmail=null;
					$bcfrom = "SEF Membership";
					$sub = 'Regular Class '.$period.' - Recovery Account';
					$attfile = null;
				
					if ((null!=$to) and (null!=$sub)){
						
						//====== decode message ============
						$decode = $this->convertcode->decodemailmsg($rawtext,$to);	
						
						//================= gmail send ===========
						$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);
						
					}
				}
				
				redirect("Login/resetaccount?a=req");
			}
			else{
				$this->session->set_flashdata("xreset","No account registered to the email");
				redirect("Login/reset");
            }
    	
		}

    }

    public function recovery($code=null){
    	if($code<>''){
    		$dtuser = $this->Mlogin->getdetailbyrstcode(array('ursttime'),$code)[0];
    		if(!empty($dtuser)){
    			$timeleft = (strtotime("+15 minutes",strtotime($dtuser['ursttime'])))-strtotime("now");
    			if ($timeleft>0){
	    		$data['fnewpass'] = form_password(array(
	    							'class' => 'form-control',
	    							'name' => 'fnewpass',
	    							'required' => 'required',
	    							'placeholder' => 'Your New Password',
	    							'id' => 'newpass'
	    						));
	    		$data['frepass'] = form_password(array(
	    							'class' => 'form-control',
	    							'name' => 'frepass',
	    							'required' => 'required',
	    							'placeholder' => 'Confirm Your New Password',
	    							'id' => 'repass'
	    						));
	    		$data['frstcode'] = form_hidden('frstcode',$code);

	    		$data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
				$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
				$data['content'] = $this->load->view('home/reset/recoveryform', $data, TRUE);
				$this->load->view ('template/main', $data);
    			} else {
    				$this->session->set_flashdata('x','Reset password failed, expired code');
    				redirect('Login');	
    			}
			} else{
				$this->session->set_flashdata('x','Reset password failed, invalid code');
    			redirect('Login');
			} 
    	} else{
    		$this->session->set_flashdata('x','Reset password failed, no code given');
    		redirect('Login');
    	}
    }

    public function resetaccount(){
    	if ($this->input->get('a')=='req'){
    	 	$data['title'] ="Reset Success";
    	 	$this->session->set_flashdata(array('info'=>'Please check your email to reset your password with following link given.',
    	 				'title'=>'Reset Password Requested'
    	 				));
            $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
			$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
			$data['content'] = $this->load->view('home/reset/resetsuccess', $data, TRUE);
			$this->load->view ('template/main', $data);
		} else if ($this->input->get('a')=='com'){
    	 	$data['title'] ="Reset Success";
    	 	$this->session->set_flashdata(array('info'=>'Your password has been reset. Please login again with new password <a href="'.base_url('Login').'" class="bg-blue">here</a>',
    	 				'title'=>'Reset Password Success'
    	 				));
            $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
			$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
			$data['content'] = $this->load->view('home/reset/resetsuccess', $data, TRUE);
			$this->load->view ('template/main', $data);
		}
    }

    public function savereset(){
		$rstcode= $this->input->post("frstcode");
		$dtuser = $this->Mlogin->getdetailbyrstcode(array("uuser","unim","upass"),$rstcode)[0]; 
		$newpass = $this->input->post("fnewpass");
		$confirmpass = $this->input->post("frepass"); $x=0;$s=0;
		if (md5($newpass) <> $dtuser['upass']){
			if ($confirmpass == $newpass){
				$arrdt = array('upass'=>md5($newpass),'urstcode'=>null);
				$hsl = $this->Mlogin->updateacc($arrdt,$dtuser['uuser']);
				if ($hsl){
					$go= "Update Account Password Success";
					$t = $go;
					$s++;
				} else {
					$go= "Update Account Password Failed";
					$t = $go;
					$x++;
				}
			} else {
				$go = "Your confirmation password is mismatch";
				$t = "Update Account Password Failed";
				$x++;
			} 
		} else {
			$go = "Your new password is the same as previous (NIM)";
			$t = "Update Account Password Failed";
			$x++;
		}

		$data['title'] =$t;
		if($s>$x){
			$this->session->set_flashdata(array('info'=>$go.'. Please login again with new password <a href="'.base_url('Login').'" class="bg-blue">here</a>',
	    	 				'title'=>$t
	    	 				));
			$data['content'] = $this->load->view('home/reset/resetsuccess', $data, TRUE);
		} else {
			$this->session->set_flashdata(array('info'=>$go.'. Please login again with old password <a href="'.base_url('Login').'" class="bg-blue">here</a>',
	    	 				'title'=>$t
	    	 				));
			$this->session->set_flashdata('x',$go);
			redirect('Login/recovery/'.$rstcode);
		
		}

        $data['topbar'] = $this->load->view('home/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('home/sidebar', NULL, TRUE);
		$this->load->view ('template/main', $data);
	}

    public function startscheduler(){
    	$this->load->library(array('Cronjob','Gmail'));
    	$this->cronjob->startcron();

    }

    public function checkloggedin(){
    	if($this->session->userdata('user')<>''){
    		echo '1';
    	} else {
    		echo '0';
    	}
    }

    public function bla(){
    	$this->load->view('home/emailsuccess');
    }

}
