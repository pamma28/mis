<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Msubject','Mtest','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['tcreated','tname','test.idtest as idt','tduration','tktrgn'];
		$header = $this->returncolomn($column);
		$header[2]='Subject List Details';
		
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
				'base_url' => base_url().'/Organizer/Subject?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Subject?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mtest->datatest($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//save idtest
				$enc = $value['idt'];
				$temp[$key]['tduration']=$temp[$key]['tduration'].' minute(s)';
				$temp[$key]['tcreated']=date('d-M-Y', strtotime($value['tcreated'])).'<br/>'.date('H:i:s', strtotime($value['tcreated']));
				$temp[$key]['tname']='<span class="idname">'.$temp[$key]['tname'].'</span>';
				
					// loop subject list
					$colsbjct = ['subject','qtot','qpercent'];
					$dtsbjct = $this->Msubject->datasbjctbyid($colsbjct,$enc);
					$rsb= array();$sno=1;
					foreach($dtsbjct as $sk=>$sv){
						$rsb[]=$sno.'. '.$sv['subject'].' ['.$sv['qtot'].' Question(s) - '.$sv['qpercent'].'%]';
						$sno++;
					}
					$temp[$key]['idt']=implode('<br/>',$rsb);
				//manipulation menu
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Subject/detailsubjecttest?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a>'.
				'<a href="'.base_url('Organizer/Subject/editsubjecttest?id=').$enc.'" alt="Edit Data" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit Subject List</a></div>';
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
			$data['factselected'] = site_url('Organizer/Subject/updateselected');
			
		
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
			$data['fsendper'] = site_url('Organizer/Subject/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Subject Test";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/subject/subjectlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function allsubject(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idsubject','subject'];
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
			$rows = $this->Msubject->countsubject($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Msubject->countsubject();	
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
						'subject' => 'Subject'
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
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'Organizer/Question/questiontype?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Question/questiontype?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Msubject->datasubject($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idsubject']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['subject']='<span class="idname">'.$temp[$key]['subject'].'</span>';
				//manipulation menu
				$enc = $value['idsubject'];
				unset($temp[$key]['idsubject']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Subject/editsubject?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Subject/deletesubject?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
				}
		$data['listdata'] = $this->table->generate($temp);
		
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
			$data['factselected'] = site_url('Organizer/Subject/updateselected');

	
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Subject Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/subject/allsubjectlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	
	public function detailsubjecttest(){
		//fecth data from db
		$col=['tcreated','tlupdate','tname','idtest','tduration','tktrgn','a.uname as creator','b.uname as editor'];
		$id = $this->input->get('id');
		$dbres = $this->Msubject->detailtest($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		$row[3]='Subject List Details';
		$col[6]='creator';
		$col[7]='editor';
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
			if (($key=='Subject List Details')){
					// loop detail test with subject
					$colsbjct = ['subject','qtot','qpercent'];
					$dtsbjct = $this->Msubject->datasbjctbyid($colsbjct,$id);
					$rsb[]= '<tr><td><b>No</b></td><td><b>Subject</b></td><td><b>Total Question</b></td><td><b>Assessment Percentage</b></td></tr>';
					$sno=1;
					foreach($dtsbjct as $sk=>$sv){
						$rsb[]='<tr><td>'.$sno.'</td><td>'.$sv['subject'].'</td><td>'.$sv['qtot'].' Question(s)</td><td>'.$sv['qpercent'].'%</td></tr>';
						$sno++;
					}					
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>'<table class="table">'.implode('',$rsb).'</table>'
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/subject/detailsubject', $data);
		
		
	}
	
	public function addsubjecttest(){
		$t = $this->input->get('t');
		//============ form add subject on certain test ===========
			$optsbjct = $this->Msubject->optsbjctavail($t);	
		$fidsbjct = array('name'=>'fsbjct',
						'id'=>'fsbjct',
						'required'=>'required',
						'placeholder'=>'',
						'value'=>'',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[] = form_dropdown($fidsbjct,$optsbjct,'');
		$fqtot = array('name'=>'fqtot',
						'id'=>'fqtot',
						'required'=>'required',
						'placeholder'=>'Total Question',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fqtot);
		
		$fqper = array('name'=>'fqper',
						'id'=>'fqper',
						'placeholder'=>'Assessment Percentage',
						'required'=>'required',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fqper);
		
		$fsend = array(	'id'=>'addsubject',
						'value'=>'Add',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
			$tot = $this->Msubject->gettotpercent($t);
		$data['intot'] = form_hidden('ftot',$tot);
		$data['inid']= form_hidden('fid',$t);
		
		//set row title
		$col = ['idsubject','qtot','qpercent'];
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
		
		$this->load->view('dashboard/org/subject/addsubjecttest', $data);
	}
	
	public function addsubject(){
		$url=$this->input->get('url');
		//============ form add subject ===========
		$fsub = array('name'=>'fsub',
						'id'=>'fsubject',
						'required'=>'required',
						'placeholder'=>'Subject Name',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fsub);
		
		$fsend = array(	'id'=>'addsubject',
						'value'=>'Add Subject',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_hidden('furl',$url).form_submit($fsend);
		
		//set row title
		$col = ['Subject'];
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
		
		$this->load->view('dashboard/org/subject/addsubject', $data);
	}
	
	public function editsubjecttest(){					
		if ($this->input->get('id')!= null)
		{
		// ============== Fetch data test & subject ============			
		$col=['tcreated','tname','tduration','tktrgn','idtest'];
		$id = $this->input->get('id');
		$g = $this->Msubject->detailtest($col,$id);
			$colsbjct = ['qsort','quo_sbjct.idsubject','subject','qtot','qpercent'];
			$dtsbjct = $this->Msubject->datasbjctbyid($colsbjct,$id);
		// ========= form edit ================ 
		$r[] = '<div class="info-box bg-navy">
                <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Test Created</span>
                  <span class="info-box-number">'.$g[0]['tcreated'].'</span>
                </div>
              </div>';
		$r[] = '<div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Test Name</span>
                  <span class="info-box-number">'.$g[0]['tname'].'</span>
                </div>
              </div>';
		$r[] = '<div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Test Duration</span>
                  <span class="info-box-number">'.$g[0]['tduration'].' Minute(s)</span>
                </div>
              </div>';
		$r[] = '<div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-sticky-note"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Test Notes</span>
                  <span class="info-box-number">'.$g[0]['tktrgn'].'</span>
                </div>
              </div>';
				
			
		$fqtot = array('name'=>'fqtot[]',
						'id'=>'fqto[]',
						'required'=>'required',
						'placeholder'=>'Total Question',
						'value'=>'',
						'class'=>'form-control');
		
		$fqper = array('name'=>'fqper[]',
						'id'=>'fqper[]',
						'placeholder'=>'Assessment Percentage',
						'required'=>'required',
						'value'=>'',
						'class'=>'form-control');
			$pilsort = array('' => ' - ');
			$totpil = count(array_column($dtsbjct, 'qsort'));
			for($i=1;$i<=$totpil;$i++) {
				$pilsort[$i] = $i;
			}
		$optsort = array(
						'name'=>'fsort[]',
						'id'=>'fsort[]',
						'required'=>'required',
						'class'=>'form-control'
					);
						
		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		// generate subject stored
		$rsb[]= '<tr><td><b>Sort</b></td><td><b>Subject</b></td><td><b>Total Question</b></td><td><b>Assessment Percentage</b></td><td><b>Menu</b></td></tr>';
			foreach($dtsbjct as $sk=>$sv){
					$fqtot['value']=$sv['qtot'];
					$fqper['value']=$sv['qpercent'];
					$link= base_url('Organizer/Subject/deletesubjecttest?s='.$sv['idsubject'].'&t='.$id);
					$rsb[]='<tr><td><label>'.form_dropdown($optsort,$pilsort,$sv['qsort']).'</label></td><td><label>'.$sv['subject'].form_hidden('fsub[]',$sv['idsubject']).'</label></td><td>'.form_input($fqtot).'</td><td>'.form_input($fqper).'</td><td><a href="#" data-href="'.$link.'" class="btn btn-danger btn-sm" data-target="#confirm-delete" data-toggle="modal" alt="Delete Data" title="Delete"><i class="fa fa-trash"></i></a></td></tr>';
					}
		$bsub = '<a href="'.base_url('Organizer/Subject/addsubject').'" data-target="#DetailModal" data-toggle="modal" role="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Subject Name</a>';
		$badd = '<a href="'.base_url("Organizer/Subject/addsubjecttest?t=".$id).'" id="btnAdd" data-tot="" data-target="#DetailModal" data-toggle="modal" role="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Subject In this Test</a>';						
		$r[]=$bsub.' '.$badd.'<table class="table table-bordered">'.implode('',$rsb).'</table>';
		
		//set row title
		$row = $this->returncolomn($col);
		$row[4]='Subject List';
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
					"dtcol"=>'',
					"dtval"=>'<div class="col-sm-3">'.$r[$a].'</div>'
					);
			if ($key=='Subject List'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="form-group col-sm-12 text-center"><h4 for="l'.$key.'"><b>'.$key.'</b></h4></div>',
					"dtval"=>'<div class="col-sm-12">'.$r[$a].'</div>'
					);
			}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Edit Subject Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/subject/editsubjecttest', $data, TRUE);
		$this->load->view ('template/main', $data);
		} else {
		$this->session->set_flashdata('x','Cannot Directly Access, Choose One of the Test.');
		redirect(base_url('Organizer/Subject'));
		}
	
	}
	
	public function editsubject(){
		$id=$this->input->get('id');
		$colq=['subject'];
		$g = $this->Msubject->detailsubject($colq,$id);
		//============ form edit quest ===========
		$fsub = array('name'=>'fsubject',
						'id'=>'fsubject',
						'required'=>'required',
						'placeholder'=>'Subject',
						'value'=>$g[0]['subject'],
						'class'=>'form-control');
		$r[]=form_input($fsub);
						
		$fsend = array(	'id'=>'updatesubject',
						'value'=>'Update Subject',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidsub',$id);
		
		//set row title
		$row = $this->returncolomn($colq);
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
		
		$this->load->view('dashboard/org/subject/editsubject', $data);
	}
	
	public function updatesubjecttest(){
		$tot=null;
		if (($this->input->post('fid')!=null) and ($this->input->post('fsub[]')!=null)){
		$id = $this->input->post('fid');
			// catch each variable
			$tot = count($this->input->post('fsub[]'));
			$v = 0; $x=0;
			foreach($this->input->post('fsub[]') as $k=>$sub){
				$sub = $this->input->post('fsub[]')[$k];
				$fdata= array(
						'idtest'=>$id,
						'idsubject'=>$sub,
						'qsort' => $this->input->post('fsort[]')[$k],
						'qtot'=>$this->input->post('fqtot[]')[$k],
						'qpercent'=>$this->input->post('fqper[]')[$k]
						);
				$hsl = $this->Msubject->updatesubjecttest($fdata,$id,$sub);
				($hsl) ? $v++:$x++;
			}
		
		}
		if ($tot!=null){
		$this->session->set_flashdata('v','Update '.$tot.' Subject Success. Details: '.$v.' succcess and '.$x.' failed.');
		} else {		
		$this->session->set_flashdata('x','Update Subject Test Failed. No Subject or Test to be updated.');
		}
		($id!=null) ? $url = '/editsubjecttest?id='.$id : $url=null;
		redirect(base_url('Organizer/Subject'.$url));
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			if ($type == '0') {
				$s=0;$x=0;
				foreach ($dtuser as $v) {
					$res= $this->Msubject->deletesubject($v);
					($res) ? $s++:$x++;
				}
			}
				
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Subject Data Success.<br/>Details: '.$s.' Success and '.$x.' Error(s)');
		} else{
		$this->session->set_flashdata('x','No Data Selected, Update Selected Subject Data Failed.');
		}
		redirect(base_url('Organizer/Subject/allsubject'));
	}
		
	public function savesubjecttest(){
		if ($this->input->post('fid')!=null){
		$id = $this->input->post('fid');
		$fdata = array (
					'idsubject' => $this->input->post('fsbjct'),
					'idtest' => $id,
					'qsort' => $this->Msubject->maxsort($id)+1,
					'qtot' => $this->input->post('fqtot'),
					'qpercent' => $this->input->post('fqper')
					);
		$r = $this->Msubject->addsubjecttest($fdata);
		}
		if ($r){
		$this->session->set_flashdata('v','Add Subject Test Success');
		} else {		
		$this->session->set_flashdata('x','Add Subject Test Failed');
		}
		redirect(base_url('Organizer/Subject/editsubjecttest?id='.$id));
	
	}
	
	public function savesubject(){
		$sname = $this->input->post('fsub');
		$fdata = array (
					'subject' => $sname
					);
		$r = $this->Msubject->addsubject($fdata);
		if ($r){
		$this->session->set_flashdata('v','Add Subject Success');
		} else {		
		$this->session->set_flashdata('x','Add Subject Failed');
		}
		($this->input->post('furl')!=null) ? $url=$this->input->post('furl') : $url=base_url('Organizer/Subject');
		redirect($url);
	
	}
	
	public function deletesubjecttest(){
		$s = $this->input->get('s');
		$t = $this->input->get('t');
		$r = $this->Msubject->deletesubjecttest($s,$t);
	if ($r){
		$this->session->set_flashdata('v','Delete Subject Test Success');
		} else{
		$this->session->set_flashdata('x','Delete Subject Test Failed');
		} 
		redirect(base_url('Organizer/Subject/editsubjecttest?id='.$t));
	}

	public function deletesubject(){
		$id = $this->input->get('id');
		$r = $this->Msubject->deletesubject($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Subject Success');
		} else{
		$this->session->set_flashdata('x','Delete Subject Failed');
		} 
		redirect(base_url('Organizer/Subject/allsubject'));
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
		redirect(base_url('Organizer/Subject'));
	}
	
	public function returncolomn($header) {
	$find=['idtest','tlupdate','tcreated','tname','tduration','tktrgn','a.uname as creator','b.uname as editor','a.uuser','idsubject','qtot','qpercent','subject'];
	$replace = ['Test ID','Last Updated','Test Created','Test Name','Test Duration','Test Notes','Created by','Last Edited by','Created by','Subject Test','Total Question','Assessment Percentage','Subject'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}

}
