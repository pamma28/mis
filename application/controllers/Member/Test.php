<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends Mem_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mtest','Msetting'));
    }

	public function index(){
		//===================== check phase date =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$schephase = explode(" - ",$this->Msetting->getset('schedulephase'));
		$startsche = strtotime(str_replace('/', '-', $schephase[0]));
		$endsche =  strtotime(str_replace('/', '-', $schephase[1]));
		$today = strtotime(date("d-m-Y"));
		$data['date'] = (($today >= $startsche) and ($today <= $endsche)) ? true : false;
		$data['startdate'] = date('d-M-Y',$startsche);
		$data['enddate'] = date('d-M-Y', $endsche);
		//===================== table handler =============
		$column=['jdwl_tes.idjdwl','jdate','jsesi','tname','tduration','jroom','jactive'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover" id="mylist">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$perpage = $this->Mtest->countmytest(null,true);
		
		//============ data test ==============
		$temp = $this->Mtest->datamytest($column,$perpage,1,null,true);	
				foreach($temp as $key=>$value){
				//manipulation date data
				$temp[$key]['jdate']=date("D, d-M-Y",strtotime($temp[$key]['jdate']));
				
				$jdate = date_create($value['jdate']);
				$nowdate = new DateTime(date('d-m-Y'));
				$rescompare = ($jdate >= $nowdate) ? true: false;
				$temp[$key]['tduration']=$temp[$key]['tduration'].' minute(s)';
				$temp[$key]['jactive']= ($rescompare) ? ($value['jactive']) ? '<label class="label label-primary">Ready</label>' : '<label class="label label-warning">Waiting</label>':'<label class="label label-danger">Expired</label>';
				//manipulation menu
				$enc = $value['idjdwl'];
					if ($rescompare){
						$menu = '<div class="btn-group btn-group-vertical">
							<a href="'.base_url('Member/Test/dotest?id=').$enc.'" alt="Do Test" class="btn btn-primary btn-sm"  title="Do Test"><i class="fa fa-pencil"></i> Start Test</a>
							<a href="'.base_url('Member/Test/detailtest?id=').$enc.'" alt="Detail Data" class="btn btn-info btn-sm" data-toggle="modal" data-target="#DetailModal" title="Detail Test"><i class="fa fa-list-alt"></i> Detail Test</a>
								</div>';
					} else {
						$menu = '<div class="btn-group btn-group-vertical"><a href="'.base_url('Member/Test/resulttest?id=').$enc.'" alt="Result Test" class="btn btn-info btn-sm" data-toggle="modal" data-target="#DetailModal" title="Check Result"><i class="fa fa-info-circle"></i> Check Result</a></div>';
					}
				$temp[$key]['menu'] = $menu;
				unset($temp[$key]['idjdwl']);
				}
		$data['mytest'] = $this->table->generate($temp);
		
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'loading/loadingcircle');  
		// =============== view handler ============
		$data['title']='My Test';
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/test/testlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function testresult(){
		//===================== Period =============
		$data['thisperiod']=$this->Msetting->getset('period');
		
		//===================== table handler =============
		$column=['resulttest.idresult','tname','jstart','jroom','q_score'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover" id="mylist">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$perpage = $this->Mtest->countmyresulttest(null);
		
		//============ data test ==============
		$temp = $this->Mtest->datamyresulttest($column,$perpage,1,null);	
				foreach($temp as $key=>$value){
				//manipulation date data
				$temp[$key]['jstart']=date("D, d-M-Y H:i",strtotime($temp[$key]['jstart']));
				;
				$temp[$key]['q_score']= ($value['q_score']<>'') ? '<label class="label label-info">'.$value['q_score'].'</label>' : '<label class="label label-warning">Being Assessed</label>';
				//manipulation menu
				$enc = $value['idresult'];
				$menu = '';	
				if (strtotime($temp[$key]['jstart']) <= strtotime(date('now')) ) {
					$menu .= '<div class="btn-group btn-group-vertical">
							<a href="'.base_url('Member/Test/dotest?id=').$enc.'" alt="Do Test" class="btn btn-primary btn-sm"  title="Do Test"><i class="fa fa-pencil"></i> Start Test</a>';
					}
					$menu .='<a href="'.base_url('Member/Test/detailtestresult?id=').$enc.'" data-href="'.base_url('Member/Test/detailtest?id=').$enc.'" alt="Detail Data" class="btn btn-info btn-sm" data-toggle="modal" data-target="#DetailModal" title="Detail Result"><i class="fa fa-list-alt"></i> Detail Test Result</a>
								</div>';
				$temp[$key]['menu'] = $menu;
				unset($temp[$key]['idresult']);
				}
		$data['myresult'] = $this->table->generate($temp);
		
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'loading/loadingcircle');  
		// =============== view handler ============
		$data['title']='Test Result';
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/test/testres', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function dotest(){
		$idjdwl = $this->input->get('id');
		if ($idjdwl != ''){
			$col =['jdate','jstart','jdwl_tes.idtest','jsesi','tname','tduration','jroom','uname','jactive'];
			$this->load->model('Msche');
			$checkme = $this->Msche->checkmeinsche($idjdwl,$this->session->userdata('user'));
			$ressche = $this->Msche->detailsche($col,$idjdwl);
			//filter me in test
			if (($checkme>0) and ((array_key_exists(0, $ressche))))
			{
				
				$arrsche = $ressche[0];
				$data['t'] = $arrsche;
				$data['me'] = $this->session->userdata('name');

				if ($arrsche['jactive']){ 
					
						//save db resulttest
						$countmethistest = $this->Mtest->countmethistest($arrsche['idtest']);
						if ($countmethistest==0){
							//generate question
							$res = $this->generatequestion($arrsche['idtest'],$idjdwl);
							$arrgeneratequest = $res[0];
							$myid = $res[1];
							
							
						} else {
							$myid = $this->Mtest->getmeidresult($arrsche['idtest']);
							$arrgeneratequest = $this->Mtest->getdetailmyresult(array('idtest','q_randcode','q_randquest'),$myid)[0];
							$arrgeneratequest = $this->getgeneratedquestion($arrgeneratequest['q_randquest'],$arrgeneratequest['q_randcode'],$myid);

						}

					
					$data['mytest'] = $arrgeneratequest;
					$data['idresult'] = form_hidden('resultid',$myid);
					$data['active'] = '';
					$data['remain'] = (strtotime($arrsche['jstart']."+".$arrsche['tduration']." minutes"));
					$data['runout'] = ($data['remain'] > strtotime(date('Y-m-d H:i:s'))) ? '1' : '0';
											
				} else {
					$data['active'] = '1';
				}

				//=============== Template ============
				$data['jsFiles'] = array(
									'icheck.min','countdown/jquery.countdown.min');
				$data['cssFiles'] = array(
									'tabpane/tabpane','icheck/square/blue');  
				// =============== view handler ============
				$data['title']=$arrsche['tname'];
				$data['content'] = $this->load->view('dashboard/mem/test/dotest', $data, TRUE);
				
			} else {
			// =============== view handler ============
			$data['title']="No Test Found";
			$data['content'] = $this->load->view('dashboard/mem/test/notest', $data, TRUE);
			}

		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$this->load->view ('template/main', $data);

		} else {
			$this->session->set_flashdata('x','Cannot Directly Access it. You need to choose one of Your Test.');
			redirect(base_url('Member/Test'));
		}
	}	
	
	public function detailtestresult(){
		$this->load->model('Mresult');
		$idresult = $this->input->get('id');
		$checkmyresult = $this->Mresult->countresult(array('idresult'=>$idresult,'a.uuser'=>$this->session->userdata('user')));
		if ($checkmyresult=='1'){
				$col =['jdate','jstart','jdwl_tes.idtest','jsesi','tname','tduration','jroom','a.uname','jactive','lvlname','q_score'];
				$data['t'] = $this->Mresult->detailresult($col,$idresult)[0];
				$data['finalscore'] = ($data['t']['q_score']=='') ? 'N/A' : $data['t']['q_score'];

				$varresult = $this->Mtest->getdetailmyresult(array('idtest','q_randcode','q_randquest'),$idresult)[0];
				
				$arrgeneratequest = $this->getgeneratedquestion($varresult['q_randquest'],$varresult['q_randcode'],$idresult);
				
				
				$data['mytest'] = $arrgeneratequest;
			// =============== view handler ============
			$this->load->view('dashboard/mem/test/detailresult', $data);
				
		} else {
			// =============== view handler ============
			$this->load->view('dashboard/mem/test/noresult', $data);
		}
		
	}

	
	private function getgeneratedquestion($arrgeneratequest,$code,$myresultid){
		$this->load->model('Mq');
		$arrquest = explode(',',$arrgeneratequest);
		$col = ['question','subject','question.idsubject','q_bundle','qmanual'];
		$prevattach = array(); $prevsub = ''; $prevbundle ='';
		
		foreach ($arrquest as $k => $v){
			$arrdetailquest = $this->Mq->getmyquestdetail($col,$v)[0];
			$finalquestion[$k]['code'] = $code;
			$finalquestion[$k]['idq'] = $v;
			$finalquestion[$k]['question'] = $arrdetailquest->question;
			$finalquestion[$k]['subject'] = $arrdetailquest->subject;
			$finalquestion[$k]['qmanual'] = $arrdetailquest->qmanual;
			
			//================ attachment ==============
			$tmparrattch = $this->Mq->getattachbyq(array('q_paragraph','q_file','q_filetype'),$v);
			if (array_key_exists(0,$tmparrattch)){
				$arrattch = $tmparrattch[0];
				$prevsub = $arrdetailquest->idsubject;
				$prevbundle = $arrdetailquest->q_bundle;
				$arrattch['indexatt'] = $arrdetailquest->subject.$arrdetailquest->q_bundle;
			} else if (($prevsub == $arrdetailquest->idsubject) and ($prevbundle == $arrdetailquest->q_bundle)){
				$arrattch = $prevattach;
				$arrattch['indexatt'] = $arrdetailquest->subject.$arrdetailquest->q_bundle;
			} else {
				$arrattch = array();
			}
			$finalquestion[$k]['attach'] = $arrattch;

			//================ answers ================
			$checkgeneratedanswer = $this->Mq->checkgeneratedanswer($v,$myresultid);
			$arridanswer = array();
			if ($checkgeneratedanswer==0){
				$arridanswer = array_column($this->Mq->populateanswer('idans',$v), 'idans');
				shuffle($arridanswer);
				//save random answer
					$this->Mq->insertqa(array(
						'idresult'=>$myresultid,
						'idq'=>$v,
						'rand_answer'=>implode(',',$arridanswer)
						));
			} else {
				$tmparridanswer = $this->Mq->getrandanswer($v,$myresultid);
				$arridanswer = ($tmparridanswer<>'') ? explode(',',$tmparridanswer) : array();
			}

			//collect all answer with order specified
				$answer = array();
				foreach ($arridanswer as $a => $b) {
					$currentanswer = $this->Mq->getanswer(array('idans','answer'),$b)[0];
					$answer[$a]['idans'] = $currentanswer['idans'];
					$answer[$a]['answer'] = $currentanswer['answer'];
					}
				//picked answer if any
				$pickedanswer = $this->Mq->getpickedanswer($v,$myresultid);
				$mark = $this->Mq->getmarkanswer($v,$myresultid);
			
			$finalquestion[$k]['pickedanswer'] = $pickedanswer;	
			$finalquestion[$k]['allanswer'] = $answer;
			$finalquestion[$k]['mark'] = ($mark=='1') ? '<span class="fa fa-check text-success"></span>' : (($mark=='0') ? '<span class="fa fa-times text-danger"></span>' : '<span class="text-success">'.($mark*10).'</span>'  );
			$prevattach = $arrattch;
		}

		return ($finalquestion);
	}

	private function generatequestion($idtest,$idjdwl){
		$this->load->model(array('Msubject','Mq','Mtest'));
		//==== catch all subject ====
		$col = ['quo_sbjct.idsubject','qtot','qsort'];
		$arrsubquo =  $this->Msubject->datasbjctbyid($col,$idtest);
		$finalquestion = array(); $finalcode=''; $enoughquest = false;
		//====== check available subject =====
		if (is_array($arrsubquo)){
				//get all unique code then pick best code (optimum diversity)
				$arrcode = array_column($this->Mtest->getalluniquecodebyidtest($idtest),'q_group');
				$totuniquecode = count($arrcode);
				$finalcode = $this->Mtest->bestquestioncode($arrcode,$idtest);
			}

		//==== generate question each subject
		$i = 0;
		foreach ($arrsubquo as $k => $v) {
			//get all unique question bundle
			$arrbundle = array_column($this->Mq->getalluniqiebundlebyidsub($v['idsubject'],$finalcode),'q_bundle');
			$totbundle = count($arrbundle);
			$sortbundle = $this->generaterandomorder(0,($totbundle-1),$totbundle);
			$indexbundle = 0;
			do
			{	
				$finalbundle = $arrbundle[$sortbundle[$indexbundle]];
				$arrqbybundle = $this->Mq->populatequestbysubbundle(array('idq','question','subject','qtype.qmanual'),$v['idsubject'],$finalbundle,$finalcode);
				$totqbundle = count($arrqbybundle);
				$sortq = $this->generaterandomorder(0,($totqbundle-1),$totqbundle);

				if ($totqbundle < $v['qtot']){
					$loop = $totqbundle;
				} else {
					$loop = $v['qtot'];
				}
					
				$prevattach = array();

					for($x=0;$x<$loop;$x++){
						//fetch all question
						$tmparrattch = $this->Mq->getattachbyq(array('q_paragraph','q_file','q_filetype'),$arrqbybundle[$sortq[$x]]['idq']);
						if (array_key_exists(0,$tmparrattch)){
							$arrattch = $tmparrattch[0];
						} else {
							$arrattch = $prevattach;
						}

						$finalquestion[$i]['code'] = $finalcode;
						$finalquestion[$i]['idq'] = $arrqbybundle[$sortq[$x]]['idq'];
						$finalquestion[$i]['question'] = $arrqbybundle[$sortq[$x]]['question'];
						$finalquestion[$i]['subject'] = $arrqbybundle[$x]['subject'];
						$finalquestion[$i]['qmanual'] = $arrqbybundle[$x]['qmanual'];
						$finalquestion[$i]['attach'] = $arrattch;
						
						$prevattach = $arrattch;
						
					$i++;
					}
				$i++;
				($totbundle>1) ? $indexbundle ++ : null;
				
			}
			while ( $i < $v['qtot']);	
		}

		//save generated question into result test
		$arrallquest = implode(array_column($finalquestion, 'idq'),',');
						$fdt = array(
							'q_submitted'=>date("Y-m-d H:i:s"),
							'idtest'=>$idtest,
							'idjdwl'=>$idjdwl,
							'q_randcode'=>$finalcode,
							'q_randquest'=>$arrallquest,
							'uuser' =>$this->session->userdata('user')
							);
						$this->Mtest->savemyresult($fdt);
		$myresultid = $this->db->insert_id();

		//=============== generate answer ======
		foreach ($finalquestion as $k => $v) {
			$arranswer = $this->Mq->populateanswer(array('idans'),$v['idq']);
			$arridanswer = array_column($arranswer, 'idans');
			shuffle($arridanswer);
			//save random answer
			$this->Mq->insertqa(array(
						'idresult'=>$myresultid,
						'idq'=>$v['idq'],
						'rand_answer'=>implode(',',$arridanswer)
						));
			$answer = array();
			foreach ($arranswer as $keyans => $valueans) {
				$currentanswer = $this->Mq->getanswer(array('idans','answer'),$valueans['idans'])[0];
				$answer[$keyans]['idans'] = $currentanswer['idans'];
				$answer[$keyans]['answer'] = $currentanswer['answer'];
				//$answer['key_ans'] = $arranswer[$sortanswer[$indexans]]['key_ans'];
				}
				//picked answer if any
				$pickedanswer = $this->Mq->getpickedanswer($v['idq'],$myresultid);
			
			$finalquestion[$k]['pickedanswer'] = $pickedanswer;	
			$finalquestion[$k]['allanswer'] = $answer;
		}

		return (array($finalquestion,$myresultid));

	}
	
	private function generaterandomorder($min, $max, $quantity) {
	    $numbers = range($min, $max);
	    shuffle($numbers);
	    return array_slice($numbers, 0, $quantity);
	}

	public function savemyanswer(){
		$this->load->model('Mq');
		if ($this->input->post('allqans')!=''){
			parse_str($this->input->post('allqans'), $arrans);
			$allq = $arrans['quest'];
			$idres = $arrans['resultid'];
			$allmanual = $arrans['qmanual'];
			foreach ($allq as $k => $v) {
				if (array_key_exists('ans'.($k+1),$arrans))
				{
				$res = false;
					
						$res = ($allmanual[$k]) ? false : $this->Mq->checkcorrectanswer($arrans['ans'.($k+1)]);
					

					$fdata = array(
						'rpickanswer'=>$arrans['ans'.($k+1)],
						'rtrue'=>$res
						);
					$ret = $this->Mq->updateqa($fdata,$idres,$v);
				}

			}
			
			print($ret);
		} else {
			print('false no format');
		}
	}
	
	public function detailtest(){
		$idjdwl = $this->input->get('id');
		if ($idjdwl != ''){
			$col =['jdate','jstart','jdwl_tes.idtest','jsesi','tname','tduration','jroom','uname','jactive'];
			$this->load->model('Msche');
			$checkme = $this->Msche->checkmeinsche($idjdwl,$this->session->userdata('user'));
			$ressche = $this->Msche->detailsche($col,$idjdwl);
			//filter me in test
			if (($checkme>0) and ((array_key_exists(0, $ressche))))
			{
				
				$arrsche = $ressche[0];
				$data['t'] = $arrsche;
				$data['me'] = $this->session->userdata('name');

				if ($arrsche['jactive']){ 
					
						//save db resulttest
						$countmethistest = $this->Mtest->countmethistest($arrsche['idtest']);
						if ($countmethistest==0){
							//generate question
							$res = $this->generatequestion($arrsche['idtest'],$idjdwl);
							$arrgeneratequest = $res[0];
							$myid = $res[1];
							
							
						} else {
							$myid = $this->Mtest->getmeidresult($arrsche['idtest']);
							$arrgeneratequest = $this->Mtest->getdetailmyresult(array('idtest','q_randcode','q_randquest'),$myid)[0];
							$arrgeneratequest = $this->getgeneratedquestion($arrgeneratequest['q_randquest'],$arrgeneratequest['q_randcode'],$myid);

						}

					
					$data['mytest'] = $arrgeneratequest;
					$data['idresult'] = form_hidden('resultid',$myid);
					$data['active'] = '';
					$data['remain'] = (strtotime($arrsche['jstart']."+".$arrsche['tduration']." minutes"));
					$data['runout'] = ($data['remain'] > strtotime(date('Y-m-d H:i:s'))) ? '1' : '0';
											
				} else {
					$data['active'] = '1';
				}

				// =============== view handler ============
				$viewresult = $this->load->view('dashboard/mem/test/dotest', $data, TRUE);
				
			} else {
			// =============== view handler ============
			$viewresult = $this->load->view('dashboard/mem/test/notest', $data, TRUE);
			}

		// =============== final view handler ============
		$this->load->view('dashboard/mem/test/detailtest', $data);

		} else {
			$this->session->set_flashdata('x','Cannot Directly Access it. You need to choose one of Your Test.');
			redirect(base_url('Member/Test'));
		}
		
	}

	
	public function returncolomn($header) {
	$find=['idjdwl','jmdate','jdate','jstart','tname','jdwl_tes.idtest','jsesi','jroom','tduration','jactive','uname','tktrgn','q_score','q_notes'];
	$replace = ['Schedule ID','Date Choosen','Schedule Date','Test Started','Test Name','Test Name','Session','Room','Test Duration','Status','Assessed by','Additional Info','Final Score','Result Notes'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
