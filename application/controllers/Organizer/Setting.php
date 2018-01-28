<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mpds','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		
	
		//========== data manipulation setting system =========
		$column=['period','price','quota','cp','email','bbm'];
		$label = $this->returncolomn($column);
		$arrinput = ['text','number','number','text','email','text'];
		foreach ($column as $k => $v) {		
		$temp[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$column[$k],
						'name' => $column[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>$arrinput[$k],
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp),
									'title' => 'Setting System',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		
		//========== data manipulation setting phase =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnphase=['registphase','paymentphase','schedulephase','certiphase'];
		$label = $this->returncolomn($columnphase);
		foreach ($columnphase as $k => $v) {		
		$temp2[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$columnphase[$k],
						'name' => $columnphase[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>'text',
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp2),
									'title' => 'Setting Phase',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset'))
									); 
		
		



		//=============== setting registration phase ============
			$registphase = $this->Msetting->getset('registphase');
			$arrregist = explode(' ', $registphase);
			$data['fregist']= form_input(array('id'=>'registrange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fregistphase',							
								'placeholder'=>'Registration Phase',							
								'value'=>$arrregist[0].' - '.$arrregist[1],							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Setting/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Setting System";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/setting/settinglist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 
	
	
	public function detailpds(){
		//fecth data from db
		$col = ['ufoto','ucreated','uupdate','uuser','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','umin','ustatus','ulunas'];
		$id = $this->input->get('id');
		$dbres = $this->Mpds->detailpds($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		//set table template
		$tmpl = array ( 'table_open'  => '<table class="table table-striped">',
					'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<td>',
                    'heading_cell_end'    => '</td>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>'

					);
		$this->table->set_template($tmpl);
		//set table data
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			if (($key=='Photo') and ($dbres[0][$col[$a]]=='')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <img src="'.base_url('upload/foto/avatar.png').'" class="img-thumbnail" style="height:100px" align="center">'
						);
					} else if (($key=='Photo') and ($dbres[0][$col[$a]]<>'')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <img src="'.base_url('upload/foto/'.$dbres[0][$col[$a]].'').'" class="img-thumbnail" style="height:100px" align="center">'
						);
					}
			if (($key=='Birthdate')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y',strtotime($dbres[0][$col[$a]]))
						);
					}
			
			if (($key=='Full Payment') and ($dbres[0][$col[$a]]=='1')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-success">Fully Paid</span>'
						);
					} else if (($key=='Full Payment') and ($dbres[0][$col[$a]]=='0')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-warning">Not Yet</span>'
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/pds/detailpds', $data);
		
		
	}
	
	public function addpds(){
		//============ form add pds available account ===========
		{	$optuname = $this->Mpds->optuser();
		$funame = array('name'=>'fpilusername',
						'id'=>'Username',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fusername'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($funame,$optuname,'');
		
		$fname = array('name'=>'ffullname',
						'id'=>'inpname',
						'required'=>'required',
						'placeholder'=>'Full Name',
						'value'=>set_value('ffullname'),
						'class'=>'form-control');
		$r[] = form_input($fname);
		
			$optjk = $this->Mpds->optjk();
		$fjk = array('name'=>'fjk',
						'id'=>'Gender',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fjk'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($fjk,$optjk,'');
		
		$fnim = array('name'=>'fnim',
						'id'=>'NIM',
						'required'=>'required',
						'placeholder'=>'NIM',
						'value'=>set_value('fnim'),
						'class'=>'form-control',
						'size'=>'13');
		$r[] = form_input($fnim);
		
			$optfac = $this->Mpds->optfac();
		$ffac = array('name'=>'ffaculty',
						'id'=>'Faculty',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('ffaculty'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ffac,$optfac,'');
		
			$fbplace = array('name'=>'fbplace',
						'id'=>'bplace',
						'required'=>'required',
						'placeholder'=>'Birthplace',
						'value'=>set_value('fbplace'),
						'class'=>'form-control');
		$r[] = form_input($fbplace);
		
			$fbdate = array('name'=>'fbdate',
						'id'=>'bdate',
						'placeholder'=>'Birthdate format (yyyy/mm/dd), eg: 1996/09/16',
						'value'=>set_value('fbd'),
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'yyyy/mm/dd'",
						'datamask' => ''
						);
		$r[] = form_input($fbdate);
		
			$femail = array('name'=>'femail',
						'id'=>'inpemail',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>set_value('femail'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($femail).'<span style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
			
			$fhp = array('name'=>'fhp',
						'id'=>'inphp',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>set_value('fhp'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fhp);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'SocialMedia',
						'placeholder'=>'BBM PIN/Social Media',
						'value'=>set_value('fsocmed'),
						'class'=>'form-control',
						'size'=>'30');
		$r[] = form_input($fsoc);
		
		$faddrnow = array('name'=>'faddrnow',
						'id'=>'addresnow',
						'placeholder'=>'Current Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrnow,set_value('faddrnow'));
		
		$faddrhome = array('name'=>'faddrhome',
						'id'=>'addreshome',
						'placeholder'=>'Home Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrhome,set_value('faddrhome'));
		}
		{
		//============ form add pds with account ===========
		
		$fname['id'] = 'Fullname';
		$r2[] = form_input($fname);
		
		$fnim['id']='username2';
		$fnim['name']='fusername';
		$r2[] = form_input($fnim).'<span id="usuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Username Available</span><span id="ufailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Username Not Available</span>';
		
		$femail['id']='Email';
		$r2[] = form_input($femail).'<span style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
		
		$r2[] = form_dropdown($fjk,$optjk,'');
		
		$r2[] = form_dropdown($ffac,$optfac,'');
		
		$fbplace['id'] ='bplace2';
		$r2[] = form_input($fbplace);
		
		$fbdate['id'] = 'bdate';
		$r2[] = form_input($fbdate);
		
		$fhp['id'] = 'fhp';
		$r2[] = form_input($fhp);
		
		$r2[] = form_input($fsoc);
		
		$r2[] = form_textarea($faddrnow,set_value('faddrnow'));
		
		$r2[] = form_textarea($faddrhome,set_value('faddrhome'));
		
		//set row title
		$col2 = ['uname','unim','uemail','jkname','fname','Birthplace','Birthdate','uhp','ubbm','uaddrnow','uaddhome'];
		$row2 = $this->returncolomn($col2);
		
		}
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['uuser','uname','jkname','unim','fname','Birthplace','Birthdate','uemail','uhp','ubbm','uaddrnow','uaddhome'];
		$row = $this->returncolomn($col);
		//set table template
		$tmpl = array ( 'table_open'  => '',
					'heading_row_start'   => '',
                    'heading_row_end'     => '',
                    'heading_cell_start'  => '',
                    'heading_cell_end'    => '',

                    'row_start'           => '',
                    'row_end'             => '',
                    'cell_start'          => '',
                    'cell_end'            => '',
					'table_close'         => ''

					);
		//=========== generate add form available user =========================
		$this->table->set_template($tmpl);
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->table->clear();
		
		//=========== generate add form new user =========================
		$this->table->set_template($tmpl);
		$a = 0;
		foreach($row2 as $key)
		{
			$dtable2[$a] = array(
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r2[$a].'</div></div>'
					);
			$a++;
		}
		$data['r2data']=$this->table->generate($dtable2);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Add Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/pds/addpds', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function editpds(){					
		// ============== Fetch data ============
		$col = ['ucreated','uuser','uname','user.idjk','unim','user.idfac','ubplace','ubdate','uemail','upass','uhp','ubbm','uaddrnow','uaddhome','ustatus'];
		$id = $this->input->get('id');
		$g = $this->Mpds->detailpds($col,$id);
		$stat = $g[0]['ustatus'];
		unset($col[14]);
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.$g[0]['ucreated'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['uuser'].'</label>';
			
		$ffname = array('name'=>'ffullname',
						'id'=>'Fullname',
						'required'=>'required',
						'placeholder'=>'Fullname',
						'value'=>$g[0]['uname'],
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($ffname);
		
		$optjk = $this->Mpds->optjk();
		$fjk = array('name'=>'fjk',
						'id'=>'Gender',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fjk'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($fjk,$optjk,$g[0]['idjk']);
		
		$fnim = array('name'=>'fnim',
						'id'=>'NIM',
						'required'=>'required',
						'placeholder'=>'NIM',
						'value'=>$g[0]['unim'],
						'class'=>'form-control',
						'size'=>'13');
		$r[] = form_input($fnim);
		
			$optfac = $this->Mpds->optfac();
		$ffac = array('name'=>'ffaculty',
						'id'=>'Faculty',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('ffaculty'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ffac,$optfac,$g[0]['idfac']);
		
		$fbplace = array('name'=>'fbplace',
						'id'=>'bplace',
						'required'=>'required',
						'placeholder'=>'Birthplace',
						'value'=>$g[0]['ubplace'],
						'class'=>'form-control');
		$r[] = form_input($fbplace);
		
			$fbdate = array('name'=>'fbdate',
						'id'=>'bdate',
						'placeholder'=>'Birthdate format (yyyy/mm/dd), eg: 1996/09/16',
						'value'=>$g[0]['ubdate'],
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'required'=>'required',
						'data-inputmask' => "'alias': 'yyyy/mm/dd'",
						'datamask' => ''
						);
		$r[] = form_input($fbdate);
		
		$femail = array('name'=>'femail',
						'id'=>'Email',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>$g[0]['uemail'],
						'class'=>'form-control',
						'size'=>'50'
						);
		$r[] = form_input($femail).'<span id="valsuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span id="valfailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
		
		$fpass = array('name'=>'fpass',
						'id'=>'password',
						'placeholder'=>'New Password',
						'value'=>'',
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fpass).'<span class="text-danger"><i class="fa fa-exclamation"></i> Let it blank, to keep old password.</span>';
		
		$fhp = array('name'=>'fhp',
						'id'=>'return',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>$g[0]['uhp'],
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fhp);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'SocialMedia',
						'placeholder'=>'BBM PIN/Social Media',
						'value'=>$g[0]['ubbm'],
						'class'=>'form-control',
						'size'=>'30');
		$r[] = form_input($fsoc);
		$faddrnow = array('name'=>'faddrnow',
						'id'=>'addresnow',
						'placeholder'=>'Current Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrnow,$g[0]['uaddrnow']);
		
		$faddrhome = array('name'=>'faddrhome',
						'id'=>'addreshome',
						'placeholder'=>'Home Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrhome,$g[0]['uaddhome']);
		
		$data['inid'] = form_hidden('fuser',$g[0]['uuser']);
		$data['inst'] = form_hidden('fstats',$stat);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($col);
		$row[3]="Gender";
		$row[5]="Faculty";
		//set table template
		$tmpl = array ( 'table_open'  => '',
					'heading_row_start'   => '',
                    'heading_row_end'     => '',
                    'heading_cell_start'  => '',
                    'heading_cell_end'    => '',

                    'row_start'           => '',
                    'row_end'             => '',
                    'cell_start'          => '',
                    'cell_end'            => '',
					'table_close'         => ''

					);
		$this->table->set_template($tmpl);
		//=========== generate edit form =========================
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->load->view('dashboard/org/pds/editpds', $data);
	
	
	}
	
	public function updatepds(){
		if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => $this->input->post('fstats')
					);
		$r = $this->Mpds->updatepds($fdata,$us);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			$r = $this->Mlogin->updateselected($dtuser,$type);
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Member Account success.<br/>Details: '.$r['v'].' success and '.$r['x'].' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Member Account Failed.');
		}
		redirect(base_url('Organizer/PDS'));
	}
		
	public function savepds(){
	
	// separation save method
	if ($this->input->post('fpilusername')!=null){
		$us = $this->input->post('fpilusername');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => 'Registered',
					'ulunas' => '0'
					);
		$r = $this->Mpds->updatepds($fdata,$us);
	} else if ($this->input->post('fusername')!=null){
		$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uupdate' => date("Y-m-d H:i:s"),
					'uuser' => $this->input->post('fusername'),
					'upass' => md5(date("dmY",strtotime($this->input->post('fbdate')))),
					'uname' => $this->input->post('ffullname'),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fusername'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => 'Registered',
					'ulunas' => '0',
					'idrole' => '3',
					'uallow' => '1'
					);
		$r = $this->Mpds->addpds($fdata);
	}
		if ($r){
		$this->session->set_flashdata('v','Add Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	
	}

	public function deletepds(){
		$id = $this->input->get('id');
		$r = $this->Mpds->deletepds($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Organizer/PDS'));
	}

	public function printpds(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['ucreated','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','ulunas'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpds->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpds->exportlogin(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		// config table
		$header = $this->returncolomn($dtcol);
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
				foreach($dexp as $key=>$val){
					//manipulation allow data
					if(array_key_exists('uallow',$val)){
						if ($val['uallow']==1){
						$dexp[$key]['uallow']='Allowed';
						}else{
						$dexp[$key]['uallow']='Denied';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Member Account Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/akun/printacc', $data);
		
	}
	
	public function getdetailuser(){
		$id = $this->input->post('user');
		echo json_encode($this->Mpds->detailuser($id));
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fregistphase')){
			$dtrange = $this->input->post('fregistphase');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
		$dtset=array(
				'beginregist'=>$dtstart,
				'endregist'=>$dtend
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Registration Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Registration Phase Failed.");
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function returncolomn($header) {
	$find=['period','price','quota','cp','email','bbm','registphase','paymentphase','schedulephase','certiphase'];
	$replace = ['Period','Price','Quota','Contact Person','Email','Social Media','Registration<br/>Phase','Payment<br/>Phase','Schedule Confirmatin<br/>Phase','Certificate<br/>Phase'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
