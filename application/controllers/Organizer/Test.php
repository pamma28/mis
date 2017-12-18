<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mtest','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idtest','tcreated','tname','tduration','tktrgn','a.uname as creator','b.uname as editor'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		// checkbox checkalldata
				$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'form-class',
							'value'=>'all',
							'id'=>'c_all'
							));	
				array_unshift($header,$checkall);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 10;
		$perpage= isset($tempfilter['page']) ? $tempfilter['page'] : 1;
		
		if ($durl!=null){
			unset($filter['view']);
			unset($filter['page']);
			$filter= array_filter($filter, function($filter) 
										{return ($filter !== null && $filter !== false && $filter !== '');
							});
			//implode query address
			$addrpage= http_build_query($filter);
			$addrpage = empty($addrpage)? null:$addrpage.'&';
			if ((array_key_exists('column',$filter)) and  (array_key_exists('search',$filter))){
				$vc = $filter['column'];
				$vq = $filter['search'];
				unset($filter['column']);
				unset($filter['search']);
				$filter[$vc]=$vq;
				$data['d']='';
				}
			else if ((empty($filter['view'])) and (!empty($filter))){
			$data['d']='d';
			}
			//count rows of data (with filter/search)
			$rows = $this->Mtest->counttest($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mtest->counttest();	
		}
		//================ filter handler ================
		$fq = array('name'=>'search',
						'id'=>'search',
						'required'=>'required',
						'placeholder'=>'Search Here',
						'value'=> isset($tempfilter['search']) ? $tempfilter['search'] : null ,
						'class'=>'form-control');
		$data['inq'] = form_input($fq);
			$optf = array(
						'tcreated' => 'Test Created',
						'tname' => 'Test Name',
						'tduration' => 'Test Duration',
						'tktrgn' => 'Test Notes'
						);
		$fc = array('name'=>'column',
						'id'=>'col',
						'class'=>'form-control'
					);
		$data['inc'] = form_dropdown($fc,$optf,isset($tempfilter['column']) ? $tempfilter['column'] : null);
		$data['inv'] = form_hidden('view',isset($tempfilter['view']) ? $tempfilter['view'] : 10);
		
		$fbq = array(	'id'=>'bsearch',
						'value'=>'search',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['bq'] = form_submit($fbq);
		
		// ============= advanced filter ===============
		$adv['Period'] = form_input(
						array('name'=>'period',
						'id'=>'period',
						'placeholder'=>'Period',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		$adv['Test Created'] = form_input(
						array('name'=>'tcreated',
						'id'=>'createdon',
						'placeholder'=>'Date Created',
						'value'=>isset($tempfilter['tcreated']) ? $tempfilter['tcreated'] : null,
						'class'=>'form-control'));
		
		$adv['Test Name'] = form_input(
						array('name'=>'tname',
						'id'=>'testname',
						'placeholder'=>'Test Name',
						'value'=>isset($tempfilter['tname']) ? $tempfilter['tname'] : null,
						'class'=>'form-control'));
		
		$adv['Test Duration'] = form_input(
						array('name'=>'tduration',
						'placeholder'=>'Test Duration (in min)',
						'value'=>isset($tempfilter['tduration']) ? $tempfilter['tduration'] : null,
						'class'=>'form-control'));
						
		$adv['Test Notes'] = form_input(
						array('name'=>'tktrgn',
						'id'=>'testnotes',
						'placeholder'=>'Test Notes',
						'class'=>'form-control',
						'value'=>isset($tempfilter['tktrgn']) ? $tempfilter['tktrgn'] : null
						));
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/PDS?'.$addrpage.'view='.$offset,
				'total_rows' => $rows,
				'per_page' => $offset,
				'use_page_numbers' => true,
				'page_query_string' =>true,
				'query_string_segment' =>'page',
				'num_links' => 3,
				'cur_tag_open' => '<span class="disabled"><a href="#">',
				'cur_tag_close' => '<span class="sr-only"></span></a></span>',
				'next_link' => 'Next',
				'prev_link' => 'Prev'
				);
		$data["urlperpage"] = base_url().'Organizer/PDS?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mtest->datatest($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				
					$temp[$key]['tduration']=$temp[$key]['tduration'].' minute(s)';
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idtest']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['tname']='<span class="idname">'.$temp[$key]['tname'].'</span>';
				$temp[$key]['tcreated']=date('d-M-Y', strtotime($value['tcreated'])).'<br/>'.date('H:i:s', strtotime($value['tcreated']));
				//manipulation menu
				$enc = $value['idtest'];
				unset($temp[$key]['idtest']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Test/detailtest?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Test/edittest?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Test/deletetest?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
				}
		$data['listlogin'] = $this->table->generate($temp);
		
		// ======== activate/deactivate account ==============
			$data['idac']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedid',
								'name'=>'fusers'
								));
			$data['idtype']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedtype',
								'name'=>'ftype'
								));
			$data['factselected'] = site_url('Organizer/Test/updateselected');
			
		
		//=============== setting registration phase ============
			$start = $this->Msetting->getset('beginregist');
			$end = $this->Msetting->getset('endregist');
			$data['fregist']= form_input(array('id'=>'registrange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fregistphase',							
								'placeholder'=>'Registration Phase',							
								'value'=>$start.' - '.$end,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/PDS/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/test/testlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function detailtest(){
		//fecth data from db
		$col=['tcreated','tlupdate','tname','tduration','tktrgn','a.uname as creator','b.uname as editor'];
		$id = $this->input->get('id');
		$dbres = $this->Mtest->detailtest($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		$col[5]='creator';
		$col[6]='editor';
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
			if (($key=='Test Duration')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.$dbres[0][$col[$a]].'minute(s)'
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/test/detailtest', $data);
		
		
	}
	
	public function addtest(){
		//============ form add pds available account ===========
		$ftname = array('name'=>'ftname',
						'id'=>'testname',
						'required'=>'required',
						'placeholder'=>'Test Name',
						'value'=>'',
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($ftname);
		
		$fdur = array('name'=>'fdur',
						'id'=>'testdur',
						'required'=>'required',
						'placeholder'=>'Test Duration (min)',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fdur);
		
		$ftnote = array('name'=>'ftket',
						'id'=>'testnote',
						'placeholder'=>'Test Note',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($ftnote,'');
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col=['tname','tduration','tktrgn'];
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
		
		$this->load->view('dashboard/org/test/addtest', $data);
	}
	
	public function edittest(){					
		// ============== Fetch data ============
		$col=['tcreated','tname','tduration','tktrgn'];
		$id = $this->input->get('id');
		$g = $this->Mtest->detailtest($col,$id);
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.$g[0]['tcreated'].'</label>';
			
		$ftname = array('name'=>'ftname',
						'id'=>'testname',
						'required'=>'required',
						'placeholder'=>'Test Name',
						'value'=>$g[0]['tname'],
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($ftname);
		
		$fdur = array('name'=>'fdur',
						'id'=>'testdur',
						'required'=>'required',
						'placeholder'=>'Test Duration (min)',
						'value'=>$g[0]['tduration'],
						'class'=>'form-control');
		$r[] = form_input($fdur);
		
		$ftnote = array('name'=>'ftket',
						'id'=>'testnote',
						'placeholder'=>'Test Note',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($ftnote,$g[0]['tktrgn']);
		
		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
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
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->load->view('dashboard/org/test/edittest', $data);
	
	
	}
	
	public function updatetest(){
		if ($this->input->post('fid')!=null){
		$id = $this->input->post('fid');
		$fdata = array (				
					'tlupdate' => date("Y-m-d H:i:s"),
					'tuse_uuser' => $this->session->userdata('user'),
					'tname' => $this->input->post('ftname'),
					'tduration' => $this->input->post('fdur'),
					'tktrgn' => $this->input->post('ftket')
					);
		$r = $this->Mtest->updatetest($fdata,$id);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Test Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Test Data Failed');
		}
		redirect(base_url('Organizer/Test'));
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			if ($type == '0') {
				$s=0;$x=0;
				$done = true;
				foreach ($dtuser as $v) {
					
					$res= $this->Mtest->deletetest($v);
					if ($res) 
					{
						$s++;
					} else {
						$done = false;
						$msg = $this->db->_error_message();
						$x++;
					}
				}
			}
				
			($done) ? $this->session->set_flashdata('v','Update '.$totuser.' Selected Test Data Success.<br/>Details: '.$s.' Success and '.$x.' Error(s)') : $this->session->set_flashdata('x','Update '.$totuser.' Selected Test Data Failed.<br/>Details: '.$msg);
		} else{
		$this->session->set_flashdata('x','No Data Selected, Update Selected Test Data Failed.');
		}
		redirect(base_url('Organizer/Test'));
	}
		
	public function savetest(){
	 
		if ($this->input->post('ftname')!=null){
		$fdata = array (
					'tcreated' => date("Y-m-d H:i:s"),
					'uuser' => $this->session->userdata('user'),
					'tname' => $this->input->post('ftname'),
					'tduration' => $this->input->post('fdur'),
					'tktrgn' => $this->input->post('ftket')
					);
		$r = $this->Mtest->addtest($fdata);
		}
		if ($r){
		$this->session->set_flashdata('v','Add Test Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Test Data Failed');
		}
		redirect(base_url('Organizer/Test'));
	
	}

	public function deletetest(){
		$id = $this->input->get('id');
		$r = $this->Mtest->deletetest($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Organizer/Test'));
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
	$find=['idtest','tlupdate','tcreated','tname','tduration','tktrgn','a.uname as creator','b.uname as editor','a.uuser'];
	$replace = ['Test ID','Last Updated','Test Created','Test Name','Test Duration','Test Notes','Created by','Last Edited by','Created by'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
