<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Confirmpay extends Mem_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mtransfer','Mpay','Msetting'));
    }

	public function index(){
		//===================== template content =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$idtmp = $this->Msetting->getset('tmppayproc');
		$this->load->model('Mtmp');
		$arrtmp = $this->Mtmp->detailtmp(array('tmpname','tmpcontent'),$idtmp);
		$data['tmptitle'] = $arrtmp[0]['tmpname'];
		$data['tmp'] = htmlspecialchars_decode($arrtmp[0]['tmpcontent']);

		//==================== price & unique code ===========
		$data['price']=$this->Msetting->getset('price');
		$this->load->model('Mpds');
		$data['code']=mb_substr($this->Mpds->detailpds(array('upaycode'),$this->session->userdata('user'))[0]['upaycode'],3,5);

		//================== Bank Name & Bank Account ========
		$data['bname']= $this->Msetting->getset('jns_bank');
		$data['accno']= $this->Msetting->getset('no_atm');
		$data['accname'] = $this->Msetting->getset('an_atm');
		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'');  
		// =============== view handler ============
		$data['title']="Payment Procedure";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pay/procedure', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	
	public function validationresult(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idttrans','tnotrans','ttdate','ttbank','ttname','ttamount','ttapprove'];
		$header = $this->returncolomn($column);
		unset($header[0]);unset($header[1]);
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
			$rows = $this->Mtransfer->countmytransferdata($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mtransfer->countmytransferdata();	
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
						'ttdate' => 'Date Requested',
						'ttbank' => 'Bank Name',
						'ttname' => 'Account Name',
						'ttnorek' => 'Account Number',
						'ttapprove' => 'Approve/Rejected(1/0)'
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
		$adv['Date Requested'] = form_input(
						array('name'=>'ttdate',
						'id'=>'createdon',
						'placeholder'=>'Date Requested',
						'value'=>isset($tempfilter['ttdate']) ? $tempfilter['ttdate'] : null,
						'class'=>'form-control'));
		
		$adv['Bank Name'] = form_input(
						array('name'=>'ttbank',
						'id'=>'bname',
						'placeholder'=>'Bank Account',
						'value'=>isset($tempfilter['ttbank']) ? $tempfilter['ttbank'] : null,
						'class'=>'form-control'));
		
		$adv['Account Name'] = form_input(
						array('name'=>'ttname',
						'id'=>'bacc',
						'placeholder'=>'Account Name',
						'value'=>isset($tempfilter['ttname']) ? $tempfilter['ttname'] : null,
						'class'=>'form-control'));
		
		$adv['Account Number'] = form_input(
						array('name'=>'ttnorek',
						'id'=>'bno',
						'placeholder'=>'Account Number',
						'value'=>isset($tempfilter['ttnorek']) ? $tempfilter['ttnorek'] : null,
						'class'=>'form-control'));
		
		$adv['Status'] = form_dropdown(array(
							'name'=>'ttapprove',
							'id'=>'status',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Approved',
							'2'=>'Rejected'),isset($tempfilter['ttapprove']) ? $tempfilter['ttapprove'] : null);
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Member/Confirmpay/validationresult?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Member/Confirmpay/validationresult?'.$addrpage.'view=';
		$data["perpage"] = ['2','5','10','50','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mtransfer->datamytransferdata($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				if($value['ttapprove']=='1'){
					$temp[$key]['ttapprove']='<span class="label label-success">Approved</span>';
				} else if($value['ttapprove']=='0'){
					$temp[$key]['ttapprove']='<span class="label label-danger">Rejected</span>';
				} else{
					$temp[$key]['ttapprove']='<span class="label label-warning">Pending</span>';
				}
				$temp[$key]['ttdate']=date('d-M-Y',strtotime($temp[$key]['ttdate'])).'<br/>';
				$temp[$key]['ttamount']=$this->convertmoney->convert($temp[$key]['ttamount']);
				//manipulation menu
				$btnprint = ($value['tnotrans']!='') ? ' <a href="'.base_url('Member/Confirmpay/previewinvoice?id=').$value['tnotrans'].'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Print Invoice" class="btn-info btn-sm"><i class="fa fa-print"></i> Print Invoice</a>' : '';
				$enc = $value['idttrans'];
				unset($temp[$key]['idttrans']);unset($temp[$key]['tnotrans']);
				$temp[$key]['menu']='<small><a href="'.base_url('Member/Confirmpay/detailtransfer?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a>'.$btnprint.'</small>';
				}
		$data['listdata'] = $this->table->generate($temp);
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'toggle/bootstrap2-toggle.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'toggle/bootstrap2-toggle.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Validation Result";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pay/result', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function sendrequest(){
		$fdata = array(
					'ttdate'=>date('Y-m-d',strtotime(str_replace('/','-',$this->input->post('ftdate')))),
					'ttbank'=>$this->input->post('ftbank'),
					'ttnorek'=>$this->input->post('faccno'),
					'ttname'=>$this->input->post('faccname'),
					'ttamount'=>$this->input->post('ftnomi'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$this->load->model('Mtransfer');
			$hsl = $this->Mtransfer->savetransfer($fdata);
			($hsl) ? $this->session->set_flashdata('v','Send Request Succes.') : $this->session->set_flashdata('x','Send Request Failed.');
			
		redirect(base_url('Member/Confirmpay/validationresult'));
	}


	public function requestvalidation(){
		// =========== total price ==========
		$data['price']=$this->Msetting->getset('price');
		$this->load->model('Mpds');
		$data['code']=mb_substr($this->Mpds->detailpds(array('upaycode'),$this->session->userdata('user'))[0]['upaycode'],3,5);
		// =========== column ============
		$col = ['Estimation Date','Bank Name','Account Number','Account Name','Nominal'];
		// ========= form edit ================ 
		$fdate = array('name'=>'ftdate',
						'id'=>'tdate',
						'required'=>'required',
						'placeholder'=>'Estimation Date of Transfer',
						'value'=>'',
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'required'=>'required',
						'data-inputmask' => "'alias': 'dd/mm/yyyy'",
						'datamask' => '');
		$r[] = form_input($fdate);
		
		$fbname = array('name'=>'ftbank',
						'id'=>'tbank',
						'required'=>'required',
						'placeholder'=>'Bank Name',
						'value'=>'',
						'class'=>'form-control',
						'size'=>'');
		$r[] = form_input($fbname);
		
		$faccno = array('name'=>'faccno',
						'id'=>'accno',
						'required'=>'required',
						'placeholder'=>'Account Number',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($faccno);
		
			$faccname = array('name'=>'faccname',
						'id'=>'accname',
						'placeholder'=>'Account Name',
						'value'=>'',
						'class'=>'form-control',
						'required'=>'required',
						);
		$r[] = form_input($faccname);
		
		$ftnomi = array('name'=>'ftnomi',
						'id'=>'tnomi',
						'type'=>'text',
						'required'=>'required',
						'placeholder'=>'Nominal Transfered',
						'value'=>'',
						'class'=>'form-control input-lg'
						);
		$r[] = '<div class="input-group"><span class="input-group-addon">Rp. </span>'.form_input($ftnomi).'</div><br/><span class="text-info"><i class="fa fa-info-circle"></i> Make sure it is equal with <b>total price</b></span>';
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Send Request',
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
		//=========== generate form =========================
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

		//============ previous request =======
		$header =['Est. Date','Amount','Status'];
		//set table template
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//data prevreq
		$prevreq = $this->Mtransfer->datatransfer(array('ttdate','ttamount','ttapprove'),1,1,array('a.uuser'=>$this->session->userdata('user')));
		$tmpreq = $prevreq;
		foreach($prevreq as $key=>$value){
			//manipulation allow data
			if($value['ttapprove']){
					$prevreq[$key]['ttapprove']='<span class="label label-success">Approved</span>';
				} else {
					$prevreq[$key]['ttapprove']='<span class="label label-danger">Rejected</span>';
				}
				$prevreq[$key]['ttdate']= date("d-M-Y",strtotime($value['ttdate']));
		}

		$data['prevreq']= (!empty($tmpreq)) ? $this->table->generate($prevreq) : "<i>No previous request</i>";
		
		//============ render content form ===========
		$this->load->model('Mtransfer');
		$totreq = $this->Mtransfer->countmytransfer(array('a.uuser'=>$this->session->userdata('user')));
		$data['totreq'] = $totreq;
		$data['lunas'] = $this->Mpds->detailpds(array('ulunas'),$this->session->userdata('user'))[0]['ulunas'];
		
		//========== filter regist date ==============
		$startpay = strtotime(str_replace('/', '-', $this->Msetting->getset('beginpay')));
		$endpay =  strtotime(str_replace('/', '-', $this->Msetting->getset('endpay')));
		$today = strtotime(date("d-m-Y"));
		$data['registperiod'] = (($today >= $startpay) and ($today <= $endpay)) ? true : false;
		$data['startpay'] = date('d-M-Y',$startpay);
		$data['endpay'] = date('d-M-Y', $endpay);
		//=============== Template ============
		$data['jsFiles'] = array('numeric/numeric.min');
		$data['cssFiles'] = array('');  
		// =============== view handler ============
		$data['title']="Request Validation";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pay/request', $data, TRUE);
		$this->load->view ('template/main', $data);	
	}
	
	public function detailtransfer(){
		//fecth data from db
		$col=['ttdate','ttdateapp','tnotrans','a.uname as mname','a.unim','ttbank','ttname','ttnorek','ttamount','a.upaycode','ttapprove','ttket'];
		$id = $this->input->get('id');
		$dbres = $this->Mtransfer->mydetailtransfer($col,$id,$this->session->userdata('user'));
		
		//set row title
		$row = $this->returncolomn($col);
		$col[3]='mname';
		$col[4]='unim';
		$col[9]='upaycode';
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
		$stat = ($dbres[0]['ttapprove']) ? '<span class="label label-success">Approved</span>' : ($dbres[0]['ttapprove']==null) ? '<span class="label label-warning">Pending</span>' : '<span class="label label-danger">Rejected</span>';
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			
			if (($key=='Status')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.$stat
						);
					} 
			if (($key=='Amount')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <b>'.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b>'
						);
					}
			
			if (($key=='Invoice No')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <a href="'.base_url("Member/Confirmpay/previewinvoice?id=".$dbres[0][$col[$a]]).'" id="openprev">'.$dbres[0][$col[$a]].'</a>'
						);
					}
			if (($key=='Date Transfered')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date("d-M-Y", strtotime($dbres[0][$col[$a]]))
						);
					}
			if (($key=='Date Processed')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date("d-M-Y H:i", strtotime($dbres[0][$col[$a]]))
						);
					}
			
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/mem/pay/detailtransfer', $data);		
	}

	
	public function payment(){
		$this->load->model('Mpay');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idtrans','tdate','tnotrans','transname','tpaid'];
		$header = $this->returncolomn2($column);
		unset($header[0]);
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
			$rows = $this->Mpay->countmypay($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mpay->countmypay();	
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
						'tdate' => 'Date Issued',
						'tnotrans' => 'Invoice Number',
						'tnomi' => 'Cash Given',
						'tpaid' => 'Nominal Paid',
						'pic' => 'Approved by',
						'valid_to' => 'Valid Date'
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
		$adv['Date Issued '] = form_input(
						array('name'=>'tdate',
						'id'=>'sissuedon',
						'placeholder'=>'Date Issued (YYYY-MM-DD)',
						'value'=>isset($tempfilter['tdate']) ? $tempfilter['tdate'] : null,
						'class'=>'form-control'));
		
		$adv['Invoice Number'] = form_input(
						array('name'=>'tnotrans',
						'id'=>'snotrans',
						'placeholder'=>'Invoice Number',
						'value'=>isset($tempfilter['tnotrans']) ? $tempfilter['tnotrans'] : null,
						'class'=>'form-control'));
		
		$adv['Nominal Paid'] = form_input(
						array('name'=>'tpaid',
						'id'=>'spaid',
						'placeholder'=>'Nominal Paid',
						'value'=>isset($tempfilter['tpaid']) ? $tempfilter['tpaid'] : null,
						'class'=>'form-control'));
		
		$adv['Cash Given'] = form_input(
						array('name'=>'tnomi',
						'id'=>'scash',
						'placeholder'=>'Cash Given',
						'value'=>isset($tempfilter['tnomi']) ? $tempfilter['tnomi'] : null,
						'class'=>'form-control'));
		
		$adv['Nominal Change'] = form_input(
						array('name'=>'tchange',
						'id'=>'schange',
						'placeholder'=>'Nominal Change',
						'value'=>isset($tempfilter['tchange']) ? $tempfilter['tchange'] : null,
						'class'=>'form-control'));
						
			$opttrans = $this->Mpay->optjtrans();
		$adv['Transaction Type'] = form_dropdown(
						array('name'=>'idjnstrans',
						'id'=>'sjnstrans',
						'placeholder'=>'Transaction Type',
						'value'=>'',
						'class'=>'form-control'),$opttrans,isset($tempfilter['idjnstrans']) ? $tempfilter['idjnstrans'] : null);
		
		$adv['Fully Paid/Not'] = form_dropdown(array(
							'name'=>'ulunas',
							'id'=>'slunas',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Fully Paid',
							'0'=>'Not Yet'),isset($tempfilter['ulunas']) ? $tempfilter['ulunas'] : null);
		$adv['Processed by'] = form_input(
						array('name'=>'pic',
						'id'=>'spicname',
						'placeholder'=>'PIC Name',
						'value'=>isset($tempfilter['pic']) ? $tempfilter['pic'] : null,
						'class'=>'form-control'));
		$adv['Invoice Valid Until'] = form_input(
						array('name'=>'valid_to',
						'id'=>'svalid',
						'placeholder'=>'Date Valid (YYYY-MM-DD)',
						'value'=>isset($tempfilter['valid_to']) ? $tempfilter['valid_to'] : null,
						'class'=>'form-control'));
		
		$dtfilter = '';
		
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Member/Confirmpay/payment?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Member/Confirmpay/payment?'.$addrpage.'view=';
		$data["perpage"] = ['2','5','10','50','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mpay->datamypay($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				$temp[$key]['tdate']=date('d-M-Y H:i:s',strtotime($temp[$key]['tdate']));
				$temp[$key]['tpaid']=$this->convertmoney->convert($temp[$key]['tpaid']);
				$temp[$key]['tnotrans']='<span class="idname">'.$temp[$key]['tnotrans'].'</span>';
				$temp[$key]['tdate']=date('d-M-Y', strtotime($value['tdate'])).'<br/>'.date('H:i:s', strtotime($value['tdate']));
				//manipulation menu
				$enc = $value['tnotrans'];
				unset($temp[$key]['idtrans']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Member/Confirmpay/detailpay?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Member/Confirmpay/previewinvoice?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Print Invoice" class="btn btn-info btn-sm" title="Print"><i class="fa fa-print"></i></a></div>';
				}
		$data['listdata'] = $this->table->generate($temp);
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="My Payment";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/pay/payment', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function detailpay(){
		//fecth data from db
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		$col = [$sumpaid,'a.ulunas','tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to'];
		$id = $this->input->get('id');
		$dbres = $this->Mpay->mydetailpay($col,$id,$this->session->userdata('user'));
		
		//set row title
		$row = $this->returncolomn2($col);
		$row[0]='Total Paid';
		$col[0]='totpaid';
		$row[1]='Full Paid Status';
		$col[1]='ulunas';
		$col[5]='mname';
		$col[6]='unim';
		$col[10]='rname';
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
		array_unshift($row,'QR Code'); //set qr label 
		array_unshift($col,'QR Code'); //set qr label
		if (is_array($dbres[0])){
				$notrans = $dbres[0]['tnotrans'];
				$dbres[0]['QR Code']= '<img src="'.base_url('upload/qr/'.$notrans).'.png" class="img-thumbnail" style="height:100px;"/>'; //set qr data 		
				
				$a = 0;
				foreach($row as $key)
				{
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.$dbres[0][$col[$a]]
						);
					
					if (($key=='Cash Given') or ($key=='Nominal Paid') or ($key=='Nominal Change')){
							$dtable[$a] = array(
								"dtcol"=>'<b>'.$key.'</b>',
								"dtval"=>' : <b>'.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b>'
								);
							}
					if (($key=='Total Paid')){
							$dtable[$a] = array(
								"dtcol"=>'<h3 class="bg-green"><b>'.$key.'</b></h3>',
								"dtval"=>'<h3 class="text-success"><b>: '.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b></h3>'
								);
						}
					
					if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='1')){
							$dtable[$a] = array(
								"dtcol"=>'<b>'.$key.'</b>',
								"dtval"=>' : <span class="label label-success">Fully Paid</span>'
								);
							} else if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='0')){
							$dtable[$a] = array(
								"dtcol"=>'<b>'.$key.'</b>',
								"dtval"=>' : <span class="label label-warning">Not Yet</span>'
								);
							}
					if (($key=='Invoice Issued') or ($key=='Valid To')){
							$dtable[$a] = array(
								"dtcol"=>'<b>'.$key.'</b>',
								"dtval"=>' : '.date("d-M-Y H:i:s",strtotime($dbres[0][$col[$a]]))
								);
							}
					$a++;
				}
				$data['rdata']='<a href="'.base_url('Member/Confirmpay/previewinvoice?id=').$dbres[0]['tnotrans'].'" class="btn btn-block btn-primary" id="previnvo"><i class="fa fa-print"></i> Preview Invoice</a>'.$this->table->generate($dtable);
			} else {
				$data['rdata']= '<h4 class="text-center">No Details</h4>';
			}
		$data['id']=$id;
		// =============== view handler ============
		$this->load->view('dashboard/mem/pay/detailpay', $data);		
	}

	public function previewinvoice(){
		if ($this->input->get('id')!=null){
			$col = ['a.ulunas','tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to'];
			$id = $this->input->get('id');
			$dbres = $this->Mpay->myinvoice($col,$id);
			//set row title
			$row = $this->returncolomn2($col);
			$row[0]='Full Paid Status';
			$col[0]='ulunas';
			$col[4]='mname';
			$col[5]='unim';
			$col[9]='rname';
			
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
		array_unshift($row,'QR Code'); //set qr label 
		array_unshift($col,'QR Code'); //set qr label
		$notrans = $dbres[0]['tnotrans'];
		$name = $dbres[0]['mname'];
		$dbres[0]['QR Code']= '<img src="'.base_url('upload/qr/'.$notrans).'.png" class="img-thumbnail" style="height:100px;"/><br/><small><i>Scan QR Code to find out your Invoice Number.</i></small>'; //set qr data 		
		
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<label>'.$key.'</label>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			
			if (($key=='Cash Given') or ($key=='Nominal Paid') or ($key=='Nominal Change')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <b>'.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b>'
						);
					}
			if (($key=='Total Paid')){
					$dtable[$a] = array(
						"dtcol"=>'<h3 class="bg-green"><b>'.$key.'</b></h3>',
						"dtval"=>'<h3 class="text-success"><b>: '.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b></h3>'
						);
				}
			
			if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='1')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-success">Fully Paid</span>'
						);
					} else if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='0')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-warning">Not Yet</span>'
						);
					}
			if (($key=='Invoice Issued') or ($key=='Valid To')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date("d-M-Y H:i:s",strtotime($dbres[0][$col[$a]]))
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		//===== parse several variables ==========
		$data['notrans'] = $dbres[0]['tnotrans'];
		$data['pic'] = $dbres[0]['rname'];
		$data['printlink'] = base_url('Member/Confirmpay/printinvoice?id='.$dbres[0]['tnotrans']);
			$this->load->view('dashboard/mem/pay/previewinvoice', $data);
			
		} else {
			$this->session->set_flashdata('x',"No data selected, Print Invoice failed.");
			redirect(base_url("Member/Confirmpay/validationresult"));
		}	
	}

	public function printinvoice(){	
		if ($this->input->get('id')!=null){
			$col = ['a.ulunas','tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to'];
			$id = $this->input->get('id');
			$dbres = $this->Mpay->mydetailpay($col,$id,$this->session->userdata('user'));
			//set row title
			$row = $this->returncolomn2($col);
			$row[0]='Full Paid Status';
			$col[0]='ulunas';
			$col[4]='mname';
			$col[5]='unim';
			$col[9]='rname';
			
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
		array_unshift($row,'QR Code'); //set qr label 
		array_unshift($col,'QR Code'); //set qr label
		$notrans = $dbres[0]['tnotrans'];
		$name = $dbres[0]['mname'];
		$dbres[0]['QR Code']= '<img src="'.base_url('upload/qr/'.$notrans).'.png" class="img-thumbnail" style="height:100px;"/><br/><small><i>Scan QR Code to find out your Invoice Number.</i></small>'; //set qr data 		
		
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<label>'.$key.'</label>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			
			if (($key=='Cash Given') or ($key=='Nominal Paid') or ($key=='Nominal Change')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <b>'.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b>'
						);
					}
			if (($key=='Total Paid')){
					$dtable[$a] = array(
						"dtcol"=>'<h3 class="bg-green"><b>'.$key.'</b></h3>',
						"dtval"=>'<h3 class="text-success"><b>: '.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b></h3>'
						);
				}
			
			if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='1')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-success">Fully Paid</span>'
						);
					} else if (($key=='Full Paid Status') and ($dbres[0][$col[$a]]=='0')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-warning">Not Yet</span>'
						);
					}
			if (($key=='Invoice Issued') or ($key=='Valid To')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date("d-M-Y H:i:s",strtotime($dbres[0][$col[$a]]))
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->session->set_flashdata('v',"Print Invoice ".$name." success");
		$this->payment();
		$this->session->set_flashdata('v',null);
		
		//===== parse several variables ==========
		$data['notrans'] = $dbres[0]['tnotrans'];
		$data['pic'] = $dbres[0]['rname'];
			$this->load->view('dashboard/mem/pay/printinvoice', $data);
			
		} else {
			$this->session->set_flashdata('x',"No data selected, Print Invoice failed.");
			redirect(base_url("Member/Confirmpay/payment"));
		}
	}

	public function checklunas($user=null){
		$this->load->model('Mpay');
		$price = $this->Msetting->getset('price');
		if ($user!=null){
		$r = $this->Mpay->checkfullpaid($user);
			if ($r>=$price){
			$h = $this->Mpay->updatefullpaid($user,1);
			} else {
			$h = $this->Mpay->updatefullpaid($user,0);
			}
		} else{
			$alluser = $this->Mpay->getalluser();
			$tot=count($alluser);
			foreach($alluser as $k=>$val){
				$r = $this->Mpay->checkfullpaid($val['uuser']);
				if ($r>=$price){
				$h = $this->Mpay->updatefullpaid($val['uuser'],1);
				} else {
				$h = $this->Mpay->updatefullpaid($val['uuser'],0);
				}
			}
			$this->session->set_flashdata('v',"Update Full Paid status of ".$tot." User(s) success");
			$this->index();
			$this->session->set_flashdata('v',null);
		
		}
	}
	
	
	public function returncolomn($header) {
		$find=['idttrans','ttdateapp','tnotrans','ttdate','a.uname as mname','a.uuser','a.unim','ttbank','ttname','ttnorek','ttamount','a.upaycode','ttapprove','b.uname as rname','ttket'];
		$replace = ['Transfer ID','Date Processed','Invoice No','Date Transfered','Full Name','Username','NIM','Bank Account','Account Name','Account Number','Amount','Payment Code','Status','Processed by','Notes'];
			foreach ($header as $key => $value){
			$header[$key]  = str_replace($find, $replace, $value);
			}
		return $header;
	}

	public function returncolomn2($header) {
		$find=['tnotrans','tdate','a.uname as mname','a.uuser','a.unim','tnomi','tpaid','tchange','transname','b.uname as rname','valid_to'];
		$replace = ['Invoice No.','Invoice Issued','Fullname','Username','NIM','Cash Given','Nominal Paid','Nominal Change','Transaction Type','Processed by','Valid To'];
			foreach ($header as $key => $value){
			$header[$key]  = str_replace($find, $replace, $value);
			}
		return $header;
	}
}
