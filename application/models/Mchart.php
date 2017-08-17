<?php if(!defined('BASEPATH')) exit('No direct script access allowed ');

class Mchart extends CI_Model{
    public function __construct(){
        parent::__construct();
		$this->load->model('Msetting');
    }
	
	public function getregfac(){
		$this->db->select("fname as 'faculty',count('user.idfac') as 'total'");
        $this->db->from('user');
        $this->db->where('idrole','3');
        $this->db->where('user.idfac<>','');
        $this->db->join('fac', 'user.idfac=fac.idfac','left');
        $this->db->group_by('user.idfac');
		$q= $this->db->get()->result_array();
        return $q;
	}
	
	public function getregfac_yearly(){
		$thisperiod = $this->Msetting->getset('period');
		$this->db->select("fname as 'faculty',(select count('user.idfac') from user inner join fac on user.idfac=fac.idfac and user.idrole='3' where DATE_FORMAT(user.ucreated,'%Y')='".$thisperiod."' and fname=faculty) as 'total', (select count('user.idfac') from user inner join fac on user.idfac=fac.idfac and user.idrole='3' where DATE_FORMAT(user.ucreated,'%Y')='".($thisperiod-1)."' and fname=faculty) as 'lasttotal',(select count('user.idfac') from user inner join fac on user.idfac=fac.idfac and user.idrole='3' where DATE_FORMAT(user.ucreated,'%Y')='".($thisperiod-2)."' and fname=faculty) as 'lasttotall'");
        $this->db->from('user');
        $this->db->where('idrole','3');
        $this->db->join('fac', 'user.idfac=fac.idfac','left');
        $this->db->group_by('user.idfac');
		$q= $this->db->get()->result_array();
        return $q;
	}
	public function getuseronline(){
		$this->db->select("CONCAT(Curdate(),' ',DATE_FORMAT(logdate,'%H')) as 'hourly',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='1' where HOUR(logdate) = HOUR(hourly))as 'Admin',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='2' where HOUR(logdate) = HOUR(hourly))as 'Organizer',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='3' where HOUR(logdate) = HOUR(hourly))as 'Member'");
		//$this->db->select("DATE_FORMAT(logdate,'%Y-%m-%d %H:00:00') as 'date',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='3' where DATE_FORMAT(logstatus.logdate,'%Y-%m-%d %H:00:00')=date)as 'Member',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='2' where DATE_FORMAT(logstatus.logdate,'%Y-%m-%d %H:00:00')=date)as 'Organizer',(select count('logstatus.uuser') from logstatus inner join user on user.uuser=logstatus.uuser and user.idrole='1' where DATE_FORMAT(logstatus.logdate,'%Y-%m-%d %H:00:00')=date)as 'Admin'");
        $this->db->from('logstatus');
        $this->db->group_by('hourly');
        $this->db->order_by('hourly','asc');
		$q= $this->db->get()->result_array();
        return $q;
	}
	
	public function getregyear(){
		$this->db->select("DATE_FORMAT(ucreated,'%Y') as 'pyear',(select count('idjk') from user where DATE_FORMAT(ucreated,'%Y')=pyear and idrole='3' and idjk='1' ) as 'totmale',(select count('idjk') from user where DATE_FORMAT(ucreated,'%Y')=pyear and idrole='3' and idjk='2' ) as 'totfemale'");
		$this->db->from('user');
        $this->db->where('idrole','3');
        $this->db->group_by('pyear');
        $this->db->order_by('pyear','desc');
		$q= $this->db->get()->result_array();
        return $q;
	}
}