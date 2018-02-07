<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mtmp','Msetting'));
    }

	public function index(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idtmplte','tmpdate','tmpname','tmpcontent','uname'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		//checkbox checkalldata
		$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'btn btn-default btn-sm',
							'value'=>'all',
							'id'=>'c_all'
							));	
		array_unshift($header,$checkall);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
			
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 10;
		$perpage= isset($tempfilter['page']) ? $tempfilter['page'] : 1;
		unset($filter['code']);
		
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
			$rows = $this->Mtmp->counttmp($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mtmp->counttmp();	
		}
		//================ filter handler ================
		$fq = array('name'=>'search',
						'id'=>'search',
						'required'=>'required',
						'placeholder'=>'Search Here',
						'value'=> isset($tempfilter['search']) ? $tempfilter['search'] : null ,
						'class'=>'form-control input-xs');
		$data['inq'] = form_input($fq);
			$optf = array(
						'tmpdate' => 'Template Created',
						'tmpname' => 'Template Name',
						'bccontent' => 'Template Content',
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
		$adv['Template Created'] = form_input(
						array('name'=>'tmpdate',
						'id'=>'createdon',
						'placeholder'=>'Template Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['tmpdate']) ? $tempfilter['tmpdate'] : null,
						'class'=>'form-control'));
		
		$adv['Template Name'] = form_input(
						array('name'=>'tmpname',
						'id'=>'tmptitle',
						'placeholder'=>'Template Name',
						'value'=>isset($tempfilter['tmpname']) ? $tempfilter['tmpname'] : null,
						'class'=>'form-control'));
							
		$adv['Template Content'] = form_input(
						array('name'=>'tmpcontent',
						'id'=>'tmpcontent',
						'placeholder'=>'Template Content',
						'size'=>'45',
						'value'=>isset($tempfilter['tmpcontent']) ? $tempfilter['tmpcontent'] : null,
						'class'=>'form-control'));
		
		$adv['PIC'] = form_input(
						array('name'=>'uname',
						'id'=>'picname',
						'placeholder'=>'PIC',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Template?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Template?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Mtmp->datatmp($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'checkbox',
							'value'=>$value['idtmplte']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text 
					$txt = strip_tags(html_entity_decode($value['tmpcontent']));
					(strlen($txt)>30) ? $tmptext = mb_substr($txt,0,30).'...':$tmptext=$txt;
				
				$temp[$key]['tmpcontent']='<span class="idname hidden">'.$value['tmpname'].'</span>'.$tmptext;
				$temp[$key]['tmpdate']=date('d-M-Y', strtotime($value['tmpdate'])).'<br/>'.date('H:i:s', strtotime($value['tmpdate']));
				
				//manipulation menu
				$enc = $value['idtmplte'];
				unset($temp[$key]['idtmplte']);
				$temp[$key]['tmpname']='<a href="'.base_url('Organizer/Template/readtmp?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Template Content"><b>'.$value['tmpname'].'</b></a>';
				$temp[$key]['menu']='<div class="btn-group" aria-label="Template Menu" role="group"><a href="'.base_url('Organizer/Template/readtmp?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Read"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Template/edittemplate?id=').$enc.'" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Template/deletetmp?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
				
				}
		
		$data['listlogin'] = $this->table->generate($temp);
		
		// ======== Delete multiple ==============
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
			$data['factselected'] = site_url('Organizer/Template/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Template/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'tmpdate' => 'Template Created',
						'tmpname' => 'Template Name',
						'tmpcontent' => 'Template Content',
						'uname' => 'PIC'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol input-xs',
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
			$data['fbtnexport']= form_submit(array('value'=>'Export',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factexp'] = site_url('Organizer/Template/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Template/printtmp');
		
		//=============== setting ============
			$smsfoo = $this->Msetting->getset('smsfooter');
			
			$data['fooeditor']= form_textarea(array(
							'name'=>'feditfoo',
							'id'=>'editfoo',
							'cols'=>'148',
							'rows'=>'2',
							'class'=>'form-control',
							'value'=>$smsfoo)
							);
			$data['fbtnupdate']= form_submit(array('value'=>'Update Footer',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Template/savesetting');
				
			
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','summernote/summernote');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Template Content";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/template/tmplist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addtemplate(){
	$id=$this->input->get('id');
	$colq=['tmpname','tmpcontent'];
	//============ form edit quest ===========
		$ftmpname =  array('name'=>'ftmpnamecon',
						'id'=>'ftmpname',
						'required'=>'required',
						'placeholder'=>'Template Name',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($ftmpname);
		
		$fcont = array('name'=>'fcont',
						'id'=>'fcont',
						'required'=>'required',
						'placeholder'=>'Template Content',
						'value'=>'',
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
		$fsend = array(	'id'=>'addtmp',
						'value'=>'Add Template',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fid',$id);
		
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

		//=============== Template ============
		$data['jsFiles'] = array(
							'summernote/summernote');
		$data['cssFiles'] = array(
							'summernote/summernote');  
		// =============== view handler ============
		$data['title']="Add Template";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/template/addtmp', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function edittemplate(){
	//fecth data from db
		$col = ['tmpdate','tmpname','tmpcontent','uname'];
		$id = $this->input->get('id');
		$dbres = $this->Mtmp->detailtmp($col,$id);
		$this->load->library('Converttime');
		
	$colq=['tmpname','tmpcontent'];
	//============ form edit quest ===========
		$ftmpname =  array('name'=>'ftmpname',
						'id'=>'ftmpname',
						'required'=>'required',
						'placeholder'=>'Template Name',
						'value'=>$dbres[0]['tmpname'],
						'class'=>'form-control');
		$r[]=form_input($ftmpname);
		
		$fcont = array('name'=>'fcont',
						'id'=>'feditcont',
						'required'=>'required',
						'placeholder'=>'Template Content',
						'value'=>html_entity_decode($dbres[0]['tmpcontent']),
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
		$fsend = array(	'id'=>'updatetmp',
						'value'=>'Update Template',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inid'] = form_hidden('fid',$id);
		$data['inbtn'] = form_submit($fsend);
		
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
		//=============== Template ============
		$data['jsFiles'] = array(
							'summernote/summernote');
		$data['cssFiles'] = array(
							'summernote/summernote');
		// =============== view handler ============  
		$data['title']="Edit Template";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/template/edittmp', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function readtmp(){
		//fecth data from db
		$col = ['tmpdate','tmpcontent','uname','tmpname'];
		$id = $this->input->get('id');
		$dbres = $this->Mtmp->detailtmp($col,$id);
		$this->load->library('Converttime');
		//set row title
		$row = $this->returncolomn($col);
		unset($row[3]);
		unset($row[2]);
		//set table template
		$tmpl = array ( 'table_open'  => '<div>',
					'heading_row_start'   => '',
                    'heading_row_end'     => '',
                    'heading_cell_start'  => '',
                    'heading_cell_end'    => '',

                    'row_start'           => '',
                    'row_end'             => '',
                    'cell_start'          => '',
                    'cell_end'            => ''

					);
		$this->table->set_template($tmpl);
		//set table data
		$a = 0;
		$dbres[0]['tmpdate'] = date('d-M-Y H:i:s', strtotime($dbres[0]['tmpdate']));
		$dbres[0]['tmpcontent'] = html_entity_decode($dbres[0]['tmpcontent']);
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<div class="col-md-12"><span class="text-primary"><b>'.$key.' :</b></span><br/>',
				"dtval"=>$dbres[0][$col[$a]].'<hr/></div>'
				);
			if ($key=='Template Created') {
				$dtable[$a] = array(
				"dtcol"=>'<div class="col-md-12 bg-primary"><h3>'.$dbres[0]['tmpname'].'</h3>',
				"dtval"=>'<code class="mailbox-read-time"><i class="fa fa-clock-o"></i> '.$dbres[0][$col[$a]].'</code><code class="pull-right"><i class="fa fa-user"></i> PIC: '.$dbres[0]['uname'].'</code><hr/></div>'
				);
			}
			
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/template/readtmp', $data);
		
		
	}
	
	public function deletetmp(){
		$id = $this->input->get('id');
		$r = $this->Mtmp->deletetmp($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Template Content Success');
		} else{
		$this->session->set_flashdata('x','Delete Template Content Failed');
		} 
		redirect(base_url('Organizer/Template'));
	}
	
	public function savetmp(){
		$this->load->library('MY_input');
				// set new data variable
				$fdata = array(
					'tmpdate'=>date('Y-m-d H:i:s'),
					'tmpname'=>$this->input->post('ftmpnamecon'),
					'tmpcontent'=>htmlentities($this->input->post('fcont',false)),
					'uuser'=>$this->session->userdata('user'),
					'tmptype'=>'CONTENT'
					);
			//update to database
			$hsl = $this->Mtmp->savetmp($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Template Succes.') : $this->session->set_flashdata('x','Add Template Failed.');
			
		redirect(base_url('Organizer/Template'));
		
	}
	
	public function updatetmp(){
	$this->load->library('MY_input');
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'tmpname'=>$this->input->post('ftmpname'),
					'tmpcontent'=>htmlentities($this->input->post('fcont',false)),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mtmp->updatetmp($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Template Content Succes.') : $this->session->set_flashdata('x','Update Template Content Failed.');
			
		redirect(base_url('Organizer/Template'));
		
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatTemplate.xls') ){
                // if file validation failed, send error to view
                $error = ['file choosen is not match with pre-defined file',$this->upload->display_errors()];
				array_filter($error);
				$this->session->set_flashdata('x','Import Data Failed, details: '.implode(' ',$error));
            } else {
              // if upload success, take file data
              $upload_data = $this->upload->data();
			 
              // load library Excell_Reader
              $this->load->library('Excel');
			  $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);
			  $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
 
              // array data
			  $highestRow = $objWorksheet->getHighestRow();
			  $highestColumn = $objWorksheet->getHighestColumn();
              $dtxl = Array();
			  
			  //variable
			  $creditnorep = $this->Msetting->getset('saldosmsnotif');
			  $creditwithrep = $this->Msetting->getset('saldosmsbc');
			  $footer = $this->Msetting->getset('smsfooter');
			  
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
				 
				   $dtxl[$i-1]['tmpdate'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['tmpname'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['tmpcontent'] = htmlentities($objWorksheet->getCell('B'.($i+1))->getValue());
                   $dtxl[$i-1]['tmptype'] = "CONTENT";
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
				 
				 }
              }
			  
			  //save data through model
			  $report = $this->Mtmp->tmpimportdata($dtxl);
 
              
            //set flashdata
			$flashdata = 'Import '.$report['success'].' Data Success, with '.$report['failed'].' unsuccessful import.';
			if ($report['faillist']<>''){
				$flashdata = $flashdata."<br/>Data error: <br/>".$report['faillist'];
			}
			$this->session->set_flashdata('v',$flashdata);
        
			}
		//delete file
              $file = $this->upload->data()['file_name'];
              $path = FCPATH.'temp_upload/' . $file;
              unlink($path);
		//redirect to data list
		redirect(base_url('Organizer/Template'));
        
    }
		
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['tmpdate','tmpname','tmpcontent','uname']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mtmp->exporttmp($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mtmp->exporttmp(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Template Content Data');
	
		//Create Heading
		$Hcol = 'A';
		$Hrow = 2;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
				$Hcol++;    
		}
		
		//Insert Data
		$Drow = 3;
		$Dcol = "A";
		$ctot = count($dexp);
		if ((is_array($dexp) || is_object($dexp)) and($ctot<>0)){
			foreach($dexp as $key){
					$Dcol = "A";
				//manipulate data
					foreach ($key as $k=>$v){
						$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$v);
						($k == 'tmpcontent') ?	$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,strip_tags(html_entity_decode($v))) : null;
						
						$Dcol++;
					}
				$Drow++;
			}
		} else {
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,'No Data');
		$Drow=$Drow+1;
		}
		
		//set limit col and row
		$Dnewcol = chr(ord($Hcol)-1);
		$Dnewrow = $Drow-1;
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane('A3');
		
		//Create big Title
		$period = $this->Msetting->getset('period');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'TEMPLATE CONTENT DATA ('.$title.')');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$Dnewcol.'1');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
		//setting autowidth
		foreach(range('A',$Dnewcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.$Dnewcol.$Dnewrow)->applyFromArray($styleArray);
		
		//setting footprint date
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$Drow, 'Generated on '.Date("d-m-Y H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setItalic(true);
		
		//Save as an Excel BIFF (xls) file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Template Content Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Template Name','Template Content']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatTemplate');
		
		//Create Heading
		$Hcol = 'A';
		$Hrow = 1;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
		//create format column as text
		$objPHPExcel->getActiveSheet()->getStyle($Hcol.'1:'.$Hcol.'100')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$Hcol++;    
		}
		
		$Bordercol = chr(ord($Hcol)-1);
		
		
		//set autowidth
		foreach(range('A',$Bordercol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Hcol)-1).'10')->applyFromArray($styleArray);
		
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane($Hcol.($Hrow+10));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatTemplate.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function printtmp(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['tmpdate','tmpname','tmpcontent','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mtmp->exporttmp($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mtmp->exporttmp(null,null,$dtcol);
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
					(array_key_exists('tmpdate',$val)) ? $dexp[$key]['tmpdate']=date('d-M-Y',strtotime($val['tmpdate'])).'<br/>'.date('H:i:s',strtotime($val['tmpdate'])): null;
					(array_key_exists('tmpcontent',$val)) ? $dexp[$key]['tmpcontent']=strip_tags(html_entity_decode($val['tmpcontent'])) : null;
					
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Template Content Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/template/printtmp', $data);
		
	}
	
	public function gettmpdata(){
		$id= $this->input->post('idtmp');
		$data = $this->Mtmp->gettmpdata($id);
		echo json_encode($data);
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Mtmp->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Template Content success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Template Content Failed.');
		}
		redirect(base_url('Organizer/Template'));
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
	$find=['tmpdate','tmpname','tmpcontent','tmptype','uname'];
	$replace = ['Template Created', 'Template Name','Template Content','Template Type','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
