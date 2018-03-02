<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Managepds extends Mem_Controller {

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
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['ufoto','ucreated','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','umin','ustatus'];
		$header=$this->returncolomn($column);
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
		//========== data manipulation =========
	
		$tmpdata = $this->Mpds->detailpds($column,$this->session->userdata('user'));	
		$a = 0; $totmis=0; $arrmis=array();
		foreach($header as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<div class="row"><label for="l'.$key.'" class="col-md-4 col-sm-4 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-md-8 col-sm-8">: '.$tmpdata[0][$column[$a]].'</div></div>'
					);
			if (($key=='Photo')){
					$foto = ($tmpdata[0][$column[$a]]!='') ? $tmpdata[0][$column[$a]] : 'avatar.png';
					$dtable[$a] = array(
						"dtcol"=>'<div class="row"><label for="photo'.$key.'" class="col-md-4 col-sm-4 control-label"><b>'.$key.'</b></label>',
						"dtval"=>'<div class="col-md-8 col-sm-8"> : <img src="'.base_url('upload/foto/'.$foto).'" class="img-thumbnail" style="height:100px" align="center"></div></div>'
						);
					}
			if (($key=='Birthdate')){
					$dtable[$a] = array(
						"dtcol"=>'<div class="row"><div class="col-sm-4 col-md-4"><b>'.$key.'</b></div>',
						"dtval"=>'<div class="col-md-8 col-sm-8"> : '.date('d-M-Y',strtotime($tmpdata[0][$column[$a]])).'</div></div>'
						);
					}
			if(($column[$a]<>'umin') and ($column[$a]<>'ufoto')){
				if ($tmpdata[0][$column[$a]] == '') {
					$totmis ++;
					$arrmis[]=$key;
				}
			}

			$a++;
		}
		$data['totmiss'] = $totmis; $data['totinp']=$a+1;
		$data['rmissing'] = implode(', ',$arrmis);
		$data['rdata'] = $this->table->generate($dtable);
		
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'');  
		// =============== view handler ============
		$data['title']="My Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pds/mypds', $data, TRUE);
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
	
	public function editpds(){					
		// ============== Fetch data ============
		$col = ['uname','user.idjk','unim','user.idfac','ubplace','ubdate','uemail','uhp','ubbm','uaddrnow','uaddhome'];
		$g = $this->Mpds->detailpds($col,$this->session->userdata('user'));
		// ========= form edit ================ 
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
						'disabled'=>'disabled',
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
						'placeholder'=>'Birthdate format (dd/mm/yyyy), eg: 21/05/1998',
						'value'=>date("d/m/Y",strtotime($g[0]['ubdate'])),
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'required'=>'required',
						'data-inputmask' => "'alias': 'dd/mm/yyyy'",
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
		
		$fhp = array('name'=>'fhp',
						'id'=>'nohp',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>$g[0]['uhp'],
						'class'=>'form-control',
						'max-length'=>13,
						'size'=>'50');
		$r[] = form_input($fhp);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'SocialMedia',
						'placeholder'=>'Social Media',
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
		$fsend = array(	'id'=>'submit',
						'value'=>'Update My PDS',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
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
		$this->table->set_template($tmpl);
		//=========== generate edit form =========================
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<div class="row form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		//=============== Template ============
		$data['jsFiles'] = array('selectpicker/select.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','inputmask/inputmask.numeric.extensions');
		$data['cssFiles'] = array('selectpicker/select.min');  
		// =============== view handler ============
		$data['title']="Edit Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pds/editpds', $data, TRUE);
		$this->load->view ('template/main', $data);
	
	
	}
	
	public function updatepds(){
		$bdate = $this->input->post('fbdate');
		$fixdate = DateTime::createFromFormat('d/m/Y', $bdate);
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $fixdate->format('Y-m-d'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome')
					);
		$r = $this->Mpds->updatepds($fdata,$this->session->userdata('user'));
		
		if ($r){
		$this->session->set_flashdata('v','Update Personal Data Sheet Success');
		} else {		
		$this->session->set_flashdata('x','Update Personal Data Sheet Failed');
		}
		redirect(base_url('Member/Managepds'));
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
		redirect(base_url('Member/Managepds'));
	
	}

	public function deletepds(){
		$id = $this->input->get('id');
		$r = $this->Mpds->deletepds($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Member/Managepds'));
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
		redirect(base_url('Member/Managepds'));
	}
	
	public function returncolomn($header) {
	$find=['ucreated','uupdate','uuser','uname','jkname','user.idjk','idjk','ubplace','ubdate','unim','uemail','uhp','ufoto','ubbm','uaddrnow','uaddhome','umin','upaycode','ustatus','ulunas','fac.idfac','user.idfac','idfac','fname','uallow','upass'];
	$replace = ['Date Registered','Last Updated','Username','Full Name','Gender','Gender','Gender','Birthplace','Birthdate','NIM','Email','Phone Number','Photo','Social Media','Current Address','Home Address','Member Index Number','Payment','Status','Full Payment','Faculty','Faculty','Faculty','Faculty','Allow/Deny','Password'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
