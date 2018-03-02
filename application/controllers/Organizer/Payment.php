<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mpay','Msetting'));
    }

	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idtrans','tdate','tnotrans','a.uname as mname','a.unim','transname','tpaid','b.uname as rname'];
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
			$rows = $this->Mpay->countpay($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mpay->countpay();	
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
						'uname' => 'Name',
						'unim' => 'NIM',
						'pic' => 'PIC',
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
		$adv['Period'] = form_input(
						array('name'=>'period',
						'id'=>'period',
						'placeholder'=>'Period',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
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
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'sfullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['NIM'] = form_input(
						array('name'=>'unim',
						'id'=>'sNIM',
						'placeholder'=>'NIM',
						'value'=>isset($tempfilter['unim']) ? $tempfilter['unim'] : null,
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
		$adv['PIC Name'] = form_input(
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
				'base_url' => base_url().'/Organizer/Payment?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Payment?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mpay->datapay($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				$temp[$key]['tdate']=date('d-M-Y H:i:s',strtotime($temp[$key]['tdate']));
				$temp[$key]['tpaid']=$this->convertmoney->convert($temp[$key]['tpaid']);
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idtrans']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['tnotrans']='<span class="idname hidden"> '.$temp[$key]['tnotrans'].', '.$temp[$key]['mname'].' ('.$temp[$key]['tpaid'].')</span>'.$temp[$key]['tnotrans'];
				$temp[$key]['tdate']=date('d-M-Y', strtotime($value['tdate'])).'<br/>'.date('H:i:s', strtotime($value['tdate']));
				//manipulation menu
				$enc = $value['idtrans'];
				unset($temp[$key]['idtrans']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Payment/detailpay?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Payment/editpay?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Payment/deletepay?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i> </a></div>';
				}
		$data['listpay'] = $this->table->generate($temp);
		
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
			$data['factselected'] = site_url('Organizer/Payment/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Payment/importxls');
			
			
		
		// ============= export form ==============
			$optcol = 	array(
						'tnotrans' => 'Invoice Number',
						'tdate' => 'Date Issued',
						'transname' => 'Transaction Type',
						'a.uname as mname' => 'Member Name',
						'a.unim' => 'Member NIM',
						'tpaid' => 'Nominal Paid',
						'tnomi' => 'Cash Given',
						'tchange' => 'Nominal Change',
						'b.uname as rname' => 'PIC',
						'valid_to' => 'Valid Date',
						'totpaid' => 'Total Paid',
						'a.ulunas' => 'Full Paid Status'
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
			$data['fbtnexport']= form_submit(array('value'=>'Export',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factexp'] = site_url('Organizer/Payment/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Payment/printpay');
		
		//=============== setting registration phase ============
			$price = $this->Msetting->getset('price');
			$data['fprice']= form_input(array('id'=>'price',
								'class'=>'form-control',						
								'name'=>'fprice',							
								'placeholder'=>'Registration Price',							
								'value'=>$price,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Payment/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Payment Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/pay/paylist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '100000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatPaymentData.xls')) {
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
              $notrans = Array();
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
					$id = $objWorksheet->getCell('A'.($i+1))->getValue();
					$no= $this->generatenotrans($id);
					// check unique no trans
					if(!(in_array($notrans,$no))){
						$notrans[]= $no;
					} else {
						$no= $this->generatenotrans($id);
						$notrans[]= $no;
					}
					//=====  create qr code ========
					$this->qr($no);
				   $vto = new DateTime('+'.$objWorksheet->getCell('F'.($i+1))->getValue().' months');
                   $dtxl[$i-1]['uuser'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['tnotrans'] = $no;
                   $dtxl[$i-1]['idjnstrans'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['tpaid'] = $objWorksheet->getCell('C'.($i+1))->getValue();
                   $dtxl[$i-1]['tnomi'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['tchange'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['valid_to'] = $vto->format("Y-m-d H:i:s");
                   $dtxl[$i-1]['use_uuser'] = $this->session->userdata('user');
				 }
              }
			  
			  //save data through model
			  $report = $this->Mpay->importdata($dtxl);
              
			  //set flashdata
				$flashdata = 'Import '.$report['success'].' Data Success, with '.$report['failed'].' unsuccessful import.';
				if ($report['faillist']<>''){
				$flashdata = $flashdata."<br/>Data error: <br/>".$report['faillist'];
				}
				// update lunas status
				$this->checklunas();
				$this->session->set_flashdata('v',$flashdata);
            }
		
		//delete file
        $file = $this->upload->data()['file_name'];
        $path = FCPATH.'temp_upload/' . $file;
        unlink($path);			
		//redirect to data list
		redirect(base_url('Organizer/Payment'));
        
    }
	
	public function exportxls(){
		//catch column value
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		if ($this->input->post('fcolomn')!=null){
			foreach($this->input->post('fcolomn') as $selected)
			{ 
				if ($selected != 'totpaid'){
				$dtcol[] = $selected;
				} else {
				$dtcol[] = $sumpaid;
				}
			}
		} else {
		$dtcol = ['tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to',$sumpaid,'a.ulunas'];
		}
		
		//check use date range
		$dexp = array();
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpay->exportpay($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpay->exportpay(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		$dtcol[3]='Member Name';
		$dtcol[4]='Member NIM';
		$dtcol[8]='PIC';
		$dtcol[10]='Total Paid';
		$dtcol[11]='Full Paid Status';
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('PaymentData');
	
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
			foreach($dexp as $key=>$val){
					$Dcol = "A";
				//manipulate data
				if(array_key_exists('ulunas',$val)){
							if ($val['ulunas']==1){
							$val['ulunas']='Fully Paid';
							}else{
							$val['ulunas']='Not Yet';
							}
				}
					foreach ($val as $k){
						$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$k);
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PAYMENT DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="PAYMENT Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Username','Transaction Type','Nominal Paid','Cash Given','Nominal Change','Valid to (Month)']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportPaymentData');
		
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
		
		//set colomn header Member Data
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Username Member");			
		$objPHPExcel->getActiveSheet()->mergeCells($tempcol.($Hrow).':'.chr(ord($Dcol)+1).$Hrow);
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow+1),"Username");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow+1),"Information");			
			$dtmem= $this->Mpay->optalluser();
			unset($dtmem['']);
		$Memrow=$Hrow+2;
		foreach ($dtmem as $k=>$v){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Memrow,$k);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Memrow,$v);
			$Memrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Memrow-1),"Remember: Always Put 'Username' instead of 'Name/Email/other info' in Colomn 'Username'");			
		
		//set colomn header Transaction Type
		$Grow = $Memrow+1;
		$Gcol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow),"Transaction Type");			
		$objPHPExcel->getActiveSheet()->getStyle($Gcol.$Grow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->mergeCells($Gcol.($Grow).':'.chr(ord($Gcol)+1).$Grow);
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow+1),"Transaction Type ID");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).($Grow+1),"Value");			
			$dtjpay= $this->Mpay->optjtrans();
			unset($dtjpay['']);
		$Grow = $Grow+2;
		foreach ($dtjpay as $k=>$v){
			$Gcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Gcol.$Grow,$k);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).$Grow,$v);
			$Grow++;
		}
		$lastcol = chr(ord($Gcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Grow-1),"Remember: Always Put 'Transaction Type ID' instead of 'Value' in Colomn 'Transaction Type'");			
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Grow-1))->applyFromArray($fillArray);
		
		//Freeze pane
		//$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Grow));
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatPaymentData.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function detailpay(){
		//fecth data from db
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		$col = [$sumpaid,'a.ulunas','tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to'];
		$id = $this->input->get('id');
		$dbres = $this->Mpay->detailpay($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
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
		$data['rdata']=$this->table->generate($dtable);
		$data['id']=$id;
		// =============== view handler ============
		$this->load->view('dashboard/org/pay/detailpay', $data);
		
		
	}
	
	public function addpay(){
		//============ form add pds available account ===========
			$optjtrans = $this->Mpay->optjtrans();
		$fjtrans = array('name'=>'fpiltrans',
						'id'=>'TransactionType',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fpiltrans'),
						'class'=>'form-control selectpicker');
		$data['piljtrans'] = form_dropdown($fjtrans,$optjtrans,'');
		
			$optuser = $this->Mpay->optalluser();
		$fuser = array('name'=>'fuser',
						'id'=>'User',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fuser'),
						'class'=>'form-control selectpicker');
		$data['piluser'] = form_dropdown($fuser,$optuser,'');
		
		$fnotrans = array('name'=>'fno',
						'id'=>'NoTrans',
						'required'=>'required',
						'placeholder'=>'Invoice Number',
						'value'=>'',
						'class'=>'form-control',
						'disabled'=>'disabled');
		$data['notrans'] = form_input($fnotrans);
		
			$fnomi = array('name'=>'fnomi',
						'id'=>'nomi',
						'required'=>'required',
						'placeholder'=>'Nominal Given',
						'value'=>set_value('fnomi'),
						'class'=>'form-control input-lg');
		$data['nomi'] = form_input($fnomi);
		
			$fpaid = array('name'=>'fpaid',
						'id'=>'paid',
						'required'=>'required',
						'placeholder'=>'Nominal Paid',
						'value'=>set_value('fpaid'),
						'class'=>'form-control input-lg');
		$data['paid'] = form_input($fpaid);
		
			$fret = array('name'=>'fret',
						'id'=>'change',
						'required'=>'required',
						'placeholder'=>'Nominal Change',
						'value'=>set_value('fret'),
						'class'=>'form-control input-lg');
		$data['ret'] = form_input($fret);
		
			$fvto = array('name'=>'fvto',
						'id'=>'validto',
						'required'=>'required',
						'placeholder'=>'Valid Until (in Month)',
						'value'=>set_value('fvto'),
						'class'=>'form-control');
		$data['vto'] = form_input($fvto);
		$data['vtoo']=form_hidden('fvtoo');
		$data['notran']=form_hidden('fnotrans');
		$data['met']=form_hidden('fredi');
		
		$fsend = array(	'id'=>'redi',
						'value'=>'Save, Add more',
						'class'=>'btn btn-primary btn-lg',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$fsen = array(	'id'=>'submit',
						'value'=>'Save',
						'class'=>'btn btn-info btn-lg',
						'type'=>'submit');
		$data['inred'] = form_submit($fsen);
		
		//set row title
		$col = ['tnotrans','transname','Member Name','tnomi','tpaid','tchange','valid_to'];
		$data['col'] = $this->returncolomn($col);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','numeric/numeric.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min');  
		// =============== view handler ============
		$data['title']="Add Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/pay/addpay', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function editpay(){					
		// ============== Fetch data ============
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		$col = [$sumpaid,'a.ulunas','tnotrans','tdate','transaksi.idjnstrans','a.uname as mname','a.unim','tpaid','tnomi','tchange','valid_to'];
		$id = $this->input->get('id');
		$g = $this->Mpay->detailpay($col,$id);
		$stat = $g[0]['ulunas'];
		unset($col[1]);
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.$g[0]['totpaid'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['tnotrans'].'</label>';
		$r[] = '<label class="form-control" disabled id="issued">'.$g[0]['tdate'].'</label>';
			
			$optjtrans = $this->Mpay->optjtrans();
		$fjtrans = array('name'=>'fpiltrans',
						'id'=>'TransactionType',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fpiltrans'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($fjtrans,$optjtrans,$g[0]['idjnstrans']);
		
		$r[] = '<label class="form-control" disabled>'.$g[0]['mname'].'</label>';
		
		$r[] = '<label class="form-control" disabled>'.$g[0]['unim'].'</label>';	
			$fpaid = array('name'=>'fpaid',
						'id'=>'paid',
						'required'=>'required',
						'placeholder'=>'Nominal Paid',
						'value'=>$g[0]['tpaid'],
						'class'=>'form-control input-lg');
		$r[] = form_input($fpaid);
		
		$fnomi = array('name'=>'fnomi',
						'id'=>'nomi',
						'required'=>'required',
						'placeholder'=>'Nominal Given',
						'value'=>$g[0]['tnomi'],
						'class'=>'form-control input-lg');
		$r[] = form_input($fnomi);
		
			$fret = array('name'=>'fret',
						'id'=>'change',
						'required'=>'required',
						'placeholder'=>'Nominal Change',
						'value'=>$g[0]['tchange'],
						'class'=>'form-control input-lg');
		$r[] = form_input($fret);
		
			$fvto = array('name'=>'fvto',
						'id'=>'validto',
						'required'=>'required',
						'placeholder'=>'Valid To (in Month)',
						'value'=>'',
						'class'=>'form-control');
		$r[] = form_input($fvto);
		
		$data['inid'] = form_hidden('fidtrans',$id);
		$data['invto'] = form_hidden('fvtoo',$g[0]['valid_to']);
		$data['instat'] = form_hidden('flunas',$stat);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($col);
		$row[0]="Total Paid";
		$row[4]="Transaction Type";
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
		
		$this->load->view('dashboard/org/pay/editpay', $data);
	
	
	}
	
	public function updatepay(){
		if ($this->input->post('fidtrans')!=null){
		$id = $this->input->post('fidtrans');
		$fdata = array (
					'idjnstrans' => $this->input->post('fpiltrans'),
					'tnomi' => $this->input->post('fnomi'),
					'tpaid' => $this->input->post('fpaid'),
					'tchange' => $this->input->post('fret'),
					'valid_to' => $this->input->post('fvtoo'),
					'use_uuser' => $this->session->userdata('user')
					);
		$r = $this->Mpay->updatepay($fdata,$id);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Payment Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Payment Data Failed');
		}
		redirect(base_url('Organizer/Payment'));
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
					$res= $this->Mpay->deletepay($v);
					($res) ? $s++:$x++;
				}
			}
				
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Payment Data Success.<br/>Details: '.$s.' success and '.$x.' error(s)');
		} else{
		$this->session->set_flashdata('x','No Data Selected, Update Selected Payment Data Failed.');
		}
		redirect(base_url('Organizer/Payment'));
	}
		
	public function savepay(){
	if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$red = $this->input->post('fredi');
		$fdata = array (
					'tdate' => date("Y-m-d H:i:s"),
					'idjnstrans' => $this->input->post('fpiltrans'),					
					'uuser' => $us,
					'use_uuser' => $this->session->userdata('user'),
					'tnotrans' => $this->input->post('fnotrans'),
					'tnomi' => $this->input->post('fnomi'),
					'tpaid' => $this->input->post('fpaid'),
					'tchange' => $this->input->post('fret'),
					'valid_to' => $this->input->post('fvtoo')
					);
		$this->qr($this->input->post('fnotrans'));
		$r = $this->Mpay->savepayment($fdata);
	}
		if ($r){
			$this->checklunas($us);
			//======= set notif to member ========
			$idnotifmem = $this->Msetting->getset('notifpayment');
			$this->notifications->pushnotif(array('idnotif'=>$idnotifmem,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$us,'nlink'=>null));
			$this->session->set_flashdata('v','Add Payment Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Payment Data Failed');
		}
		
		(null==$red) ? redirect(base_url('Organizer/Payment')) : redirect(base_url('Organizer/Payment/addpay'));
	
	}

	public function deletepay(){
		$id = $this->input->get('id');
		$this->Mpay->updatetransbyidpay($id);
		$r = $this->Mpay->deletepay($id);
			// delete qr and update status full paid
			$this->deleteqr($id);
			$us = $this->Mpay->getusertrans($id);
			$this->checklunas($us);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Organizer/Payment'));
	}

	public function printpay(){
		//catch column value
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		if ($this->input->post('fcolomn')!=null){
			foreach($this->input->post('fcolomn') as $selected)
			{ 
				if ($selected != 'totpaid'){
				$dtcol[] = $selected;
				} else {
				$dtcol[] = $sumpaid;
				}
			}
		} else {
		$dtcol = ['tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to',$sumpaid,'a.ulunas'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpay->exportpay($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpay->exportpay(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		// config table
		$dtcol[3]='Member Name';
		$dtcol[4]='Member NIM';
		$dtcol[8]='PIC';
		$dtcol[10]='Total Paid';
		$dtcol[11]='Full Paid Status';
		$header = $this->returncolomn($dtcol);
		
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
				foreach($dexp as $key=>$val){
					//manipulation allow data
					if(array_key_exists('ulunas',$val)){
						if ($val['ulunas']==1){
						$dexp[$key]['ulunas']='Fully Paid';
						}else{
						$dexp[$key]['ulunas']='Not Yet';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Payment Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/pay/printpay', $data);
		
	}
	
	public function printinvoice(){
		
		if ($this->input->get('id')!=null){
			$col = ['a.ulunas','tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to'];
			$id = $this->input->get('id');
			$dbres = $this->Mpay->detailpay($col,$id);
			//set row title
			$row = $this->returncolomn($col);
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
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//===== parse several variables ==========
		$data['notrans'] = $dbres[0]['tnotrans'];
		$data['pic'] = $dbres[0]['rname'];
			$this->load->view('dashboard/org/pay/printinvoice', $data);
			
		} else {
			$this->session->set_flashdata('x',"No data selected, Print Invoice failed.");
			redirect(base_url("Organizer/Payment"));
		}
	}
	
	public function getdetailpay(){
		$id = $this->input->post('user');
		$res = $this->Mpay->fulldetailpay($id);
		end($res);
		$res[key($res)]['notrans'] = $this->generatenotrans($id);
		echo json_encode($res);
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fprice')){
			$price = $this->input->post('fprice');
		$dtset=array(
				'price'=>$price
				);
		$this->Msetting->savesetting($dtset);
		$this->checklunas();
		$this->session->set_flashdata('v',"Update Setting Registration Price and Full Paid Status Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Registration Price Failed.");
		}
		redirect(base_url('Organizer/Payment'));
	}
	
	public function returncolomn($header) {
	$find=['tnotrans','tdate','a.uname as mname','a.uuser','a.unim','tnomi','tpaid','tchange','transname','b.uname as rname','valid_to'];
	$replace = ['Invoice No.','Invoice Issued','Fullname','Username','NIM','Cash Given','Nominal Paid','Nominal Change','Transaction Type','PIC','Valid To'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
	public function checklunas($user=null){
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
		
	public function generatenotrans($id){
		$n = rand(000,999);
		$r = $this->Mpay->getpaycode($id);
		$no = date('ymd').mb_substr($r,3,3).$n;
		$c = $this->Mpay->checknotrans($no);
		 if($c>=1) {
		 $this->generatenotrans($id);}
		 else{ return $no;}
	}
	
	public function qr($text=null){
		$this->load->library('ciqrcode');
		$params['data'] = $text;
		$params['level'] = 'H';
		$params['size'] = 10;
		$params['savename'] = FCPATH.'/upload/qr/'.$text.'.png';
		$this->ciqrcode->generate($params);
	}
	
	public function deleteqr($id){
		$notrans = $this->Mpay->getnotrans($id);
		$path = FCPATH.'upload/qr/' . $notrans.'.png';
        unlink($path);
	}
}
