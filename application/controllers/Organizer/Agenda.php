<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agenda extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Magn','Msetting'));
    }

	public function index(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idagenda','agcreated','agdate','agtime','agtitle','agplace','agdescript','uname'];
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
			$rows = $this->Magn->countagn($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Magn->countagn();	
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
						'agcreated' => 'Agenda Created',
						'agtitle' => 'Agenda Title',
						'agdate' => 'Agenda Date',
						'agtime' => 'Agenda Time',
						'agplace' => 'Place',
						'agdescript' => 'Description',
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
		$adv['Agenda Created'] = form_input(
						array('name'=>'agcreated',
						'id'=>'createdon',
						'placeholder'=>'Agenda Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['agcreated']) ? $tempfilter['agcreated'] : null,
						'class'=>'form-control'));
		
		$adv['Agenda Title'] = form_input(
						array('name'=>'agtitle',
						'id'=>'agtitle',
						'placeholder'=>'Agenda Title',
						'value'=>isset($tempfilter['agtitle']) ? $tempfilter['agtitle'] : null,
						'class'=>'form-control'));
		
		$adv['Agenda Date'] = form_input(
						array('name'=>'agdate',
						'id'=>'agdate',
						'placeholder'=>'Agenda Date (YYYY-MM-DD)',
						'value'=>isset($tempfilter['agdate']) ? $tempfilter['agdate'] : null,
						'class'=>'form-control'));
		
		$adv['Agenda Time'] = form_input(
						array('name'=>'agtime',
						'id'=>'agtime',
						'placeholder'=>'Agenda Time (hh:mm)',
						'value'=>isset($tempfilter['agtime']) ? $tempfilter['agtime'] : null,
						'class'=>'form-control'));
		
		$adv['Place'] = form_input(
						array('name'=>'agplace',
						'id'=>'agplace',
						'placeholder'=>'Agenda Place',
						'value'=>isset($tempfilter['agplace']) ? $tempfilter['agplace'] : null,
						'class'=>'form-control'));
				
		
		$adv['Description'] = form_input(
						array('name'=>'agdescript',
						'id'=>'agdescript',
						'placeholder'=>'Description',
						'size'=>'45',
						'value'=>isset($tempfilter['agdescript']) ? $tempfilter['agdescript'] : null,
						'class'=>'form-control'));
		
		$adv['Notes'] = form_input(
						array('name'=>'agnotes',
						'id'=>'agnotes',
						'placeholder'=>'Notes',
						'size'=>'45',
						'value'=>isset($tempfilter['agnotes']) ? $tempfilter['agnotes'] : null,
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
				'base_url' => base_url().'/Organizer/Agenda?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Agenda?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Magn->dataagn($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'checkbox',
							'value'=>$value['idagenda']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text 
					
				$temp[$key]['agdescript'] .='<span class="idname hidden">'.$value['agtitle'].'</span>';
				$temp[$key]['agcreated']=date('d-M-Y', strtotime($value['agcreated'])).'<br/>'.date('H:i:s', strtotime($value['agcreated']));
				$temp[$key]['agdate']=date('d-M-Y', strtotime($value['agdate']));
				$temp[$key]['agdescript']= (strlen($value['agdescript']) > 25 ) ? mb_substr($value['agdescript'],0,25).'.....' : $value['agdescript'] ;
				//manipulation menu
				$enc = $value['idagenda'];
				unset($temp[$key]['idagenda']);
				$temp[$key]['agtitle']='<a href="'.base_url('Organizer/Agenda/readagenda?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Agenda"><b>'.$value['agtitle'].'</b></a>';
				$temp[$key]['menu']='<div class="btn-group" aria-label="Template Menu" role="group"><a href="'.base_url('Organizer/Agenda/readagenda?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Read"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Agenda/editagenda?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Agenda/deleteagenda?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
				
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
			$data['factselected'] = site_url('Organizer/Agenda/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Agenda/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'agcreated' => 'Agenda Created',
						'agtitle' => 'Agenda Title',
						'agdate' => 'Agenda Date',
						'agtime' => 'Agenda Time',
						'agplace' => 'Place',
						'agdescript' => 'Description',
						'agnotes' => 'Notes',
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
			$data['factexp'] = site_url('Organizer/Agenda/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Agenda/printtmp');
		
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
			$data['fsendper'] = site_url('Organizer/Agenda/savesetting');
				
			
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','summernote/summernote','numeric/numeric.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Agenda";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/agenda/agnlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addagenda(){
	$id=$this->input->get('id');
	$colq=['agtitle','agdate','agtime','agplace','agdescript','agnotes'];
	//============ form edit quest ===========
		$fagtit = array('name'=>'fagtit',
						'id'=>'agtit',
						'required'=>'required',
						'placeholder'=>'Agenda Title',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fagtit);
		
		$fagdate = array('name'=>'fagdate',
						'id'=>'agdate',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'datamask' => '',
						'required'=>'required',
						'placeholder'=>'Agenda Date',
						'value'=>'');
		$r[] = form_input($fagdate);
		
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
						
		$r[] = 'Agenda Start (Hour) :'.form_dropdown($fjHstart,$optH).' (Min) :'.form_dropdown($fjMstart,$optM).'<hr/> Agenda End (Hour) :'.form_dropdown($fjHend,$optH).' (Min) :'.form_dropdown($fjMend,$optM);
		
		
		$fagplc = array('name'=>'fagplc',
						'id'=>'agplc',
						'required'=>'required',
						'placeholder'=>'Agenda Place',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fagplc);
		
		$fagdes = array('name'=>'fagdes',
						'id'=>'agdes',
						'required'=>'required',
						'placeholder'=>'Agenda Description Details',
						'value'=>'',
						'rows'=>'5',
						'cols'=>'82',
						'class'=>'form-control');
		$r[] = form_textarea($fagdes);
		
		$fagnot = array('name'=>'fagnot',
						'id'=>'agnot',
						'required'=>'required',
						'placeholder'=>'Agenda Notes',
						'value'=>'',
						'rows'=>'5',
						'cols'=>'82',
						'class'=>'form-control');
		$r[] = form_textarea($fagnot);
		
		$fsend = array(	'id'=>'addatcl',
						'value'=>'Add Agenda',
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
		
		$this->load->view('dashboard/org/Agenda/addagn', $data);
	}
	
	public function editagenda(){
	//fecth data from db
		$col=['agtitle','agdate','agtime','agplace','agdescript','agnotes'];
		$id = $this->input->get('id');
		$dbres = $this->Magn->detailagn($col,$id);
		$this->load->library('Converttime');
		$colq = $this->returncolomn($col);
	//============ form edit quest ===========
		$fagtit = array('name'=>'fagtit',
						'id'=>'agtit',
						'required'=>'required',
						'placeholder'=>'Agenda Title',
						'value'=>$dbres[0]['agtitle'],
						'class'=>'form-control');
		$r[] = form_input($fagtit);
		
		$fagdate = array('name'=>'fagdate',
						'id'=>'agdate',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'value' => date('d-m-Y', strtotime($dbres[0]['agdate'])),
						'required'=>'required',
						'placeholder'=>'Agenda Date');
		$r[] = form_input($fagdate);
		
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
						
		$r[] = 'Agenda Start (Hour) :'.form_dropdown($fjHstart,$optH,mb_substr($dbres[0]['agtime'],0,2)).' (Min) :'.form_dropdown($fjMstart,$optM,mb_substr($dbres[0]['agtime'],3,2)).'<hr/> Agenda End (Hour) :'.form_dropdown($fjHend,$optH,mb_substr($dbres[0]['agtime'],8,2)).' (Min) :'.form_dropdown($fjMend,$optM,mb_substr($dbres[0]['agtime'],11,2));
		
		
		$fagplc = array('name'=>'fagplc',
						'id'=>'agplc',
						'required'=>'required',
						'placeholder'=>'Agenda Place',
						'value'=>$dbres[0]['agplace'],
						'class'=>'form-control');
		$r[] = form_input($fagplc);
		
		$fagdes = array('name'=>'fagdes',
						'id'=>'agdes',
						'required'=>'required',
						'placeholder'=>'Agenda Description Details',
						'value'=>$dbres[0]['agdescript'],
						'rows'=>'5',
						'cols'=>'82',
						'class'=>'form-control');
		$r[] = form_textarea($fagdes);
		
		$fagnot = array('name'=>'fagnot',
						'id'=>'agnot',
						'required'=>'required',
						'placeholder'=>'Agenda Notes',
						'value'=>$dbres[0]['agnotes'],
						'rows'=>'5',
						'cols'=>'82',
						'class'=>'form-control');
		$r[] = form_textarea($fagnot);
		
		
		$fsend = array(	'id'=>'updateatcl',
						'value'=>'Update Agenda',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inid'] = form_hidden('fid',$id);
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($colq);
		//set table article
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
		
		$this->load->view('dashboard/org/Agenda/editagn', $data);
	}
	
	public function readagenda(){
		//fecth data from db
		$col = ['agcreated','agtitle','agdate','agtime','agplace','agdescript','agnotes'];
		$id = $this->input->get('id');
		$dbres = $this->Magn->detailagn($col,$id);
		$this->load->library('Converttime');
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
		$dbres[0]['agcreated'] = date('d-M-Y H:i:s', strtotime($dbres[0]['agcreated']));
		$dbres[0]['agdate'] = date('d-M-Y', strtotime($dbres[0]['agdate']));
		
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<b>'.$key.'</b>',
					"dtval"=>' : '.$dbres[0][$col[$a]]
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/Agenda/readagn', $data);
		
		
	}
	
	public function deleteagenda(){
		$id = $this->input->get('id');
		$r = $this->Magn->deleteagn($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Agenda Success');
		} else{
		$this->session->set_flashdata('x','Delete Agenda Failed');
		} 
		redirect(base_url('Organizer/Agenda'));
	}
	
	public function saveagn(){
				// set new data variable
				$fdata = array(
					'agcreated'=>date('Y-m-d H:i:s'),
					'agtitle'=>$this->input->post('fagtit'),
					'agplace'=>$this->input->post('fagplc'),
					'agdescript'=>$this->input->post('fagdes'),
					'agdate'=>date('Y-m-d',strtotime($this->input->post('fagdate'))),
					'agtime'=>$this->input->post('Hs').':'.$this->input->post('Ms').' - '.$this->input->post('Hend').':'.$this->input->post('Mend'),
					'agnotes'=>$this->input->post('fagnot'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Magn->saveagn($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Agenda Succes.') : $this->session->set_flashdata('x','Add Agenda Failed.');
			
		redirect(base_url('Organizer/Agenda'));
		
	}
	
	public function updateagn(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'agtitle'=>$this->input->post('fagtit'),
					'agplace'=>$this->input->post('fagplc'),
					'agdescript'=>$this->input->post('fagdes'),
					'agdate'=>date('Y-m-d',strtotime($this->input->post('fagdate'))),
					'agtime'=>$this->input->post('Hs').':'.$this->input->post('Ms').' - '.$this->input->post('Hend').':'.$this->input->post('Mend'),
					'agnotes'=>$this->input->post('fagnot'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Magn->updateagn($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Agenda Succes.') : $this->session->set_flashdata('x','Update Agenda Failed.');
			
		redirect(base_url('Organizer/Agenda'));
		
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatAgenda.xls') ){
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
			
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
				   $dtxl[$i-1]['agcreated'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['agtitle'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['agdate'] = date("Y-m-d",strtotime($objWorksheet->getCell('B'.($i+1))->getValue()));
                   $dtxl[$i-1]['agtime'] = date("H:i",strtotime($objWorksheet->getCell('C'.($i+1))->getValue())).' - '.date("H:i",strtotime($objWorksheet->getCell('D'.($i+1))->getValue()));
                   $dtxl[$i-1]['agplace'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['agdescript'] = $objWorksheet->getCell('F'.($i+1))->getValue();
                   $dtxl[$i-1]['agnotes'] = $objWorksheet->getCell('G'.($i+1))->getValue();
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
				 
				 }
              }
			  
			  //save data through model
			  $report = $this->Magn->agnimportdata($dtxl);
 
              
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
		redirect(base_url('Organizer/Agenda'));
        
    }
		
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['agcreated','agtitle','agdate','agtime','agplace','agdescript','agnotes']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Magn->exportagn($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Magn->exportagn(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Agenda Data');
	
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
						($k == 'agcreated') ?	$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,date("d-M-Y H:i:s",strtotime($v))) : null;
						($k == 'agdate') ?	$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow, date("d-M-Y ",strtotime($v))) : null;
						
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'AGENDA DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Agenda Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Agenda Title','Agenda Date(dd-mm-yyyy)','Begin Time(hh:mm)','End Time(hh:mm)','Place','Description','Notes']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatAgenda');
		
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
		
		$Dcol = chr(ord($Hcol)+1);
	
		$Hrow= 2;
		$tempcol = $Dcol;
		$rowrole=$Hrow;
		
		//set autowidth
		foreach(range('A',$tempcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Dcol)-2).'10')->applyFromArray($styleArray);
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Dcol)-1).($Hrow+8));
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatAgenda.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function printtmp(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['agcreated','agtitle','agdate','agtime','agplace','agdescript','agnotes'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Magn->exportagn($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Magn->exportagn(null,null,$dtcol);
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
					(array_key_exists('agcreated',$val)) ? $dexp[$key]['agcreated']=date('d-M-Y',strtotime($val['agcreated'])).'<br/>'.date('H:i:s',strtotime($val['agcreated'])): null;
					(array_key_exists('agdate',$val)) ? $dexp[$key]['agdate']=date('d-M-Y',strtotime($val['agdate'])): null;
					
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Agenda Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/Agenda/printagn', $data);
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Magn->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Failed.');
		}
		redirect(base_url('Organizer/Agenda'));
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
	$find=['agcreated','agdate','agtime','agtitle','agplace','agdescript','agnotes','uname'];
	$replace = ['Created', 'Agenda Date','Agenda Time','Title','Place','Description','Notes','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
