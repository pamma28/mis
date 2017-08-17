<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gender extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mgender','Msetting'));
    }

	
	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idjk','jkname'];
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
			$rows = $this->Mgender->countjk($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mgender->countjk();	
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
						'jkname' => 'Gender'
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
				'base_url' => base_url().'Organizer/Gender?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Gender?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mgender->datajk($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idjk']
							));
				array_unshift($temp[$key],$ctable);
	
				//manipulation menu
				$enc = $value['idjk'];
				unset($temp[$key]['idjk']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Gender/editgen?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>'.
				'<a href="#" data-href="'.base_url('Organizer/Gender/delgen?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Delete</a></div>';
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
			$data['factselected'] = site_url('Organizer/Gender/updateselected');
			
		//=============== Template ============
		$data['jsFiles'] = array(
							);
		$data['cssFiles'] = array(
							);  
		// =============== view handler ============
		$data['title']="Gender";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/gender/genlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addgen(){
	$id=$this->input->get('id');
	$colq=['jkname'];
	//============ form edit quest ===========
		$fncontent =  array('name'=>'jkname',
						'id'=>'jkname',
						'required'=>'required',
						'placeholder'=>'Gender Name',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($fncontent);
		
		$fsend = array(	'id'=>'addgen',
						'value'=>'Add Gender',
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
		
		$this->load->view('dashboard/org/gender/addgen', $data);
	}
	
	public function editgen(){
	//fecth data from db
		$col=['jkname'];
		$id = $this->input->get('id');
		$dbres = $this->Mgender->detailjk($col,$id);
		$colq = $this->returncolomn($col);
	//============ form edit quest ===========
		$fcont =  array('name'=>'jkname',
						'id'=>'jkname',
						'required'=>'required',
						'placeholder'=>'Gender Name',
						'value'=>$dbres[0]['jkname'],
						'class'=>'form-control');
		$r[]=form_input($fcont);
		
		
		$fsend = array(	'id'=>'updatejk',
						'value'=>'Update Gender',
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
		
		$this->load->view('dashboard/org/gender/editgen', $data);
	}
	
	public function savegen(){
			// set new data variable
				$fdata = array(
					'jkname'=>$this->input->post('jkname')
					);
			//update to database
			$hsl = $this->Mgender->savejk($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Gender Succes.') : $this->session->set_flashdata('x','Add Gender Failed.');
			
		redirect(base_url('Organizer/Gender'));
		
	}
	
	public function delgen(){
		$id = $this->input->get('id');
		$r = $this->Mgender->deletejk($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Gender Success');
		} else{
		$this->session->set_flashdata('x','Delete Gender Failed');
		} 
		redirect(base_url('Organizer/Gender'));
	}
	
	public function updategen(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'jkname'=>$this->input->post('jkname')
					);
			//update to database
			$hsl = $this->Mgender->updatejk($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Gender Succes.') : $this->session->set_flashdata('x','Update Gender Failed.');
			
		redirect(base_url('Organizer/Gender'));
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Mgender->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Failed.');
		}
		redirect(base_url('Organizer/Gender'));
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
	$find=['jkname'];
	$replace = ['Gender'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
