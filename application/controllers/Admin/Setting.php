<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney','Converttime','Gmail'));
		$this->load->helper(array('form','breadcrumb'));
		
		$this->load->model(array('Mlogin','Mchart','Mpds','Mpay','Msetting'));
    }

	public function index(){
	
	//============== save handler ========================
		//====================================================
		if($this->input->post('finputs')<>''){
			$arrinputs = explode(',',$this->input->post('finputs'));
			$go =  "";$x=0;$s=0;
			if($this->input->post('fflash')=='pass'){
				$oldpass = md5($this->input->post("upassold"));
				$newpass = $this->input->post("upassnew");
				$confirmpass = $this->input->post("upassnew2");
				$dtpass = $this->Mlogin->detaillogin(array("upass"),$this->session->userdata('user'))[0]['upass']; 
				if ($oldpass == $dtpass){
					if ($confirmpass == $newpass){
						$arrdt = array('upass'=>md5($newpass));
						$hsl = $this->Mlogin->updateacc($arrdt,$this->session->userdata('user'));
						if ($hsl){
							$go= "Update Password Setting Success";
							$s++;
						} else {
							$go= "Update Password Setting Failed";
							$x++;
						}
					} else {
						$go = "Your confirmation password is mismatch with new password";
						$x++;
					} 
				} else {
					$go = "Your old password is mismatch";
					$x++; 
				}
			} else if($this->input->post('fflash')=='pho'){
					$arrfilename =explode('.', $_FILES['ufoto']['name']);
					$extfilename = end($arrfilename);
					$new_name = md5($this->session->userdata('user').$_FILES['ufoto']['name']).'.'.$extfilename;
					$tmpfoto = $this->Mlogin->detaillogin(array('ufoto'),$this->session->userdata('user'));
					$config['upload_path'] = FCPATH.'upload/foto/';
					$config['allowed_types'] = 'png|jpg|jpeg';
					$config['max_size']     = '300';
					$config['overwrite'] = true;
					$config['file_name'] = $new_name;
					//$config['max_width'] = '500';
					//$config['max_height'] = '500';
					$this->load->library('upload', $config);

					if ($this->upload->do_upload('ufoto')){
			            $go = 'Upload New Profile Photo Success';
			            $newfile = $new_name ;
			            $hslfoto = $this->Mlogin->updateacc(array('ufoto'=>$newfile),$this->session->userdata('user'));
			            ($hslfoto) ? $this->session->set_userdata(array('photo'=>$newfile)): null;
			            if (($tmpfoto <> $newfile)) { unlink(FCPATH.'upload/foto/'.$tmpfoto);}
			            $s=1;
			               
		            } else {
		            	$go = $this->upload->display_errors();
		            	$x=1;
		            }
		    } else {
			foreach ($arrinputs as $k => $v) {
						//============== save data handler =============
						if (($this->input->post($v)!='') and ($go =='')){
							$arrdt[$v] = $this->input->post($v);
						}
					}
					$hsl = $this->Mlogin->updateacc($arrdt,$this->session->userdata('user'));
					if ($hsl){
						$go= "Update Account Setting Success";
						$s++;
					} else {
						$go= "Update Account Setting Failed";
						$x++;
					}
		    }
			
			($go<>'') ? $txt = $go : $txt= "Update Success";
			($s>$x) ? $this->session->set_flashdata('v'.$this->input->post('fflash'),$txt) : $this->session->set_flashdata('x'.$this->input->post('fflash'),$txt);
			header("Location: ".$_SERVER['REQUEST_URI']);
		}




		//========== setting account =========
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnacc=['ufoto','uname','uemail','uhp','ubbm'];
		$labelacc = ['Photo','Full Name','Email','Phone Number','Social Media'];
		$arracc = $this->Mlogin->detaillogin($columnacc,$this->session->userdata('user'))[0];
		foreach ($columnacc as $k => $v) {

			$tempacc[$k] =array($labelacc[$k], 
						form_input(array(
							'id'=>'f'.$columnacc[$k],
							'name' => $columnacc[$k],
							'class'=>'form-control',
							'value'=>$arracc[$v],
							'required'=>'required')));
			if($columnacc[$k]=="uemail"){
				$tempacc[$k][1] .= '<p class="text-danger hidden">Email Is Taken</p><span id="emailnow" class="hidden">'.$arracc[$v].'</span>';
			} 
			else if($columnacc[$k]=="ufoto"){
				($arracc[$v]=='') ? $foto = "avatar.png" : $foto = $arracc[$v];	
				$tempacc[$k][1] = '<div class="thumbnail text-center">
							        <img src="'.base_url("upload/foto/".$foto).'" alt="" class="img-responsive imgava" alt="user photo">
							        <div class="caption">
							        	<button type="button" class="btn" data-toggle="modal" data-target="#fotoModal">
							            <i class="fa fa-cloud-upload fa-2x"></i> <br/>
							            <small><b>Upload</b></small>
							            </button>
							        </div>
							    </div>';
			}  
					
		}

		$data['acc']=array('table' => $this->table->generate($tempacc),
									'title' => 'Account Setting',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateacc')),
									'finputs' => form_hidden('finputs',implode($columnacc, ',')).form_hidden('fflash','acc')
									); 
		

		//========== setting photo =========
		
		$tmpl = array ( 'table_open'  => '<table class="table table-hover text-center">',
						'row_open' );
		$this->table->set_template($tmpl);
		
		$columnpho=['ufoto'];
		$arrpho = $this->Mlogin->detaillogin($columnpho,$this->session->userdata('user'))[0];
		foreach ($columnpho as $k => $v) {
					
			$temppho[$k] =array( 
							'<div class="text-center">
							<div class="well">
								<div id="prevmyphoto" class="img-thumbnail" style="height:200px;width:200px;">
							     </div>
							</div>
							<hr/><p><b>Upload New Photo (Max 300kb)</b></p>'.form_upload(array(
							'id'=>'f'.$columnpho[$k],
							'name' => $columnpho[$k],
							'class'=>'btn btn-default',
							'style'=> 'margin:0px auto',
							'required'=>'required')).'</div>');
		}
		$data['pho']=array('table' => $this->table->generate($temppho),
									'title' => 'Change Photo',
									'fbtn' => form_submit(array('value'=>'Update Photo',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnacc, ',')).form_hidden('fflash','pho')
									); 
		

		//========== setting password =========
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnpass=['upassold','upassnew','upassnew2'];
		$labelpass = ['Old Password',"New Password","Confirm New Password"];
		
		foreach ($columnpass as $k => $v) {		
			$temppass[$k] =array($labelpass[$k], 
						form_input(array(
							'id'=>'f'.$columnpass[$k],
							'name' => $columnpass[$k],
							'class'=>'form-control',
							'value'=>'',
							'type'=>'password',
							'required'=>'required')));
		}

		$data['pass']=array('table' => $this->table->generate($temppass),
									'title' => 'Password Setting',
									'fbtn' => form_submit(array('value'=>'Update Password',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs','upass').form_hidden('fflash','pass')
									); 


		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','uploadpreview/uploadPreview');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Setting Account";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/setting/account', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
}
