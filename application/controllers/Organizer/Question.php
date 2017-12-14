<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mq','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['subject','idsubject as id1','1 as id3','1 as id2'];
		$header = $this->returncolomn($column);
		$header[1]='Subject in Test';
		$header[2]='Total Question';
		$header[3]='Question List Details';
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
			$rows = $this->Mq->countsubject($filter);
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mq->countsubject();	
			
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
						'subject' => 'Subject Name'
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
				'base_url' => base_url().'/Organizer/Question?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Question?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mq->datasubject($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//save id test count test and question 
				$enc = $value['id1'];
				$qtot = $this->Mq->countquestbyid($enc);
				$ttot = $this->Mq->counttestbyid($enc);
				
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['id1']
							));
				array_unshift($temp[$key],$ctable);
					
					// loop subject list
					$coltest = ['tname','qtot','qpercent'];
					$dttest = $this->Mq->datatestbyid($coltest,$enc);
					$rts= array();$sno=1;
					foreach($dttest as $sk=>$sv){
						$rts[]=$sno.'. '.$sv['tname'].', '.$sv['qtot'].' Question(s)';
						$sno++;
					}
					($ttot>5) ? $rts[] = '<i><b>and '.(($ttot)-5).' test(s) more.</b></i>' : null;
						// determine amount question should be made
						$tempq= $this->Mq->getqtotbyid($enc);
						($tempq!=null) ? $totq=($tempq*3) : $totq = '0';
						$rts[]='<b>Minimun Total Question: '.$totq.'<b>';
					$temp[$key]['id1']=implode('<br/>',$rts);
					
					// loop question list
					$colq = ['question','qtype','q_group'];
					$dtq = $this->Mq->dataquestbyid($colq,$enc);
					$rqs= array();$sno=1;
					foreach($dtq as $sk=>$sv){
						(strlen($sv['question'])>30) ? $par=mb_substr($sv['question'],0,30).'.....':$par=$sv['question'];
						$rqs[]=$sno.'. ['.$sv['qtype'].'] '.$par;
						$sno++;
					}
					($qtot>5) ? $rqs[] = '<i><b>and '.(($qtot)-5).' question(s) more.<b></i>' : null;
					$temp[$key]['id2']=implode('<br/>',$rqs);
					
				//total question
				($qtot<$totq) ? $qmore = '<br/><i><b>Need '.($totq-$qtot).' more.</b></i>': $qmore =null;
				$temp[$key]['id3']= $qtot.' available.'.$qmore;
				
				//manipulation menu
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Question/detailquestsubject?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Question/editquestsubject?id=').$enc.'" alt="Edit Data" class="btn btn-info btn-sm" title="Edit Question List"><i class="fa fa-edit"></i> Edit Question List</a></div>';
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
			$data['factselected'] = site_url('Organizer/Question/updateselected');
			
		
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
			$data['fsendper'] = site_url('Organizer/Question/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Subject Test";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/question/questlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function allquestion(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idq','idattach','subject','question','qtype','q_group','q_bundle','1 as ans'];
		$header = $this->returncolomn($column);
		$header[7]="Answers";
		unset($header[0]);
		unset($header[1]);
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
			$rows = $this->Mq->countquest($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mq->countquest();	
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
						'subject'=>'Subject',
						'question' => 'Question',
						'qtype' => 'Question Type',
						'q_group' => 'Question Code',
						'q_bundle' => 'Question Group'
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
		
		$adv['Subject'] = form_input(
						array('name'=>'subject',
						'id'=>'subject',
						'placeholder'=>'Subject',
						'value'=>isset($tempfilter['subject']) ? $tempfilter['subject'] : null,
						'class'=>'form-control'));
		
		$adv['Question'] = form_input(
						array('name'=>'question',
						'id'=>'question',
						'size'=>'100',
						'placeholder'=>'Question',
						'value'=>isset($tempfilter['question']) ? $tempfilter['question'] : null,
						'class'=>'form-control'));
		$adv['Question Type'] = form_input(
						array('name'=>'qtype',
						'id'=>'questype',
						'placeholder'=>'Question Type',
						'value'=>isset($tempfilter['qtype']) ? $tempfilter['qtype'] : null,
						'class'=>'form-control'));
		
		$adv['Question Code'] = form_input(
						array('name'=>'q_group',
						'id'=>'questcode',
						'placeholder'=>'Question Code',
						'value'=>isset($tempfilter['q_group']) ? $tempfilter['q_group'] : null,
						'class'=>'form-control'));
		
		$adv['Question Group'] = form_input(
						array('name'=>'q_bundle',
						'id'=>'questgroup',
						'placeholder'=>'Question Group',
						'value'=>isset($tempfilter['q_bundle']) ? $tempfilter['q_bundle'] : null,
						'class'=>'form-control'));
						
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Question/allquestion?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Question/allquestion?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mq->dataquest($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation attachment
					if(null!=$value['idattach']) {
					$btnattach='<a href="'.base_url('Organizer/Question/editattach?att=').$value['idattach'].'" data-target="#DetailModal" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Edit Attachment"><i class="fa fa-edit"></i></a>
					<a href="#" data-href="'.base_url('Organizer/Question/deleteattach?att=').$value['idattach'].'" data-target="#confirm-delete" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Delete Attachment"><i class="fa fa-times"></i></a>';
					$qattach = '<br/><hr/><div class="text-center"><div class="btn-group"><a href="'.base_url('Organizer/Question/detailattach?att=').$value['idattach'].'" data-target="#DetailModal" class="btn btn-info btn-xs" data-toggle="modal" role="button"><i class="fa fa-link"></i> Attachment</a>'.$btnattach.'</div></div>';
					}else{ 
					$qattach='<br/><hr/><div class="text-center"><div class="btn-group"><a href="'.base_url('Organizer/Question/addattach?q=').$value['idq'].'" data-target="#DetailModal" class="btn btn-info btn-xs" data-toggle="modal" role="button"><i class="fa fa-plus"></i> Add Attachment</a></div></div>';
					}
				unset($temp[$key]['idattach']);
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idq']
							));
				array_unshift($temp[$key],$ctable);
				//manipulation question data
				(strlen($value['question'])>30) ? $ques=mb_substr($value['question'],0,30).'.....':$ques=$value['question'];
				$temp[$key]['question']='<span class="idname">'.$temp[$key]['question'].'</span>'.$qattach;
				//manipulation answer
				$colans=['idans','answer','key_ans'];
				$tans = $this->Mq->populateanswer($colans,$value['idq']);
				$tempans = array(); $keyans='';
					foreach($tans as $vans){
					if ($vans['key_ans']=='1') {($keyans='<small class="label label-success"><i><b>Key Answer: '.$vans['answer'].'</b></i></small>');}
					($keyans!=null)?($keyexist='1'):($keyexist='');
					$linkans= base_url('Organizer/Question/deleteanswer?ans='.$vans['idans']);
					$editans = '<a href="'.base_url('Organizer/Question/editanswer?ans=').$vans['idans'].'" data-target="#DetailModal" data-key="'.$keyexist.'" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Edit Answer"><i class="fa fa-edit"></i></a>';
					$delans = '<a href="#" class="btn btn-default btn-xs" data-href="'.$linkans.'" data-target="#confirm-delete" data-toggle="modal" alt="Delete Answer"><i class="fa fa-times"></i></a>';
					$tempans[]= '<div class="form-group"><div class="col-md-7">- '.$vans['answer'].'</div><label class="col-md-5">'.$editans.$delans.'</label></div>';
					}
					$bans = '<a href="'.base_url('Organizer/Question/addanswer?q=').$value['idq'].'" data-target="#DetailModal" class="btn btn-default btn-xs" data-toggle="modal" role="button"><i class="fa fa-plus"></i> Add Answer</a>';	
					$dtans = $bans.implode('',$tempans).$keyans;
				$temp[$key]['ans']=$dtans;
				
				//manipulation menu
				$enc = $value['idq'];
				unset($temp[$key]['idq']);
				$temp[$key]['menu']='<div class="btn-group-vertical" aria-label="Question Menu" role="group"><a href="'.base_url('Organizer/Question/detailquest?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Question/editquest?q=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Question/deletequest?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Question/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Question/importxls');
			
			
		
		// ============= export form ==============
			$optcol = array(
						'subject'=>'Subject',
						'question' => 'Question',
						'qtype' => 'Question Type',
						'q_group' => 'Question Code',
						'q_bundle' => 'Question Group',
						'answer' => 'Answer(s)',
						'key' => 'Key Answer'
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
			$data['fbtnexport']= form_submit(array('value'=>'Export',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factexp'] = site_url('Organizer/Question/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Question/printquest');
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="All Question Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/question/allquestlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function questiontype(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idqtype','qtype','qmanual'];
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
			$rows = $this->Mq->countqtype($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mq->countqtype();	
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
						'qtype' => 'Question Type'
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
	
		$temp = $this->Mq->dataqtype($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idqtype']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['idqtype']='<span class="idname">'.$temp[$key]['idqtype'].'</span>';
				//manipulation menu
				$temp[$key]['qmanual'] = ($value['qmanual']==0) ? '<label class="label label-default">No</label>' :  '<label class="label label-primary">Yes</label>';
				$enc = $value['idqtype'];
				unset($temp[$key]['idqtype']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Question/editquesttype?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Question/deletequesttype?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/PDS/updateselected');
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Question Type Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/question/qtypelist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function detailquestsubject(){
		//fecth data from db
		$col=['subject','1 as id1','1 as id2'];
		$id = $this->input->get('id');
		$dbres = $this->Mq->detailsubject($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		$row[1]='Subject In Test';
		$row[2]='Question List Details';
		$col[1]='id1';
		$col[2]='id2';
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
				"dtval"=>' : <b>'.$dbres[0][$col[$a]].'</b>'
				);

			if (($key=='Subject In Test')){
					// loop detail subject related
					$colq = ['tname','qtot','tduration','tktrgn','qpercent','uname'];
					$dttest = $this->Mq->populatetest($colq,$id);
					$rst[]= '<tr><td><b>No</b></td><td><b>Test Name</b></td><td><b>Duration</b></td><td><b>Total Question</b></td><td><b>Assessment Percentage</b></td><td><b>Notes</b></td><td><b>Created by</b></td></tr>';
					$sno=1;
					foreach($dttest as $sk=>$sv){
						$rst[]='<tr><td>'.$sno.'</td><td>'.$sv['tname'].'</td><td>'.$sv['tduration'].' min</td><td>'.$sv['qtot'].'</td><td>'.$sv['qpercent'].'%</td><td>'.$sv['tktrgn'].'</td><td>'.$sv['uname'].'</td></tr>';
						$sno++;
					}					
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>'<table class="table">'.implode('',$rst).'</table>'
						);
					}
			
			if (($key=='Question List Details')){
					// loop detail question
					$colq = ['question','qtype','q_group','uname','q_paragraph','q_file'];
					$dtq = $this->Mq->populatequest($colq,$id);
					$rsq[]= '<tr><td><b>No</b></td><td><b>Question</b></td><td><b>Type</b></td><td class="text-center"><b>Code</b></td><td><b>Attachment</b></td><td><b>Last Updated by</b></td></tr>';
					$sno=1;
					foreach($dtq as $sk=>$sv){
						(strlen($sv['q_paragraph'])>30) ? $par=mb_substr($sv['q_paragraph'],0,30).'.....':$par=$sv['q_paragraph'];
						$rsq[]='<tr><td>'.$sno.'</td><td>'.$sv['question'].'</td><td>'.$sv['qtype'].'</td><td>'.$sv['q_group'].'</td><td>'.$par.'</td><td>'.$sv['uname'].'</td></tr>';
						$sno++;
					}					
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>'<table class="table">'.implode('',$rsq).'</table>'
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/question/detailquest', $data);
		
		
	}
	
	public function editquestsubject(){					
		if ($this->input->get('id')!= null)
		{
		// ============== Fetch data test & subject ============			
		$col=['subject','1 as id1','1 as id2','1 as id3'];
		$id = $this->input->get('id');
		$g = $this->Mq->detailsubject($col,$id);
			$colq = ['question.idq','question','question.idqtype','q_bundle','q_group','ques_attach.idattach'];
			$dtq = $this->Mq->populatequest($colq,$id);
			$totq = $this->Mq->countquestbyid($id);
			$maxq = $this->Mq->getqtotbyid($id);
			if($maxq!=0){$qpercent = floor(($totq/($maxq*3))*100);$qmore=ceil(100-(($totq/($maxq*3))*100));}else{$qpercent=100;$qmore=0;}
			((($maxq*3)-$totq)>0) ? $qneed=($maxq*3)-$totq:$qneed=0;
		// ========= form edit ================ 
		$r[] = '<div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-edit"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Subject Name</span>
                  <span class="info-box-number">'.$g[0]['subject'].'</span>
                </div>
              </div>';
		$r[] = '<div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-question"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Question Made</span>
                  <span class="info-box-number">'.' ('.$qpercent.'%) '.$totq.' Question(s)</span>
                </div>
              </div>';
		$r[] = '<div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Question Needed</span>
                  <span class="info-box-number">'.$qneed.' Question(s) more</span>
                  <div class="progress">
                    <div style="width: '.$qpercent.'%" class="progress-bar"></div>
                  </div>
                  <span class="progress-description">
                    '.$qmore.'% more
                  </span>
                </div>
              </div>';
				
		$fansq = array('name'=>'fansq[]',
						'id'=>'fansq[]',
						'required'=>'required',
						'placeholder'=>'Answer',
						'value'=>'',
						'class'=>'form-control');
						
		$fquest = array('name'=>'fquest[]',
						'id'=>'fquest[]',
						'required'=>'required',
						'placeholder'=>'Question',
						'value'=>'',
						'rows'=>2,
						'cols'=>6,
						'class'=>'form-control');
		
			$optjq=$this->Mq->optqtype();
		$fqtype = array('name'=>'fqtype[]',
						'id'=>'fqtype[]',
						'placeholder'=>'Question Type',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$foptkey = array('name'=>'foptkey[]',
						'id'=>'foptkey[]',
						'placeholder'=>'Key Answer',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
			$qcode='A';
			for($a=1;$a<30;$a++){
				$optqcode[$qcode]=$qcode;
				$qcode++;
			}
		$fqcode = array('name'=>'fqcode[]',
						'id'=>'fqgroup[]',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
			for($a=1;$a<50;$a++){
				$optqgroup[$a]=$a;
			}
		$fqgroup = array('name'=>'fqbundle[]',
						'id'=>'fqbundle[]',
						'required'=>'required',
						'placeholder'=>'(1-999)',
						'data-live-search'=>'true',
						'class'=>'selectpicker  form-control');
				
		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		// generate QUESTION stored
		$rq[]= '<tr><td><b>Question</b></td><td><b>Type</b></td><td><b>Code</b></td><td><b>Group</b></td><td><b>Answer<b></td><td><b>Menu</b></td></tr>';
			foreach($dtq as $sk=>$sv){
					$fquest['value']=$sv['question'];
					$linkedit= base_url('Organizer/Question/editquest?q='.$sv['idq'].'&s='.$id);
					$linkdel= base_url('Organizer/Question/deletequest?q='.$sv['idq'].'&s='.$id);
							
							// generate ANSWER stored
							$colans =['idans','answer','key_ans'];
							$dtans= $this->Mq->populateanswer($colans,$sv['idq']);
							$checkkey = $this->Mq->checkkeyexist($sv['idq']);
							($checkkey>0) ? $keyexist='1':$keyexist='0';
							$rans= array();$keyans='';$optkey= array(''=>'No Key Answer');$keyid='';
							foreach($dtans as $ik=>$iv){
								if ($iv['key_ans']=='1'){
								 $keyans= $iv['answer'];
								 $keyid=$iv['idans'];
								}
								$optkey[$iv['idans']]=$iv['answer'];
								$fansq['value']=$iv['answer'];
								$linkq= base_url('Organizer/Question/deleteanswer?ans='.$iv['idans'].'&s='.$id);
								$editans = '<a href="'.base_url('Organizer/Question/editanswer?ans=').$iv['idans'].'&s='.$id.'" data-target="#DetailModal" data-key="'.$keyexist.'" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Edit Answer"><i class="fa fa-edit"></i></a>';
								$rans[]='<div class="row"><div class="col-md-9">'.form_hidden('fidans[]',$iv['idans']).form_input($fansq).'</div><div class="col-md-3 btn-group">'.$editans.'<a href="#" class="btn btn-default btn-xs" data-href="'.$linkq.'" data-target="#confirm-delete" data-toggle="modal" alt="Delete Answer"><i class="fa fa-times"></i></a></div></div>';
							}
								($keyans!='')? $keyans = '<small><i>Key answer: <b>'.$keyans.'</b></i></small>': $keyans='<small><i>No Key Answer</i></small>';
							$bans = '<a href="'.base_url('Organizer/Question/addanswer?q=').$sv['idq'].'&s='.$id.'" data-target="#DetailModal" class="btn btn-default btn-xs" data-toggle="modal" role="button"><i class="fa fa-plus"></i> Add Answer</a>';	
							$rans[]='<hr/><div class="form-group"><label class="col-sm-4">Key Answer:</label><div class="col-sm-8">'.form_dropdown($foptkey,$optkey,$keyid).'</div></div>';
							$rans[]='<div class="row form-group"><div class="col-md-12">'.$keyans.'</div></div>';
							$popans=$bans.form_hidden('ftotans[]',count($dtans)).'<div class="form-horizontal">'.implode('',$rans).'</div>';								
					
					//get attachment if any
					if(null!=$sv['idattach']) {
					$btnattach='<a href="'.base_url('Organizer/Question/editattach?att=').$sv['idattach'].'&s='.$id.'" data-target="#DetailModal" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Edit Attachment"><i class="fa fa-edit"></i></a>
					<a href="#" data-href="'.base_url('Organizer/Question/deleteattach?att=').$sv['idattach'].'&s='.$id.'" data-target="#confirm-delete" class="btn btn-default btn-xs" data-toggle="modal" role="button" alt="Delete Attachment"><i class="fa fa-times"></i></a>';
					$qattach = '<br/><hr/><div class="text-center"><div class="btn-group"><a href="'.base_url('Organizer/Question/detailattach?att=').$sv['idattach'].'" data-target="#DetailModal" class="btn btn-info btn-xs" data-toggle="modal" role="button"><i class="fa fa-link"></i> Attachment</a>'.$btnattach.'</div></div>';
					}else{ 
					$qattach='<br/><hr/><div class="text-center"><div class="btn-group"><a href="'.base_url('Organizer/Question/addattach?q=').$sv['idq'].'&s='.$id.'" data-target="#DetailModal" class="btn btn-info btn-xs" data-toggle="modal" role="button"><i class="fa fa-plus"></i> Add Attachment</a></div></div>';
					}
					
					$rq[]='<tr><td>'.form_hidden('fidq[]',$sv['idq']).form_textarea($fquest).'</td><td>'.form_dropdown($fqtype,$optjq,$sv['idqtype']).$qattach.'</td><td>'.form_dropdown($fqcode,$optqcode,$sv['q_group']).'</td><td>'.form_dropdown($fqgroup,$optqgroup,$sv['q_bundle']).'</td><td>'.$popans.'</td><td><div class="btn-group-vertical" role="group" aria-label="Question Menu"><a href="'.$linkedit.'" class="btn btn-info btn-sm" data-target="#DetailModal" data-toggle="modal" alt="Edit Question" title="Edit"><i class="fa fa-edit"></i></a><a href="#" data-href="'.$linkdel.'" class="btn btn-danger btn-sm" data-target="#confirm-delete" data-toggle="modal" alt="Delete Data" title="Delete"><i class="fa fa-trash"></i></a></div></td></tr>';
					}
		
		// generate question variables
		$bsub = '<a href="'.base_url('Organizer/Question/addquest?s='.$id).'" data-target="#DetailModal" data-toggle="modal" role="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Question</a>';
		$badd = '<a href="'.base_url("Organizer/Question/addquesttype?s=".$id).'" id="btnAdd" data-tot="" data-target="#DetailModal" data-toggle="modal" role="button" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Question Type</a>';						
		$r[]=$bsub.' '.$badd.'<div class="table-responsive"><table class="table table-bordered">'.implode('',$rq).'</table></div>';
		
		//set row title
		$row = $this->returncolomn($col);
		$row[1]='Total Question';
		$row[2]='Question Needed';
		$row[3]='Question List';
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
					"dtval"=>'<div class="col-sm-4">'.$r[$a].'</div>'
					);
			if ($key=='Question List'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="form-group row text-center"><h4 for="l'.$key.'"><b>'.$key.'</b></h4></div>',
					"dtval"=>'<div class="row">'.$r[$a].'</div>'
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
		$data['title']="Edit Question Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/question/editquestion', $data, TRUE);
		$this->load->view ('template/main', $data);
		} else {
		$this->session->set_flashdata('x','Cannot Directly Access, Choose One of the Subject.');
		redirect(base_url('Organizer/Question'));
		}
	
	}
	
	public function predefinedimport(){
		$dtcol = ['Question','ID Subject','ID Question Type','Question Code','Question Group','Answer 1','Answer 2','Answer 3','Answer 4','Answer 5','Key Answer']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportQuestionData');
		
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
		
		//=== Create hint
		//set new colomn
			$Dcol = chr(ord($Hcol)+1);
			$Dnewcol = chr(ord($Hcol)+3);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setItalic(true);
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.'1', 'Hint (Please pay attention)');
		$objPHPExcel->getActiveSheet()->mergeCells($Dcol.'1:'.$Dnewcol.'1');
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
		$Hrow= 2;
		$tempcol = $Dcol;
		$rowrole=$Hrow;
		
		//set colomn header Subject Data
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Question Subject");			
		$objPHPExcel->getActiveSheet()->mergeCells($tempcol.($Hrow).':'.chr(ord($Dcol)+1).$Hrow);
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow+1),"ID Subject");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow+1),"Subject");			
			$dtsub= $this->Mq->optsub();
			unset($dtsub['']);
		$Memrow=$Hrow+2;
		foreach ($dtsub as $k=>$v){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Memrow,$k);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Memrow,$v);
			$Memrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Memrow-1),"Remember: Always Put 'ID Subject' instead of 'Subject' in Colomn 'ID Subject'");			
		
		//set colomn header Question Type
		$Grow = $Memrow+1;
		$Gcol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow),"Question Type");			
		$objPHPExcel->getActiveSheet()->getStyle($Gcol.$Grow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->mergeCells($Gcol.($Grow).':'.chr(ord($Gcol)+1).$Grow);
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow+1),"Question Type ID");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).($Grow+1),"Value");			
			$dtqtype= $this->Mq->optqtype();
			unset($dtqtype['']);
		$Grow = $Grow+2;
		foreach ($dtqtype as $k=>$v){
			$Gcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Gcol.$Grow,$k);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).$Grow,$v);
			$Grow++;
		}
		$lastcol = chr(ord($Gcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Grow-1),"Remember: Always Put 'Question Type ID' instead of 'Value' in Colomn 'Question Type'");			
		
		//set colomn header Question Code
		$Crow = $Grow+1;
		$Ccol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Ccol.($Crow),"Question Code");			
		$objPHPExcel->getActiveSheet()->getStyle($Ccol.$Crow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->setCellValue($Ccol.($Crow+1),"A-Z");			
		$objPHPExcel->getActiveSheet()->getStyle($Ccol.($Crow+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->mergeCells($Ccol.($Crow).':'.chr(ord($Ccol)+1).($Crow));
		$objPHPExcel->getActiveSheet()->mergeCells($Ccol.($Crow+1).':'.chr(ord($Ccol)+1).($Crow+1));
		$Crow = $Crow+1;
		$lastcol = chr(ord($Ccol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Crow),"Remember: Always Put 'Alphabetical' instead of 'Number' in Colomn 'Question Code' (A-Z)");			
		
		//set colomn header Question Group
		$Grrow = $Crow+2;
		$Grcol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Grcol.($Grrow),"Question Group");			
		$objPHPExcel->getActiveSheet()->getStyle($Grcol.$Grrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue($Grcol.($Grrow+1),"1-999");		
		$objPHPExcel->getActiveSheet()->mergeCells($Grcol.($Grrow).':'.chr(ord($Grcol)+1).$Grrow);
		$objPHPExcel->getActiveSheet()->mergeCells($Grcol.($Grrow+1).':'.chr(ord($Grcol)+1).($Grrow+1));
		$Grrow = $Grrow+1;
		$lastcol = chr(ord($Grcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Grrow),"Remember: Always Put 'Number' instead of 'Alphabetical' in Colomn 'Question Group' (1-999)");			
		
		//set colomn header Key Answer
		$Krow = $Grrow+2;
		$Kcol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Kcol.($Krow),"Key Answer");			
		$objPHPExcel->getActiveSheet()->getStyle($Kcol.$Krow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue($Kcol.($Krow+1),"1-5");		
		$objPHPExcel->getActiveSheet()->mergeCells($Kcol.($Krow).':'.chr(ord($Kcol)+1).$Krow);
		$objPHPExcel->getActiveSheet()->mergeCells($Kcol.($Krow+1).':'.chr(ord($Kcol)+1).($Krow+1));
		$Krow = $Krow+1;
		$lastcol = chr(ord($Kcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Krow),"Remember: Choose among 1-5 as Key Answer in Colomn 'Key Answer' (1-5)");			
		
		
		//set autowidth
		foreach(range('A',$lastcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Hcol)-1).'11')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Hrow).':'.chr(ord($lastcol)-1).($Memrow-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Memrow+1).':'.chr(ord($lastcol)-1).($Grow-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Crow-1).':'.chr(ord($lastcol)-1).($Crow))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Grrow-1).':'.chr(ord($lastcol)-1).($Grrow))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Krow-1).':'.chr(ord($lastcol)-1).($Krow))->applyFromArray($styleArray);
		
		
		//set background color of HINT 
		$fillArray = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb'=>'E6FF00')
			),
			'font' => array(
				'color' => array('rgb' => '003333')
			) 
		);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Krow))->applyFromArray($fillArray);
		
		//Freeze pane
		//$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Grow));
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatQuestionData.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '100000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatQuestionData.xls')) {
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
              $dtans = Array();
              $notrans = Array();
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
                   //question data
				   $dtxl[$i-1]['question'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['idsubject'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['idqtype'] = $objWorksheet->getCell('C'.($i+1))->getValue();
                   $dtxl[$i-1]['q_group'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['q_bundle'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
				   
				   //answers data
				   $colans='F';
				   for ($an=0;$an<=4;$an++){
				   ($objWorksheet->getCell('K'.($i+1))->getValue()==($an+1))? $kans='1':$kans='0';
				   $dtans['answer'][$i-1][$an]=$objWorksheet->getCell($colans.($i+1))->getValue();
				   $dtans['key'][$i-1][$an]=$kans;
				   $colans++;
				   }
				 }
              }
			  
			  //save data through model
			  $report = $this->Mq->importdata($dtxl,$dtans);
              
			  //set flashdata
				$flashdata = 'Import '.$report['success'].' Question(s), '.$report['sucans'].' Answer(s) Data Success, with '.$report['failed'].' unsuccessful import.';
				if ($report['faillist']<>''){
				$flashdata = $flashdata."<br/>Data error: <br/>".$report['faillist'];
				}
				($report['failans']!=null) ? $flashdata = $flashdata."<br/>Answer(s) error: <br/>".$report['failans']:null;
				$this->session->set_flashdata('v',$flashdata);
            }
		
		//redirect to data list
		redirect(base_url('Organizer/Question/allquestion'));
    }
	
	public function exportxls(){
		//catch column value
		$countans='(select count(idans) from Answer where idq=que) as totq';
		$kans='(select answer from answer where idq=que and key_ans="1") as keyans';
		$colidq='question.idq as que';
		$dtcol=array();
		if ($this->input->post('fcolomn')!=null){
			foreach($this->input->post('fcolomn') as $selected)
			{ 
				if (($selected == 'answer')){
				$dtcol[] =$colidq;
				$dtcol[] =$countans;
				$totcol=2;
				} else if($selected=='key'){
				if (!in_array($colidq,$dtcol)){$dtcol[] =$colidq;}
				$dtcol[] =$kans;
				} else {
				$dtcol[] = $selected;
				}
			}
		} else {
		$dtcol = ['subject','question','qtype','q_group','q_bundle','question.idq as que',$countans,$kans];
		}		
		//get data from database
			$dexp = $this->Mq->exportquest($dtcol);
			$title = Date('d-m-Y');
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('QuestionData');
		
		//catch highest answer number, store answer(s)
		$maxq=0;
		$keyans=array();
		$Drow=3;
		if(in_array($countans,$dtcol)){
			foreach($dexp as $k=>$v){
			//store total question
			($v['totq']>$maxq) ? $maxq=$v['totq']:null;
			//store keyanswer
			$dans[$k]= $this->Mq->populateanswer(array('answer','idans','key_ans'),$v['que']);
			}
		}
		
		//set colomn for answer and key answer
		((in_array($countans,$dtcol)) and (in_array($kans,$dtcol)))? $totcol=3:$totcol=2;
		$Acol=chr(ord('A')+(count($dexp[0])-$totcol));
		$Kcol=chr(ord('A')+(count($dexp[0])+$maxq)-$totcol);
		
		foreach($dexp as $k=>$v){
			if (in_array($countans,$dtcol)){
					//put answers in table
					for($qi=0;$qi<$v['totq'];$qi++){
							$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Acol)+$qi).$Drow,$dans[$k][$qi]['answer']);
					}	
			unset($dexp[$k]['totq']);
				}
			
			$Drow++;
			unset($dexp[$k]['que']);
		}

	
		//change header data
		if(($key = array_search($colidq, $dtcol)) !== false) {
			unset($dtcol[$key]);
			}
		if(($key = array_search($kans, $dtcol)) !== false) {
			$dtcol[$key]="Key Answer";
			}	
		$dtcol = $this->returncolomn($dtcol);
		
		//Create Heading
		$Hcol = 'A';
		$Hrow = 2;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
				if ($h==$countans){
					$Htempcol=$Hcol;
					for($qi=1;$qi<=$maxq;$qi++)
					{
					$objPHPExcel->getActiveSheet()->setCellValue($Htempcol.$Hrow,'Answer'.$qi);
					$objPHPExcel->getActiveSheet()->getStyle($Htempcol.$Hrow)->getFont()->setSize(12);
					$objPHPExcel->getActiveSheet()->getStyle($Htempcol.$Hrow)->getFont()->setBold(true);
					$Htempcol++;
						if (($qi==$maxq) and (in_array($kans,$dtcol))){
						$objPHPExcel->getActiveSheet()->setCellValue($Kcol.$Hrow,'Key Answer');
						$objPHPExcel->getActiveSheet()->getStyle($Kcol.$Hrow)->getFont()->setSize(12);
						$objPHPExcel->getActiveSheet()->getStyle($Kcol.$Hrow)->getFont()->setBold(true);
						$Hcol=$Htempcol;
						} else{
						$Hcol=chr(ord($Htempcol)-1);
						}
					}
				}
				$Hcol++;
				
		}
				
		//Insert Data
		$Drow = 3;
		$Dcol = "A";
		$ctot = count($dexp);
		if ((is_array($dexp) || is_object($dexp)) and($ctot<>0)){
			foreach($dexp as $key=>$val){
				$Dcol = "A";
				foreach ($val as $i=>$k){
					if($i=='keyans'){
					(($val['keyans']==null))? $k="No Key Answer":null;
					$objPHPExcel->getActiveSheet()->setCellValue($Kcol.$Drow,$k);
					} else {
					$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$k);
					}
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'QUESTION DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Question Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function printquest(){
		//catch column value
		$countans='(select count(idans) from Answer where idq=que) as totq';
		$kans='(select answer from answer where idq=que and key_ans="1") as keyans';
		$colidq='question.idq as que';
		$dtcol=array();
		if ($this->input->post('fcolomn')!=null){
			foreach($this->input->post('fcolomn') as $selected)
			{ 
				if (($selected == 'answer')){
				$dtcol[] =$colidq;
				$dtcol[] =$countans;
				$totcol=2;
				} else if($selected=='key'){
				if (!in_array($colidq,$dtcol)){$dtcol[] =$colidq;}
				$dtcol[] =$kans;
				} else {
				$dtcol[] = $selected;
				}
			}
		} else {
		$dtcol = ['subject','question','qtype','q_group','q_bundle','question.idq as que',$countans,$kans];
		}		
		//get data from database
		$dexp = array();
		$dexp = $this->Mq->exportquest($dtcol);
		$title = Date('d-m-Y');
		
		//catch highest answer number, store answer(s)
		$maxq=0;
		$keyans=array();
		$totans=array();
		$Drow=3;
		if(in_array($countans,$dtcol)){
			foreach($dexp as $k=>$v){
			//store highest question
			($v['totq']>$maxq) ? $maxq=$v['totq']:null;
			$totans[$k]=$v['totq'];
			//store keyanswer
			$dans[$k]= $this->Mq->populateanswer(array('answer','idans','key_ans'),$v['que']);
			}
		}
		
		// config table
		if(($key = array_search($colidq, $dtcol)) !== false) {
			unset($dtcol[$key]);
			}
		$tempkey=0;
		if(($key = array_search($kans, $dtcol)) !== false) {
			unset($dtcol[$key]);
			$tempkey=1;
			}
		if(($key = array_search($countans, $dtcol)) !== false) {
			unset($dtcol[$key]);
			$sumcol=count($dexp[0]);
				for($i=1;$i<=$maxq;$i++){
				$dtcol[$sumcol+$i-$totcol]='Answer'.$i;
				}
			}
		($tempkey==1)?$dtcol[]='Key Answer':null;
		$header = $this->returncolomn($dtcol);
		
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
			foreach($dexp as $key=>$val){
				foreach($val as $k=>$v){
					if($k=='totq'){
						unset($dexp[$key]['totq']);
						for($ia=0;$ia<$maxq;$ia++){
							(array_key_exists($ia,$dans[$key])) ? $vans=$dans[$key][$ia]['answer']:$vans='';
							$dexp[$key]['answer'.($ia+1)]=$vans;
						}
					}
					if($k=='keyans'){
						$tempans=$val['keyans'];
						($tempans==null)?$tempans='No Key Answer':null;
						unset($dexp[$key]['keyans']);
						$dexp[$key]['answerkey']=$tempans;
					}
				}
				unset($dexp[$key]['que']);
			}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->allquestion();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Question Data ".$period." Period<br/><small>".$title."</small>";
		
		$this->load->view('dashboard/org/question/printquest', $data);
		
	}
	
	public function addquestsubject(){
	$colq=['Subject','question','idqtype','q_group','q_bundle'];
	//============ form edit quest ===========
			$optsub= $this->Mq->optsub();
		$fsub = array('name'=>'fsub',
						'id'=>'fsub',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fsub,$optsub,'');
		
		$fquest = array('name'=>'fquest',
						'id'=>'fquest',
						'required'=>'required',
						'placeholder'=>'Question',
						'value'=>'',
						'rows'=>5,
						'cols'=>10,
						'class'=>'form-control');
		$r[]=form_textarea($fquest);
		
			$optjq=$this->Mq->optqtype();
		$fqtype = array('name'=>'fqtype',
						'id'=>'fqtype',
						'placeholder'=>'Question Type',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqtype,$optjq,'');
		
			$qcode='A';
			for($a=1;$a<30;$a++){
				$optqcode[$qcode]=$qcode;
				$qcode++;
			}
		
		$fqcode = array('name'=>'fqcode',
						'id'=>'fqgroup',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqcode,$optqcode,'');
		
			for($a=1;$a<50;$a++){
				$optqgroup[$a]=$a;
			}
		$fqgroup = array('name'=>'fqbundle',
						'id'=>'fqbundle',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker  form-control');
		$r[]=form_dropdown($fqgroup,$optqgroup,'');
						
		$fsend = array(	'id'=>'updatequestion',
						'value'=>'Add Question',
						'class'=>'btn btn-primary',
						'type'=>'submit');
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
		
		$this->load->view('dashboard/org/question/addquestsub', $data);
	}
	
	public function addquest(){
	$idsub=$this->input->get('s');
	$colq=['question','idqtype','q_group','q_bundle'];
	//============ form edit quest ===========
		$fquest = array('name'=>'fquest',
						'id'=>'fquest',
						'required'=>'required',
						'placeholder'=>'Question',
						'value'=>'',
						'rows'=>2,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fquest);
		
			$optjq=$this->Mq->optqtype();
		$fqtype = array('name'=>'fqtype',
						'id'=>'fqtype',
						'placeholder'=>'Question Type',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqtype,$optjq,'');
		
			$qcode='A';
			for($a=1;$a<30;$a++){
				$optqcode[$qcode]=$qcode;
				$qcode++;
			}
		
		$fqcode = array('name'=>'fqcode',
						'id'=>'fqgroup',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqcode,$optqcode,'');
		
			for($a=1;$a<50;$a++){
				$optqgroup[$a]=$a;
			}
		$fqgroup = array('name'=>'fqbundle',
						'id'=>'fqbundle',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker  form-control');
		$r[]=form_dropdown($fqgroup,$optqgroup,'');
						
		$fsend = array(	'id'=>'updatequestion',
						'value'=>'Add Question',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['insub'] = form_hidden('fsub',$idsub);
		
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
		
		$this->load->view('dashboard/org/question/addquest', $data);
	}
	
	
	public function addquesttype(){
		$idsub=$this->input->get('s');
	//============ form add answer ===========
		$dtqtype =$this->Mq->optqtype();
		unset($dtqtype['']);
		$r[] = '<label>'.implode(', ',$dtqtype).'</label>';
		
		$fqtype = array('name'=>'fqtype',
						'id'=>'fqtype',
						'required'=>'required',
						'placeholder'=>'Question Type',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fqtype);
		
		
		$fsend = array(	'id'=>'addqtype',
						'value'=>'Add Question Type',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['insub'] = form_hidden('fsub',$idsub);
		
		//set row title
		$col =['1','qtype'];
		$row = $this->returncolomn($col);
		$row[0] = 'Current Question Type';
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
		
		$this->load->view('dashboard/org/question/addquesttype', $data);
	}
	
	public function addanswer(){
		$idq=$this->input->get('q');
		$idsub=$this->input->get('s');
	//============ form add answer ===========
		$fans = array('name'=>'fans',
						'id'=>'fans',
						'required'=>'required',
						'placeholder'=>'Answer',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fans);
		
				$optkey = array(
						'0'=>"No",
						'1'=>"Yes"
						);
		$fkey = array('name'=>'fkey',
						'id'=>'fkey',
						'required'=>'required',
						'placeholder'=>'Key Answer',
						'class'=>'selectpicker form-control');
		$r[] = form_dropdown($fkey,$optkey,'0');
		
		$fsend = array(	'id'=>'addanswer',
						'value'=>'Add Answer',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidq',$idq);
		$data['insub'] = form_hidden('fids',$idsub);
		
		//set row title
		$col =['answer','key_ans'];
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
		
		$this->load->view('dashboard/org/question/addanswer', $data);
	}
	
	public function addattach(){
		$idq=$this->input->get('q');
		$idsub=$this->input->get('s');
		// variable input
		$fqpara = array('name'=>'fqparap',
						'id'=>'fqparap',
						'placeholder'=>'Paragraph/Passage',
						'value'=>'',
						'rows'=>5,
						'cols'=>30,
						'class'=>'form-control');
		$r[]=form_textarea($fqpara);
			$optftype = array(
						''=>'No File',
						'img'=>'Image',
						'mp3'=>'Music (Mp3)',
						'flash'=>'Video (Mp4)'
						);
		
		$fftype = array('name'=>'fftype',
						'id'=>'fftype',
						'data-live-search'=>'true',
						'class'=>'form-control');
		
		$r[]=form_dropdown($fftype,$optftype,'');
						
		$fqfile = array('name'=>'fqfile',
						'id'=>'fqfile',
						'type'=>'file',
						'class'=>'btn btn-info');
		$r[] =form_upload($fqfile);
		
		$fsend = array(	'id'=>'updateattach',
						'value'=>'Add Attachment',
						'class'=>'btn btn-primary',
						'type'=>'submit');
						
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidq',$idq);
		$data['insub'] = form_hidden('fidsub',$idsub);
		
		//set row title
		$col =['q_paragraph','q_filetype','q_file'];;
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
		
		$this->load->view('dashboard/org/question/addattach', $data);
	}
	
	public function detailattach(){
		if($this->input->get('att')!=null){
		$idattach =$this->input->get('att');
			$colattach=['q_paragraph','q_file','q_filetype'];
			$dbattach = $this->Mq->getattach($colattach,$idattach);
		
		// return row
		$row = $this->returncolomn($colattach);
		unset($row[2]);
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
					// view attachment
					if ($dbattach[0]['q_filetype']=='img'){
						$att = '<div id="attachprev" style="max-height:320px;max-height:240;"><img src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" class="img-rounded" height="260px"></div>';
					} else if ($dbattach[0]['q_filetype']=='mp3'){
						$att = '<div id="attachprev" style="margin:8% 0;"><audio controls="controls" preload="none">
						<source width="375" height="20" type="audio/mp3" src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" />
						</audio></div>';
					} else if ($dbattach[0]['q_filetype']=='flash'){
						$att = '<div id="attachprev"><video width="340" height="240" poster="'.base_url('upload/attach/poster.jpg').'" controls="controls" preload="none">
								<source type="video/mp4" src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" />
							</video></div>';
					} else {
						$att ='<i><b><span id="attachprev">No Attachment</span></b></i>';
					}
					
			$dtable[$a] = array(
				"dtcol"=>'<label class="col-sm-2">'.$key.' </label>',
				"dtval"=>'<div class="col-sm-10">'.$dbattach[0][$colattach[$a]].'</div>'
				);
			
			if ($row[$a]=='File Attachment'){
				$dtable[$a] = array(
				"dtcol"=>'<label class="col-sm-2">'.$key.' </label>',
				"dtval"=>'<div class="col-sm-10">'.$att.'</div>'
				);
			}
				
			$a++;
			}	
		
		$data['rdata']=$this->table->generate($dtable);
		// =============== view handler ============
		} else {
		$data['rdata']="No Question Attachment selected";
		}
		$this->load->view('dashboard/org/question/detailattach', $data);
	}
	
	public function detailquest(){
	$idq=$this->input->get('id');
		$idq=$this->input->get('id');
		$colq=['question','qtype','q_group','q_bundle','uname'];
		$g = $this->Mq->detailallquest($colq,$idq)[0];
		
		//set row title
		$row = $this->returncolomn($colq);
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
		//=========== generate add form available user =========================
		$this->table->set_template($tmpl);
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<label><b>'.$key.'</b></label>',
					"dtval"=>'<div>: '.$g[$colq[$a]].'</div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->load->view('dashboard/org/question/detailallquest', $data);
	}
	
	public function editquest(){
		$idq=$this->input->get('q');
		$idsub=$this->input->get('s');
		$colq=['question','idqtype','q_group','q_bundle'];
		$g = $this->Mq->detailquest($colq,$idq);
	//============ form edit quest ===========
		$fquest = array('name'=>'fquest',
						'id'=>'fquest',
						'required'=>'required',
						'placeholder'=>'Question',
						'value'=>$g[0]['question'],
						'rows'=>2,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fquest);
		
			$optjq=$this->Mq->optqtype();
		$fqtype = array('name'=>'fqtype',
						'id'=>'fqtype',
						'placeholder'=>'Question Type',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqtype,$optjq,$g[0]['idqtype']);
		
			$qcode='A';
			for($a=1;$a<30;$a++){
				$optqcode[$qcode]=$qcode;
				$qcode++;
			}
		
		$fqcode = array('name'=>'fqcode',
						'id'=>'fqgroup',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
		$r[]=form_dropdown($fqcode,$optqcode,$g[0]['q_group']);
		
			for($a=1;$a<50;$a++){
				$optqgroup[$a]=$a;
			}
		$fqgroup = array('name'=>'fqbundle',
						'id'=>'fqbundle',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'selectpicker  form-control');
		$r[]=form_dropdown($fqgroup,$optqgroup,$g[0]['q_bundle']);
						
		$fsend = array(	'id'=>'updatequestion',
						'value'=>'Update Question',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidq',$idq);
		$data['insub'] = form_hidden('fsub',$idsub);
		
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
		
		$this->load->view('dashboard/org/question/editquest', $data);
	}
	
	public function editanswer(){
		$idans=$this->input->get('ans');
		$idsub=$this->input->get('s');
		$colans=['answer','key_ans'];
		$g = $this->Mq->getanswer($colans,$idans);
	//============ form edit answer ===========
		$fans = array('name'=>'fans',
						'id'=>'fans',
						'required'=>'required',
						'placeholder'=>'Answer',
						'value'=>$g[0]['answer'],
						'class'=>'form-control');
		$r[] = form_input($fans);
		
				$optkey = array(
						'0'=>"No",
						'1'=>"Yes"
						);
		$fkey = array('name'=>'fkey',
						'id'=>'fkey',
						'required'=>'required',
						'placeholder'=>'Key Answer',
						'class'=>'selectpicker form-control');
		$r[] = form_dropdown($fkey,$optkey,$g[0]['key_ans']);
		
		$fsend = array(	'id'=>'updateanswer',
						'value'=>'Update Answer',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidans',$idans);
		$data['insub'] = form_hidden('fsub',$idsub);
		
		//set row title
		$col =['answer','key_ans'];
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
		
		$this->load->view('dashboard/org/question/editanswer', $data);
	}
	
	public function editattach(){
		if($this->input->get('att')!=null){
		$idattach =$this->input->get('att');
		$ids =$this->input->get('s');
			$colattach=['q_paragraph','1','q_filetype','q_file'];
			$dbattach = $this->Mq->getattach($colattach,$idattach);
		// variable input
		$fqpara = array('name'=>'fqparap',
						'id'=>'fqparap',
						'required'=>'required',
						'placeholder'=>'Pargraph/Passage',
						'value'=>$dbattach[0]['q_paragraph'],
						'rows'=>5,
						'cols'=>30,
						'class'=>'form-control');
		$r[]=form_textarea($fqpara);
			$optftype = array(
						''=>'No File',
						'img'=>'Image',
						'mp3'=>'Music (Mp3)',
						'flash'=>'Video (Mp4)'
						);
		
		$fftype = array('name'=>'fftype',
						'id'=>'fftype',
						'data-live-search'=>'true',
						'class'=>'selectpicker form-control');
			// view attachment
			if ($dbattach[0]['q_filetype']=='img'){
				$att = '<div id="attachprev" style="max-height:320px;max-height:240;"><img src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" class="img-rounded" height="260px"></div>';
			} else if ($dbattach[0]['q_filetype']=='mp3'){
				$att = '<div id="attachprev" style="margin:8% 0;"><audio controls="controls" preload="none">
				<source width="375" height="20" type="audio/mp3" src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" />
				</audio></div>';
			} else if ($dbattach[0]['q_filetype']=='flash'){
				$att = '<div id="attachprev"><video width="340" height="240" poster="'.base_url('upload/attach/poster.jpg').'" controls="controls" preload="none">
					<!-- MP4 for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7 -->
						<source type="video/mp4" src="'.base_url('upload/attach/'.$dbattach[0]['q_file']).'" />
					</video></div>';
			} else {
				$att ='<i><b><span id="attachprev">No Attachment</span></b></i>';
			}
		
		$r[]=$att;
		
		$r[]=form_dropdown($fftype,$optftype,$dbattach[0]['q_filetype']);
						
		$fqfile = array('name'=>'fqfile',
						'id'=>'fqfile',
						'type'=>'file',
						'class'=>'btn btn-info');
		$r[] =form_upload($fqfile);
		
		$fsend = array(	'id'=>'updateattach',
						'value'=>'Update Attachment',
						'class'=>'btn btn-primary',
						'type'=>'submit');
						
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidatt',$idattach);
		$data['insub'] = form_hidden('fidsub',$ids);
		
		// return row
		$row = $this->returncolomn($colattach);
		$row[1]='Attachment Preview';
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
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		} else {
		$data['rdata']="No Question Attachment selected";
		}
		$this->load->view('dashboard/org/question/editattach', $data);
	}
	
	public function editquesttype(){
		$idqt=$this->input->get('id');
		$colans=['qtype','qmanual'];
		$g = $this->Mq->getquesttype($colans,$idqt);
	//============ form edit answer ===========
		$fqtype = array('name'=>'fqtype',
						'id'=>'fqtype',
						'required'=>'required',
						'placeholder'=>'Question Type',
						'value'=>$g[0]['qtype'],
						'class'=>'form-control');
		$r[] = form_input($fqtype);

		$fqman = array('name'=>'fqman',
						'id'=>'fqman',
						'required'=>'required',
						'class'=>'form-control');
			$arrqman = array(
						'0'=>'No',
						'1'=>'Yes');
		$r[] = form_dropdown($fqman,$arrqman,$g[0]['qmanual']);
		
		
		$fsend = array(	'id'=>'updateqtype',
						'value'=>'Update Question Type',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fidqt',$idqt);
		
		//set row title
		$col =['qtype','qmanual'];
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
		
		$this->load->view('dashboard/org/question/editqtype', $data);
	}
	
	public function updatesubjectquestion(){
		$tot=null;
		if (($this->input->post('fid')!=null) and ($this->input->post('fidq[]')!=null)){
		$id = $this->input->post('fid');
			// catch each variable
			$tot = count($this->input->post('fidq[]'));
			$vq = 0; $xq=0;$vans = 0; $xans=0;$ians=0;
			foreach($this->input->post('fidq[]') as $k=>$sub){
				$idq = $this->input->post('fidq[]')[$k];
				$fdata= array(
						'uuser'=>$this->session->userdata('user'),
						'question'=>$this->input->post('fquest[]')[$k],
						'q_bundle'=>$this->input->post('fqbundle[]')[$k],
						'q_group'=>$this->input->post('fqcode[]')[$k],
						'idqtype'=>$this->input->post('fqtype[]')[$k]
						);
							// update answer
							$totans = $this->input->post('ftotans[]')[$k];
							$keyid = $this->input->post('foptkey[]')[$k];
							for($i=0;$totans>$i;$i++){
								$idans = $this->input->post('fidans[]')[$ians];
								($keyid == $idans) ? $valkey='1':$valkey='0';
								$fansdata= array(
									'answer'=>$this->input->post('fansq[]')[$ians],
									'key_ans'=> $valkey
									);
								$ians++;
								$hans = $this->Mq->updateanswer($fansdata,$idans);
								($hans) ? $vans++:$xans++;
							}
				
				$hsl = $this->Mq->updatequestiontest($fdata,$idq);
				($hsl) ? $vq++:$xq++;
			}
		}
		if ($tot!=null){
		$this->session->set_flashdata('v','Update '.$tot.' Subject Success. Details: ('.$vq.' Question '.$vans.' Answer) succcess and ('.$xq.' Question & '.$xans.' Answer) failed.');
		} else {		
		$this->session->set_flashdata('x','Update Question Test Failed. No Subject or Question to be updated.');
		}
		
		($id!=null) ? $url = '/editquestsubject?id='.$id : $url=null;
		redirect(base_url('Organizer/Question'.$url));
		
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
		redirect(base_url('Organizer/Question'));
	}
	
	public function updateattach(){
		$idsub='';
		if($this->input->post('fidatt')!=null){
		$idatt = $this->input->post('fidatt');
		$idsub = $this->input->post('fidsub');
			if ($this->input->post('fftype')=='img'){
				$ttype='jgp|jpeg|bmp|png|gif';
			} else if ($this->input->post('fftype')=='mp3'){
				$ttype='mp3';
			} else if ($this->input->post('fftype')=='flash'){
				$ttype='*';
			} else {
				$ttype='';
			}
		// config upload
            $config['upload_path'] = FCPATH.'upload/attach/';
            $config['allowed_types'] = $ttype;
            $config['max_size'] = 0;
            $this->load->library('upload', $config);
		
		// delete previous file if being update
			$prevfile = $this->Mq->checkattach($idatt);
			if (($prevfile!= $_FILES['fqfile']['name']) and ($_FILES['fqfile']['name']!= null)){
				//delete file
				$pathold = FCPATH.'upload/attach/'. $prevfile;
				unlink($pathold);
				
				//save new file
				($this->upload->do_upload('fqfile'))? $t='File Success Uploaded': $t='File Failed Uploaded'.$this->upload->display_errors();
				$fnew = $this->upload->data()['file_name'];
				
				// set new data variable
				$fdata = array(
					'q_paragraph'=>$this->input->post('fqparap'),
					'q_file'=>$fnew,
					'q_filetype'=>$this->input->post('fftype')
					);
			} else {
				$t='No New Attachment Uploaded';
				// new data without file
				$fdata = array(
					'q_paragraph'=>$this->input->post('fqparap')
					);
			}
				
			//update to database
			$hsl = $this->Mq->updateattach($fdata,$idatt);
			($hsl) ? $this->session->set_flashdata('v','Update Attachment Succes and '.$t.'.') : $this->session->set_flashdata('x','Edit Attachment Failed');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		($idsub!=null) ? $url = '/editquestsubject?id='.$idsub : $url=null;
		redirect(base_url('Organizer/Question'.$url));
	}
	
	public function updatequest(){
		$idsub='';
		if($this->input->post('fidq')!=null){
		$idq = $this->input->post('fidq');
		$idsub = $this->input->post('fsub');
				// set new data variable
				$fdata = array(
					'question'=>$this->input->post('fquest'),
					'idqtype'=>$this->input->post('fqtype'),
					'q_group'=>$this->input->post('fqcode'),
					'q_bundle'=>$this->input->post('fqbundle'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mq->updatequest($fdata,$idq);
			($hsl) ? $this->session->set_flashdata('v','Update Question Succes.') : $this->session->set_flashdata('x','Update Question Failed.');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		($idsub!=null) ? $url = '/editquestsubject?id='.$idsub : $url=null;
		redirect(base_url('Organizer/Question/allquestion'.$url));
	}
	
	public function updateqtype(){
		$idqt='';
		if($this->input->post('fidqt')!=null){
		$idqt = $this->input->post('fidqt');
				// set new data variable
				$fdata = array(
					'qtype'=>$this->input->post('fqtype'),
					'qmanual'=>$this->input->post('fqman')
					);
			//update to database
			$hsl = $this->Mq->updateqtype($fdata,$idqt);
			($hsl) ? $this->session->set_flashdata('v','Update Question Type Succes.') : $this->session->set_flashdata('x','Update Question Type Failed.');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		redirect(base_url('Organizer/Question/questiontype'));
	}
	
	public function savequestsubject(){
		if($this->input->post('fsub')!=null){
		$idsub = $this->input->post('fsub');
				// set new data variable
				$fdata = array(
					'idsubject'=>$idsub,
					'question'=>$this->input->post('fquest'),
					'idqtype'=>$this->input->post('fqtype'),
					'q_group'=>$this->input->post('fqcode'),
					'q_bundle'=>$this->input->post('fqbundle'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mq->addquest($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Question Succes.') : $this->session->set_flashdata('x','Add Question Failed.');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		redirect(base_url('Organizer/Question'));
	}
	
	public function savequest(){
		$idsub='';
		if($this->input->post('fsub')!=null){
		$idsub = $this->input->post('fsub');
				// set new data variable
				$fdata = array(
					'idsubject'=>$idsub,
					'question'=>$this->input->post('fquest'),
					'idqtype'=>$this->input->post('fqtype'),
					'q_group'=>$this->input->post('fqcode'),
					'q_bundle'=>$this->input->post('fqbundle'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mq->addquest($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Question Succes.') : $this->session->set_flashdata('x','Add Question Failed.');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		($idsub!=null) ? $url = '/editquestsubject?id='.$idsub : $url=null;
		redirect(base_url('Organizer/Question'.$url));
	}
	
	public function savequesttype(){
		$idsub='';
		if($this->input->post('fsub')!=null){
		$idsub = $this->input->post('fsub');
				// set new data variable
				$fdata = array(
					'qtype'=>$this->input->post('fqtype')
					);
			//update to database
			$hsl = $this->Mq->addquesttype($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Question Type Succes.') : $this->session->set_flashdata('x','Add Question Type Failed.');
			
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		($idsub!=null) ? $url = '/editquestsubject?id='.$idsub : $url=null;
		redirect(base_url('Organizer/Question'.$url));
	}
	
	public function saveanswer(){
		if (null!=$this->input->post('fidq')){
			$fdata = array (
						'answer' => $this->input->post('fans'),
						'idq' => $this->input->post('fidq'),
						'key_ans' => $this->input->post('fkey')
						);
			$r = $this->Mq->addanswer($fdata);
			
			if ($r){
			$this->session->set_flashdata('v','Add Answer Success');
			} else {		
			$this->session->set_flashdata('x','Add Answer Failed');
			}
		($this->input->post('fids')!=null) ? redirect(base_url('Organizer/Question/editquestsubject?id='.$this->input->post('fids'))): redirect(base_url('Organizer/Question/allquestion'));
		}else{
		$this->session->set_flashdata('x','No Question Selected, Add Answer Failed');
		redirect(base_url('Organizer/Question'));
		}
	}
	
	public function saveattach(){
	$idsub='';
		if($this->input->post('fidq')!=null){
		$idq = $this->input->post('fidq');
		$idsub = $this->input->post('fidsub');
		($_FILES['fqfile']['name']!=null) ? $fhashfile = md5($_FILES['fqfile']['name']) : $fhashfile =null ;
			if ($this->input->post('fftype')=='img'){
				$ttype='jpg|jpeg|bmp|png|gif';
			} else if ($this->input->post('fftype')=='mp3'){
				$ttype='mp3';
			} else if ($this->input->post('fftype')=='flash'){
				$ttype='*';
			} else {
				$ttype='';
			}
		// config upload
            $config['upload_path'] = FCPATH.'upload/attach/';
            $config['allowed_types'] = $ttype;
            $config['file_name'] = $fhashfile;
            $config['max_size'] = 0;
            $this->load->library('upload', $config);
		
		// save file to server
			if (($this->upload->do_upload('fqfile')) and ($fhashfile!=null)){
				$fnew = $this->upload->data()['file_name'];
				
				// set new attach variable
				$fdtatt = array(
					'q_paragraph'=>$this->input->post('fqparap'),
					'q_file'=>$fnew,
					'q_filetype'=>$this->input->post('fftype'),
					'idq'=>$idq
					);
				$t[]=('Upload Attachment Success');
			} else {
				// set new attach variable
				$fdtatt = array(
					'q_paragraph'=>$this->input->post('fqparap'),
					'idq'=>$idq
					);
				$t[]=('No Attachment Uploaded, '.$this->upload->display_errors());
			}
			
			//add to database
				$hsl = $this->Mq->addattach($fdtatt);
				if ($hsl) { 
					//update question attach
					$idatt = $this->Mq->getattachbyq('idattach',$idq);
					$fdtq = array(
							'idattach'=>$idatt[0]['idattach']
							);
					$updateq = $this->Mq->updatequestiontest($fdtq,$idq);
					$t[]='Add Question Attachment Success';
				} else{
					$t[]='Add Question Attachment Failed'; 
				}
				$this->session->set_flashdata('v',implode(' and ',$t));
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		($idsub!=null) ? $url = '/editquestsubject?id='.$idsub : $url=null;
		redirect(base_url('Organizer/Question'.$url));
	}
	
	public function updateanswer(){
		if (null!=$this->input->post('fidans')){
			$fdata = array (
						'answer' => $this->input->post('fans'),
						'key_ans' => $this->input->post('fkey')
						);
			$r = $this->Mq->updateanswer($fdata,$this->input->post('fidans'));
			if ($r){
			$this->session->set_flashdata('v','Edit Answer Success');
			} else {		
			$this->session->set_flashdata('x','Edit Answer Failed');
			}
			if (null!=$this->input->post('fsub')){
			redirect(base_url('Organizer/Question/editquestsubject?id='.$this->input->post('fsub')));
			} else {
			redirect(base_url('Organizer/Question/allquestion'));
			}
		}else{
		$this->session->set_flashdata('x','No Question Selected, Edit Answer Failed');
		redirect(base_url('Organizer/Question'));
		}
	}
	
	public function deletesubjecttest(){
		$s = $this->input->get('s');
		$t = $this->input->get('t');
		$r = $this->Mq->deletesubject($s,$t);
	if ($r){
		$this->session->set_flashdata('v','Delete Subject Test Success');
		} else{
		$this->session->set_flashdata('x','Delete Subject Test Failed');
		} 
		redirect(base_url('Organizer/Question/editsubjecttest?id='.$t));
	}

	public function deleteattach(){
		$s = $this->input->get('s');
		$t = $this->input->get('att');
			//catch data attach
			$fatt = $this->Mq->getattachfile($t);
			$this->load->helper('file');
			$pathatt = FCPATH.'upload/attach/'. $fatt;
			unlink($pathatt);
		$r = $this->Mq->deleteattach($t);
	if ($r){
		$this->session->set_flashdata('v','Delete Attachment Success');
		} else{
		$this->session->set_flashdata('x','Delete Attachment Failed');
		} 
		redirect(base_url('Organizer/Question/editquestsubject?id='.$s));
	}
	
	public function deletequest(){
		$q = $this->input->get('id');
			//delete attach
			$idt = $this->Mq->getattachbyq('idattach',$q)[0]['idattach'];
			$fatt = $this->Mq->getattachfile($idt);
			$pathatt = FCPATH.'upload/attach/'. $fatt;
			$this->load->helper('file');
			unlink($pathatt);
			$this->Mq->deleteattach($idt);
			//delete all sanswer
			$this->Mq->deleteallanswer($q);
			
		$r = $this->Mq->deletequest($q);
	if ($r){
		$this->session->set_flashdata('v','Delete Question (Answer & Attachment if any) Success');
		} else{
		$this->session->set_flashdata('x','Delete Question  (Answer & Attachment if any) Failed');
		} 
		redirect(base_url('Organizer/Question/allquestion'));
	}

	public function deleteanswer(){
		$s = $this->input->get('s');
		$ans = $this->input->get('ans');
		$r = $this->Mq->deleteanswer($ans);
	if ($r){
		$this->session->set_flashdata('v','Delete Answer Success');
		} else{
		$this->session->set_flashdata('x','Delete Answer Failed');
		} 
	($this->input->get('s')!=null) ? redirect(base_url('Organizer/Question/editquestsubject?id='.$this->input->get('s'))): redirect(base_url('Organizer/Question/allquestion'));
		
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
		redirect(base_url('Organizer/Question'));
	}
	
	public function returncolomn($header) {
	$find=['subject.idsubject','subject','question','idqtype','q_group','answer','key_ans','q_paragraph','q_filetype','q_file','q_bundle','qtype','uname','qmanual'];
	$replace = ['ID Subject','Subject Name', 'Question','Question Type','Question Code','Answer','Key Answer','Paragraph','Filetype Attachment','File Attachment','Group','Question Type','Last Updated by','Manual Correction'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}

}
