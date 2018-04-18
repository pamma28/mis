<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Level extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mlvl','Msetting'));
    }

	
	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idlevel','lvlname','lvlabre','lvllow','lvlup'];
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
			$rows = $this->Mlvl->countlevel($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mlvl->countlevel();	
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
						'lvlname' => 'Level Name',
						'lvlabre' => 'Abbreviation',
						'lvllow' => 'Lowest Mark',
						'lvlup' => 'Highest Mark'
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
				'base_url' => base_url().'Organizer/Level?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Level?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mlvl->datalevel($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idlevel']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['lvlname']='<span class="idname hidden">'.$value['lvlname'].'</span>'.$value['lvlname'];

				//manipulation menu
				$enc = $value['idlevel'];
				unset($temp[$key]['idlevel']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Level/readlevel?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Data" class="btn btn-primary btn-sm" title="Read"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Level/editlevel?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Level/dellevel?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Level/updateselected');
			
		//=============== Template ============
		$data['jsFiles'] = array('numeric/numeric.min'
							);
		$data['cssFiles'] = array(
							);  
		// =============== view handler ============
		$data['title']="Notification";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/level/levellist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addlevel(){
	$id=$this->input->get('id');
	$colq=['lvlname','lvlabre','lvllow','lvlup'];
	//============ form edit quest ===========
		$flvlname =  array('name'=>'flvlname',
						'id'=>'flvlname',
						'required'=>'required',
						'placeholder'=>'Level Name',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($flvlname);
		
		$flvlabre =  array('name'=>'flvlabre',
						'id'=>'flvlabre',
						'required'=>'required',
						'placeholder'=>'Abbreviation',
						'value'=>'',
						'style'=>'width:120px',
						'class'=>'form-control');
		$r[]=form_input($flvlabre);
		
		$flowest =  array('name'=>'flow',
						'id'=>'flow',
						'required'=>'required',
						'placeholder'=>'00.00',
						'value'=>'',
						'style'=>'width:80px',
						'class'=>'form-control');
		$r[]=form_input($flowest);
		
		$fhighest =  array('name'=>'fhigh',
						'id'=>'fhigh',
						'required'=>'required',
						'placeholder'=>'00.00',
						'value'=>'',
						'style'=>'width:80px',
						'class'=>'form-control');
		$r[]=form_input($fhighest);
		
		$fsend = array(	'id'=>'addcat',
						'value'=>'Add Level',
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
		
		$this->load->view('dashboard/org/level/addlevel', $data);
	}
	
	public function readlevel(){
		//fecth data from db
		$col = ['lvlname','lvlabre','lvllow','lvlup'];
		$id = $this->input->get('id');
		$dbres = $this->Mlvl->detaillevel($col,$id);
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
		$this->load->view('dashboard/org/level/readlevel', $data);
		
		
	}
	
	public function editlevel(){
	//fecth data from db
		$col=['lvlname','lvlabre','lvllow','lvlup'];
		$id = $this->input->get('id');
		$dbres = $this->Mlvl->detaillevel($col,$id);
		$colq = $this->returncolomn($col);
	//============ form edit quest ===========
		$flvlname =  array('name'=>'flvlname',
						'id'=>'flvlname',
						'required'=>'required',
						'placeholder'=>'Level Name',
						'value'=>$dbres[0]['lvlname'],
						'class'=>'form-control');
		$r[]=form_input($flvlname);
		
		$flvlabre =  array('name'=>'flvlabre',
						'id'=>'flvlabre',
						'required'=>'required',
						'placeholder'=>'Abbreviation',
						'value'=>$dbres[0]['lvlabre'],
						'style'=>'width:120px',
						'class'=>'form-control');
		$r[]=form_input($flvlabre);
		
		$flowest =  array('name'=>'flow',
						'id'=>'flow',
						'required'=>'required',
						'placeholder'=>'00.00',
						'value'=>$dbres[0]['lvllow'],
						'style'=>'width:80px',
						'class'=>'form-control');
		$r[]=form_input($flowest);
		
		$fhighest =  array('name'=>'fhigh',
						'id'=>'fhigh',
						'required'=>'required',
						'placeholder'=>'00.00',
						'value'=>$dbres[0]['lvlup'],
						'style'=>'width:80px',
						'class'=>'form-control');
		$r[]=form_input($fhighest);
		
		
		$fsend = array(	'id'=>'updatelvl',
						'value'=>'Update Level',
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
		
		$this->load->view('dashboard/org/level/editlevel', $data);
	}
	
	public function savelevel(){
			// set new data variable
				$fdata = array(
					'lvlname'=>$this->input->post('flvlname'),
					'lvlabre'=>$this->input->post('flvlabre'),
					'lvllow'=>$this->input->post('flow'),
					'lvlup'=>$this->input->post('fhigh')
					);
			//update to database
			$hsl = $this->Mlvl->savelevel($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Level Succes.') : $this->session->set_flashdata('x','Add Level Failed.');
			
		redirect(base_url('Organizer/Level'));
		
	}
	
	public function dellevel(){
		$id = $this->input->get('id');
		$r = $this->Mlvl->deletelevel($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Level Success');
		} else{
		$this->session->set_flashdata('x','Delete Level Failed');
		} 
		redirect(base_url('Organizer/Level'));
	}
	
	public function updatelevel(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'lvlname'=>$this->input->post('flvlname'),
					'lvlabre'=>$this->input->post('flvlabre'),
					'lvllow'=>$this->input->post('flow'),
					'lvlup'=>$this->input->post('fhigh')
					);
			//update to database
			$hsl = $this->Mlvl->updatelevel($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Level Succes.') : $this->session->set_flashdata('x','Update Level Failed.');
			
		redirect(base_url('Organizer/Level'));
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Mlvl->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Failed.');
		}
		redirect(base_url('Organizer/Level'));
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
	$find=['lvlname','lvlabre','lvllow','lvlup'];
	$replace = ['Level Name', 'Abbreviation','Lowest Mark','Highest Mark'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
