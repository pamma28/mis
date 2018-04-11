<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mdb extends CI_Model{
    public function __construct(){
        parent::__construct();
    }
	
	
	public function datadb($column = null, $per_page = null, $page = null,$col = null, $filter = null){
		$this->db->select($column);
		$this->db->limit($per_page,(($page-1)*($per_page)));
			if (($col != null) AND ($filter != null)){$this->db->like($col,$filter);}
		$this->db->order_by('table_name','asc');
		$this->db->where('TABLE_SCHEMA',$this->db->database);
		$q = $this->db->get('INFORMATION_SCHEMA.TABLES');
		$qr = $q->result_array();	
		return $qr;
	}
	
	public function countdb($col = null, $filter = null){
	
		$this->db->where('TABLE_SCHEMA',$this->db->database);
		if (($col != null) AND ($filter != null)){
		$this->db->like($col,$filter);}
		return $this->db->count_all_results("INFORMATION_SCHEMA.TABLES");

	}
	
	public function restoredb($dtdb){
		//var store result
		$tot = 0;
		$fail = 0;
		$this->db->query('SET foreign_key_checks = 0');
		foreach ($dtdb as $k=>$query) {
           $res = $this->db->query($query);
		   if ($res){
		   $tot++;} else {
		   $fail++;}
		}
		//restore trigger
		if ($tot>1){
			$arrtrigger = ["CREATE TRIGGER `Update_tmp_Score` AFTER UPDATE ON `resultqa` FOR EACH ROW BEGIN
							UPDATE `resulttest`
							SET q_tmpscore = (
							   SELECT SUM(tot) as tmpscore from (
							    SELECT ((SUM(rtrue)/count(*))*(select qpercent from quo_sbjct WHERE idsubject=subject.idsubject and idtest=resulttest.idtest)) as tot
							    FROM resultqa
							    LEFT JOIN `question` ON question.idq = resultqa.idq
							    LEFT JOIN `qtype` ON qtype.idqtype = question.idqtype
							    LEFT JOIN `subject` ON subject.idsubject = question.idsubject
							    LEFT JOIN `resulttest` ON resultqa.idresult = resulttest.idresult
							    WHERE resultqa.idresult = new.idresult AND qmanual=0
							    GROUP BY question.idsubject) as b
							    )   
							    WHERE resulttest.idresult = new.idresult;
								END",
							"CREATE TRIGGER `minquotajdwl` BEFORE INSERT ON `jdwl_mem` FOR EACH ROW BEGIN
							UPDATE
							  jdwl_tes
							SET
							  jdwl_tes.jquota = (jdwl_tes.jquota - 1)
							WHERE
							  jdwl_tes.idjdwl = NEW.idjdwl;
							END",
							"CREATE TRIGGER `restorequotajdwl` AFTER DELETE ON `jdwl_mem` FOR EACH ROW BEGIN
							UPDATE
							  jdwl_tes
							SET
							  jquota = (jquota + 1)
							WHERE
							  idjdwl = OLD.idjdwl;
							END"];
			foreach ($arrtrigger as $v) {
				$this->db->query(rtrim(trim($v), "\n;"));
			}
		}
		$this->db->query('SET foreign_key_checks = 1');
		return ($tot.' Query(s) successfully excuted with '.$fail.' Query(s) failed.');
	}
	
	
	public function deletedb($id){
	$this->db->query('SET FOREIGN_KEY_CHECKS = 0');
	$this->db->query('TRUNCATE '.$id);
	return $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
	}
	
	public function detaildb($col,$id){
	$this->db->select($col);
	return $this->db->get($id)->result_array();
	}
	
}