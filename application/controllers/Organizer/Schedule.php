<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Msche','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['jdwl_tes.idjdwl','jdate','tname','jsesi','jroom','jquota','jactive','uname'];
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
			unset($filter['id']);
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
			$rows = $this->Msche->countsche($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Msche->countsche();	
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
						'period' => 'Period',
						'jdate' => 'Schedule Date',
						'tname' => 'Test Name',
						'jsesi' => 'Session',
						'jroom' => 'Room',
						'jquota' => 'Quota',
						'jactive' => 'Active/Inactive',
						'uname' => 'PIC'
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
		$adv['Schedule Date'] = form_input(
						array('name'=>'jdate',
						'id'=>'createddate',
						'placeholder'=>'Schedule Date',
						'value'=>isset($tempfilter['jdate']) ? $tempfilter['jdate'] : null,
						'class'=>'form-control'));
		
		$adv['Test Name'] = form_input(
						array('name'=>'tname',
						'id'=>'testname',
						'placeholder'=>'Test Name',
						'value'=>isset($tempfilter['tname']) ? $tempfilter['tname'] : null,
						'class'=>'form-control'));
		
		$adv['Session'] = form_input(
						array('name'=>'jsesi',
						'placeholder'=>'Session',
						'value'=>isset($tempfilter['jsesi']) ? $tempfilter['jsesi'] : null,
						'class'=>'form-control'));
						
		$adv['Room'] = form_input(
						array('name'=>'jroom',
						'placeholder'=>'Room',
						'value'=>isset($tempfilter['jroom']) ? $tempfilter['jroom'] : null,
						'class'=>'form-control'));
						
		$adv['Quota'] = form_input(
						array('name'=>'jquota',
						'placeholder'=>'Quota',
						'value'=>isset($tempfilter['jquota']) ? $tempfilter['jquota'] : null,
						'class'=>'form-control'));
						
		$adv['Active/Inactive'] = form_dropdown(
						array('name'=>'jactive',
						'id'=>'',
						'class'=>'form-control'
						),
						array(''=>'Please Select','1'=>'Active','0'=>'Inactive'),
						isset($tempfilter['jactive']) ? $tempfilter['jactive'] : null);
		
		$adv['PIC'] = form_input(
						array('name'=>'uname',
						'placeholder'=>'PIC Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
						
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
	
		$temp = $this->Msche->datasche($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation active data
				($value['jactive']) ? $temp[$key]['jactive']='<span class="label label-success">Active</span>':$temp[$key]['jactive']='<span class="label label-warning">Inactive</span>';
				//manipulation date data
				$temp[$key]['jdate']=date("d-M-Y",strtotime($temp[$key]['jdate']));
				
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idjdwl']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['tname']='<span class="idname">'.$temp[$key]['tname'].'</span>';
				//manipulation menu
				$enc = $value['idjdwl'];
				unset($temp[$key]['idjdwl']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Schedule/detailsche?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Schedule/editsche?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Schedule/deletesche?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Schedule/updateselected');
			
		//=============== print handler ============
			$optcol = array(
						'jdate' => 'Schedule Date',
						'tname' => 'Test Name',
						'jsesi' => 'Session',
						'jroom' => 'Room',
						'jquota' => 'Quota',
						'jactive' => 'Active/Inactive',
						'uname' => 'PIC'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol',
							'multiple'=>'multiple',
							'required'=>'required'),$optcol);
			$data['fcheckcol']= form_input(array(
							'name'=>'fcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm selectall',
							'value'=>'Select all columns'
							));
			$data['funcheckcol']= form_input(array(
							'name'=>'funcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm unselectall',
							'value'=>'Unselect all columns'
							));
			$data['fusedate']= form_checkbox(array(
							'name'=>'fusedate',
							'id'=>'usedate',
							'value'=>'use'
							),false);
			$data['fdtrange']= form_input(array(
							'name'=>'fdtrange',
							'type'=>'text',
							'class'=>'form-control frange',
							'id'=>'rangedate'
							));
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Schedule/printsche');
		
		//=============== setting registration phase ============
			$start = $this->Msetting->getset('beginschedule');
			$end = $this->Msetting->getset('endschedule');
			$data['fregist']= form_input(array('id'=>'scherange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fschephase',							
								'placeholder'=>'Schedule Confirmation',							
								'value'=>$start.' - '.$end,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Schedule/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','numeric/numeric.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Test Schedule";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/schedule/schelist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function active(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['jdwl_tes.idjdwl','jdate','jstart','tname','jsesi','jroom','jquota','jactive'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		unset($header[6]);
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
			unset($filter['id']);
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
			$rows = $this->Msche->countsche($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Msche->countsche();	
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
						'period' => 'Period',
						'jdate' => 'Schedule Date',
						'tname' => 'Test Name',
						'jsesi' => 'Session',
						'jroom' => 'Room',
						'jquota' => 'Quota',
						'jactive' => 'Active/Inactive',
						'uname' => 'PIC'
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
		$adv['Schedule Date'] = form_input(
						array('name'=>'jdate',
						'id'=>'createddate',
						'placeholder'=>'Schedule Date',
						'value'=>isset($tempfilter['jdate']) ? $tempfilter['jdate'] : null,
						'class'=>'form-control'));
		
		$adv['Test Name'] = form_input(
						array('name'=>'tname',
						'id'=>'testname',
						'placeholder'=>'Test Name',
						'value'=>isset($tempfilter['tname']) ? $tempfilter['tname'] : null,
						'class'=>'form-control'));
		
		$adv['Session'] = form_input(
						array('name'=>'jsesi',
						'placeholder'=>'Session',
						'value'=>isset($tempfilter['jsesi']) ? $tempfilter['jsesi'] : null,
						'class'=>'form-control'));
						
		$adv['Room'] = form_input(
						array('name'=>'jroom',
						'placeholder'=>'Room',
						'value'=>isset($tempfilter['jroom']) ? $tempfilter['jroom'] : null,
						'class'=>'form-control'));
						
		$adv['Quota'] = form_input(
						array('name'=>'jquota',
						'placeholder'=>'Quota',
						'value'=>isset($tempfilter['jquota']) ? $tempfilter['jquota'] : null,
						'class'=>'form-control'));
						
		$adv['Active/Inactive'] = form_dropdown(
						array('name'=>'jactive',
						'id'=>'',
						'class'=>'form-control'
						),
						array(''=>'Please Select','1'=>'Active','0'=>'Inactive'),
						isset($tempfilter['jactive']) ? $tempfilter['jactive'] : null);
		
		$adv['PIC'] = form_input(
						array('name'=>'uname',
						'placeholder'=>'PIC Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
						
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
	
		$temp = $this->Msche->datasche($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation date data
				$temp[$key]['jdate']=date("d-M-Y",strtotime($temp[$key]['jdate']));
				($temp[$key]['jstart']==null) ? $temp[$key]['jstart']='Not Activated Yet':$temp[$key]['jstart']=date("d-M-Y H:i",strtotime($temp[$key]['jstart']));
				$temp[$key]['tname']='<span class="idname">'.$temp[$key]['tname'].'</span>';
				//manipulation menu
				$enc = $value['idjdwl'];
				//manipulation active data
				(!$value['jactive']) ? $btnact='<a href="'.base_url('Organizer/Schedule/activate?id=').$enc.'" alt="Activate" class="btn-success btn-sm"><i class="fa fa-check"></i> Activate</a>':$btnact='<a href="'.base_url('Organizer/Schedule/deactivate?id=').$enc.'" alt="Deactivate" class="btn-warning btn-sm"><i class="fa fa-ban"></i> Deactivate</a>';
				unset($temp[$key]['idjdwl']);
				unset($temp[$key]['jactive']);
				$temp[$key]['menu']='<small>'.$btnact.' | <a href="'.base_url('Organizer/Schedule/printpresencelist?id=').$enc.'" alt="Print Presence List" class="btn-info btn-sm"><i class="fa fa-print"></i> Print Presence List</a></small>';
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
			$data['factselected'] = site_url('Organizer/Schedule/updateselected');
			
		//=============== print handler ============
			$optcol = array(
						'jdate' => 'Schedule Date',
						'tname' => 'Test Name',
						'jsesi' => 'Session',
						'jroom' => 'Room',
						'jquota' => 'Quota',
						'jactive' => 'Active/Inactive',
						'uname' => 'PIC'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol',
							'multiple'=>'multiple',
							'required'=>'required'),$optcol);
			$data['fcheckcol']= form_input(array(
							'name'=>'fcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm selectall',
							'value'=>'Select all columns'
							));
			$data['funcheckcol']= form_input(array(
							'name'=>'funcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm unselectall',
							'value'=>'Unselect all columns'
							));
			$data['fusedate']= form_checkbox(array(
							'name'=>'fusedate',
							'id'=>'usedate',
							'value'=>'use'
							),false);
			$data['fdtrange']= form_input(array(
							'name'=>'fdtrange',
							'type'=>'text',
							'class'=>'form-control frange',
							'id'=>'rangedate'
							));
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Schedule/printsche');
		
		//=============== setting registration phase ============
			$start = $this->Msetting->getset('beginschedule');
			$end = $this->Msetting->getset('endschedule');
			$data['fregist']= form_input(array('id'=>'scherange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fschephase',							
								'placeholder'=>'Schedule Confirmation',							
								'value'=>$start.' - '.$end,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Schedule/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','numeric/numeric.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Activate Schedule";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/schedule/activatelist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}	
	
	public function detailsche(){
		//fecth data from db
		$col=['jdate','tname','jsesi','jroom','jquota','jactive','uname','1 as mem'];
		$id = $this->input->get('id');
		$dbres = $this->Msche->detailsche($col,$id);
		
		//set row title
		$col[7]='mem';
		$row = $this->returncolomn($col);
		$row[7]='Member Choosing This Schedule';
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
				
			if (($key=='Member Choosing This Schedule')){
					// loop detail member related
					$colq = ['user.uuser','uname','unim','uhp'];
					$dtmem = $this->Msche->populatemember($colq,$id);
					$rst[]= '<tr><td><b>No</b></td><td><b>Member Name</b></td><td><b>NIM</b></td><td><b>Phone Number</b></td><td><b>Action</b></td></tr>';
					$sno=1;
					foreach($dtmem as $sk=>$sv){
						$rst[]='<tr><td>'.$sno.'</td><td>'.$sv['uname'].'</td><td>'.$sv['unim'].'</td><td>'.$sv['uhp'].'</td><td><a href="#" class="btn btn-danger btn-xs" data-href="'.base_url('Organizer/Schedule/RemoveMember?id='.$sv['uuser']).'" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Remove</a></td></tr>';
						$sno++;
					}					
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>'<table class="table">'.implode('',$rst).'</table>'
						);
			} else if ($key=="Status"){
				($dbres[0][$col[$a]]) ? $dbres[0]['jactive']='<span class="label label-success">Active</span>':$dbres[0]['jactive']='<span class="label label-warning">Inactive</span>';
			}
			
			
			
			
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/schedule/detailsche', $data);
		
		
	}
	
	public function addsche(){
		//============ form add pds available account ===========
		$fjdate = array('name'=>'fjdate',
						'id'=>'jdate',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'datamask' => '',
						'required'=>'required',
						'placeholder'=>'Schedule Date',
						'value'=>'');
		$r[] = form_input($fjdate);
		
			$opttest = $this->Msche->opttest();
		$ftname = array('name'=>'idt',
						'id'=>'idt',
						'required'=>'required',
						'placeholder'=>'Test Name',
						'value'=>'',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ftname,$opttest,'');
		
		for($i=8;$i<=22;$i++){
			($i<10) ? $optH['0'.$i] = '0'.$i:$optH[$i] = $i;
		}
		for($i=0;$i<=60;$i++){
			(($i%5)==0) ? ($i<10) ? $optM['0'.$i] = '0'.$i:$optM[$i] = $i:null;
		}
		$fjHstart = array('name'=>'Hs',
						'id'=>'Hstart',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjHend= array('name'=>'Hend',
						'id'=>'Hend',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjMstart = array('name'=>'Ms',
						'id'=>'Mstart',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjMend = array('name'=>'Mend',
						'id'=>'Mend',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
						
		$r[] = 'Session Start (Hour) :'.form_dropdown($fjHstart,$optH).' (Min) :'.form_dropdown($fjMstart,$optM).'<hr/> Session End (Hour) :'.form_dropdown($fjHend,$optH).' (Min) :'.form_dropdown($fjMend,$optM);
		
		$fjroom = array('name'=>'fjroom',
						'id'=>'jroom',
						'required'=>'required',
						'placeholder'=>'Room',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fjroom);
		
		$fjquo = array('name'=>'fjquo',
						'id'=>'jquo',
						'required'=>'required',
						'placeholder'=>'Quota',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fjquo);
				
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col=['jdate','tname','jsesi','jroom','jquota'];
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
		
		$this->load->view('dashboard/org/schedule/addsche', $data);
	}
	
	public function editsche(){					
		// ============== Fetch data ============
		$col=['jdate','jdwl_tes.idtest','jsesi','jroom','jquota','jactive'];
		$id = $this->input->get('id');
		$g = $this->Msche->detailsche($col,$id)[0];
		// ========= form edit ================ 
		$fjdate = array('name'=>'fjdate',
						'id'=>'jdate',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'datamask' => '',
						'required'=>'required',
						'placeholder'=>'Schedule Date',
						'value'=>date('d-m-Y',strtotime($g['jdate'])));
		$r[] = form_input($fjdate);
		
			$opttest = $this->Msche->opttest();
		$ftname = array('name'=>'idt',
						'id'=>'idt',
						'required'=>'required',
						'placeholder'=>'Test Name',
						'value'=>'',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ftname,$opttest,$g['idtest']);
		
		for($i=8;$i<=22;$i++){
			($i<10) ? $optH['0'.$i] = '0'.$i:$optH[$i] = $i;
		}
		for($i=0;$i<=60;$i++){
			(($i%5)==0) ? ($i<10) ? $optM['0'.$i] = '0'.$i:$optM[$i] = $i:null;
		}
		$fjHstart = array('name'=>'Hs',
						'id'=>'Hstart',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjHend= array('name'=>'Hend',
						'id'=>'Hend',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjMstart = array('name'=>'Ms',
						'id'=>'Mstart',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
		$fjMend = array('name'=>'Mend',
						'id'=>'Mend',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker');
						
		$r[] = 'Session Start (Hour) :'.form_dropdown($fjHstart,$optH,mb_substr($g['jsesi'],0,2)).' (Min) :'.form_dropdown($fjMstart,$optM,mb_substr($g['jsesi'],3,2)).'<hr/> Session End (Hour) :'.form_dropdown($fjHend,$optH,mb_substr($g['jsesi'],6,2)).' (Min) :'.form_dropdown($fjMend,$optM,mb_substr($g['jsesi'],9,2));
		
		$fjroom = array('name'=>'fjroom',
						'id'=>'jroom',
						'required'=>'required',
						'placeholder'=>'Room',
						'value'=>$g['jroom'],
						'class'=>'form-control');
		$r[] = form_input($fjroom);
		
		$fjquo = array('name'=>'fjquo',
						'id'=>'jquo',
						'required'=>'required',
						'placeholder'=>'Quota',
						'value'=>$g['jquota'],
						'class'=>'form-control');
		$r[] = form_input($fjquo);
		
		$r[]= form_dropdown(array('name'=>'fjstat',
						'id'=>'jstat',
						'required'=>'required',
						'class'=>'form-control'),
						array(
						''=>'Please Select',
						'0'=>'Inactive',
						'1'=>'Active'
						),$g['jactive']);
		
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
		
		$this->load->view('dashboard/org/schedule/editsche', $data);
	
	
	}
	
	public function updatesche(){
		if ($this->input->post('fid')!=null){
		$id = $this->input->post('fid');
		$fixdate=DateTime::createFromFormat('d-m-Y', $this->input->post('fjdate'));
		$fdata = array (
					'jdate' => $fixdate->format('Y-m-d'),
					'idtest' => $this->input->post('idt'),
					'jsesi' => $this->input->post('Hs').'.'.$this->input->post('Ms').'-'.$this->input->post('Hend').'.'.$this->input->post('Mend'),
					'jroom' => $this->input->post('fjroom'),
					'jactive' => $this->input->post('fjstat'),
					'uuser' => $this->session->userdata('user'),
					'jquota' => $this->input->post('fjquo')
					);
		$r = $this->Msche->updatesche($fdata,$id);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Schedule Test Success');
		} else {		
		$this->session->set_flashdata('x','Update Schedule Test Failed');
		}
		redirect(base_url('Organizer/Schedule'));
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			if (($type=='0') or ($type=='1')) 
			{
				$r = $this->Msche->updateselected($dtuser,$type);
			} else {
				$v=0;$x=0;
					foreach($dtuser as $v){
					$rs = $this->Msche->deletesche($v);
					($rs) ? $v++:$x++;
					}
				$r=array(
				"v"=>$v,
				"x"=>$x
				);
			}
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Schedule success.<br/>Details: '.$r['v'].' success and '.$r['x'].' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Schedule Failed.');
		}
		redirect(base_url('Organizer/Schedule'));
	}
		
	public function savesche(){
		if ($this->input->post('fjdate')!=null){
		$fdata = array (
					'jdate' => date('Y-m-d',strtotime($this->input->post('fjdate'))),
					'idtest' => $this->input->post('idt'),
					'jsesi' => $this->input->post('Hs').'.'.$this->input->post('Ms').'-'.$this->input->post('Hend').'.'.$this->input->post('Mend'),
					'jroom' => $this->input->post('fjroom'),
					'jactive' => 0,
					'uuser' => $this->session->userdata('user'),
					'jquota' => $this->input->post('fjquo')
					);
		$r = $this->Msche->addsche($fdata);
		}
		if ($r){
		$this->session->set_flashdata('v','Add Schedule Test Success');
		} else {		
		$this->session->set_flashdata('x','Add Schedule Test Failed');
		}
		redirect(base_url('Organizer/Schedule'));
	
	}

	public function deletesche(){
		$id = $this->input->get('id');
		$r = $this->Msche->deletesche($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Organizer/Schedule'));
	}
	
	public function printsche(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol=['jdate','tname','jsesi','jroom','jquota','jactive','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Msche->exportsche($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Msche->exportsche(null,null,$dtcol);
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
					if(array_key_exists('jactive',$val)){
						if ($val['jactive']==1){
						$dexp[$key]['jactive']='Allowed';
						}else{
						$dexp[$key]['jactive']='Denied';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Schedule Test ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/schedule/printsche', $data);
		
	}
	
	public function printpresencelist(){
	if ($this->input->get('id')<>null){	
		$dtcol=['jdate','tname','jsesi','jroom','uname'];
		$dexp = $this->Msche->datasche($dtcol,null,null,array('jdwl_tes.idjdwl'=>$this->input->get('id')));
		$Colmem = ['uname','unim','uhp'];
		$dmem = $this->Msche->populatemember($Colmem,$this->input->get('id'));

		//fetch data	
				$a=0;
				$hdetail=$this->returncolomn($dtcol);
				foreach($dexp[0] as $key=>$val){
					//manipulation allow data
					$dtdetail[$a] = array(
					"dtcol"=>'<b>'.$hdetail[$a].'</b>',
					"dtval"=>' : '.$val
					);
					$a++;
				}
				foreach($dmem as $k=>$v)
				{
				array_unshift($dmem[$k],($k+1));
				(($k%2)==0) ? $dmem[$k]['sign']=($k+1):$dmem[$k]['sign']='<div class="col-xs-offset-6">'.($k+1).'</div>';
				}
				
		$data['detailschedule'] = $this->table->generate($dtdetail).'<hr/>';
		$this->table->clear();
		
		// config table
		$header = ['#','Full Name','NIM', 'Phone Number','Signature'];
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$data['printlistlogin'] = $this->table->generate($dmem);
		$this->session->set_flashdata('v',"Print Presence List Success");
		$this->active();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Presence List of ".$dexp[0]['tname'].' '.$period." Period";
		$this->load->view('dashboard/org/schedule/printpresence', $data);
	} else {
		redirect(base_url('Organizer/Schedule/active'));
	}
		
	}
	
	
	public function removeMember(){
		$id=$this->input->get('id');
	}
	
	public function activate(){
		$id = $this->input->get('id');
		$r = $this->Msche->activate($id,array('jactive'=>'1','jstart'=>date('Y-m-d H:i:s')));
	if ($r){
		$this->session->set_flashdata('v','Activate Schedule Success');
		} else{
		$this->session->set_flashdata('x','Activate Schedule Failed');
		} 
		redirect(base_url('Organizer/Schedule/active'));
	}
	
	public function deactivate(){
		$id = $this->input->get('id');
		$r = $this->Msche->activate($id,array('jactive'=>'0','jstart'=>null));
	if ($r){
		$this->session->set_flashdata('v','Deactivate Schedule Success');
		} else{
		$this->session->set_flashdata('x','Deactivate Schedule Failed');
		} 
		redirect(base_url('Organizer/Schedule/active'));
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fschephase')){
			$dtrange = $this->input->post('fschephase');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
		$dtset=array(
				'beginschedule'=>$dtstart,
				'endschedule'=>$dtend
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Schedule Confirmation Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Schedule Confirmation Phase Failed.");
		}
		redirect(base_url('Organizer/Schedule'));
	}
	
	public function returncolomn($header) {
	$find=['idjdwl','jdate','jstart','tname','jdwl_tes.idtest','jsesi','jroom','jquota','jactive','uname'];
	$replace = ['Schedule ID','Schedule Date','Schedule Activated','Test Name','Test Name','Session','Room','Quota','Status','Last Updated by'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
