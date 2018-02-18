<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mnotif','Msetting'));
    }

	
	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idnotif','npublish','nicon','ncontent','uname'];
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
			$rows = $this->Mnotif->countnotif($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mnotif->countnotif();	
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
						'npublish' => 'Notification Created',
						'ncontent' => 'Notification',
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
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'Organizer/Notification?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Notification?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mnotif->datanotif($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idnotif']
							));
				array_unshift($temp[$key],$ctable);
				(strlen($temp[$key]['ncontent'])>30) ? $temp[$key]['ncontent'] = mb_substr($temp[$key]['ncontent'],0,30).'.....' : null;
				$temp[$key]['nicon'] = '<span class="fa '.$temp[$key]['nicon'].'"></span>';
				$temp[$key]['ncontent'] .= '<span class="idname hidden">'.mb_substr($value['ncontent'],0,10).'</span>'; 
				$temp[$key]['npublish']=date('d-M-Y', strtotime($value['npublish'])).'<br/>'.date('H:i:s', strtotime($value['npublish']));
					
				//manipulation menu
				$enc = $value['idnotif'];
				unset($temp[$key]['idnotif']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Notification/readnotif?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Data" class="btn btn-primary btn-sm" title="Read"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Notification/editnotif?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Notification/delnotif?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Notification/updateselected');
			
		//=============== Template ============
		$data['jsFiles'] = array('selectpicker/select.min'
							);
		$data['cssFiles'] = array('selectpicker/select.min'
							);  
		// =============== view handler ============
		$data['title']="Notification";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/notification/notiflist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addnotif(){
	$id=$this->input->get('id');
	$colq=['nicon','ncontent'];
	//============ form add notif ===========
		$opticon = array(
				"fa-home",
				"fa-key",
				"fa-legal",
				"fa-pencil",
				"fa-money",
				"fa-book",
				"fa-bullhorn",
				"fa-file-text",
				"fa-envelope-square",
				"fa-envelope",
				"fa-certificate",
				"fa-check-square-o",
				"fa-calendar",
				"fa-clock-o",
				"fa-edit",
				"fa-check",
				"fa-times"
				);
		$selecticon = '<select name="fnicon" id="fnicon" class="form-control selectpicker" required="required">';

		foreach ($opticon as $k => $v) {
			$selecticon .= '<option value="'.$v.'" data-icon="'.$v.'"></option>';
		}
		$selecticon .= "</select>";

		$r[] = $selecticon;
		$fncontent =  array('name'=>'fncontent',
						'id'=>'fncont',
						'required'=>'required',
						'placeholder'=>'Notification Content',
						'value'=>'',
						'rows'=>'5',
						'class'=>'form-control');
		$r[]=form_textarea($fncontent);
		
		$fsend = array(	'id'=>'addcat',
						'value'=>'Add Notification',
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
		
		$this->load->view('dashboard/org/notification/addnotif', $data);
	}
	
	public function readnotif(){
		//fecth data from db
		$col = ['ncontent'];
		$id = $this->input->get('id');
		$dbres = $this->Mnotif->detailnotif($col,$id);
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
		$this->load->view('dashboard/org/notification/readnotif', $data);
		
		
	}
	
	
	public function editnotif(){
	//fecth data from db
		$col=['nicon','ncontent'];
		$id = $this->input->get('id');
		$dbres = $this->Mnotif->detailnotif($col,$id);
		$colq = $this->returncolomn($col);
	//============ form edit notif ===========

		$opticon = array(
				"fa-home",
				"fa-key",
				"fa-legal",
				"fa-pencil",
				"fa-money",
				"fa-book",
				"fa-bullhorn",
				"fa-file-text",
				"fa-envelope-square",
				"fa-envelope",
				"fa-certificate",
				"fa-check-square-o",
				"fa-calendar",
				"fa-clock-o",
				"fa-edit",
				"fa-check",
				"fa-times"
				);
		$selecticon = '<select name="fnicon" id="fnicon" class="form-control selectpicker" required="required">';

		foreach ($opticon as $k => $v) {
			($dbres[0]['nicon']==$v) ? $select = 'selected' : $select='';
			$selecticon .= '<option value="'.$v.'" data-icon="'.$v.'" '.$select.'></option>';
		}
		$selecticon .= "</select>";
		$r[] = $selecticon;

		$fcont =  array('name'=>'fncontent',
						'id'=>'fncont',
						'required'=>'required',
						'placeholder'=>'Notification Content',
						'rows'=>'4',
						'value'=>$dbres[0]['ncontent'],
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
		
		$fsend = array(	'id'=>'updatecont',
						'value'=>'Update Notification',
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
		
		$this->load->view('dashboard/org/notification/editnotif', $data);
	}
	
	public function savenotif(){
			// set new data variable
				$fdata = array(
					'npublish'=>date('Y-m-d H:i:s'),
					'nicon'=>$this->input->post('fnicon'),
					'ncontent'=>$this->input->post('fncontent'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mnotif->savenotif($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Notification Succes.') : $this->session->set_flashdata('x','Add Notification Failed.');
			
		redirect(base_url('Organizer/Notification'));
		
	}
	
	public function delnotif(){
		$id = $this->input->get('id');
		$r = $this->Mnotif->deletenotif($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Notification Success');
		} else{
		$this->session->set_flashdata('x','Delete Notification Failed');
		} 
		redirect(base_url('Organizer/Notification'));
	}
	
	public function updatenotif(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'nicon'=>$this->input->post('fnicon'),
					'ncontent'=>$this->input->post('fncontent'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mnotif->updatenotif($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Notification Succes.') : $this->session->set_flashdata('x','Update Notification Failed.');
			
		redirect(base_url('Organizer/Notification'));
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Mnotif->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Failed.');
		}
		redirect(base_url('Organizer/Notification'));
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
	$find=['npublish','nicon','ncontent','uname'];
	$replace = ['Notification Created','Icon', 'Notification Content','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
