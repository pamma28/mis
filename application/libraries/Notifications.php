<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Notifications {
	protected $CI;

	public function __construct() {
        $this->CI =& get_instance();
		$this->CI->load->database();
        $this->CI->load->library(array('session','converttime'));
        $this->CI->load->model(array('Mnotif','Mlogin'));

    }
    
    public function getmynotif(){
        $user = $this->CI->session->userdata('user');
        $arrread = array('nread.idnotif','nread.uuser','nread.use_uuser','nicon','ncontent','datesend','nread','nlink');
        $countunread = $this->CI->Mnotif->countnread(array('use_uuser'=>$user,'nread'=>'0'));
        if ($countunread>5) {
            $arrnotif = $this->CI->Mnotif->datanread(
                $arrread,
                $countunread,
                1,
                array('use_uuser'=>$user,'nread'=>'0')
            );
        } else {
            $arrnotif = $this->CI->Mnotif->datanread(
                $arrread,
                5,
                1,
                array('use_uuser'=>$user)
            );
            
        }
            $list='';
            foreach ($arrnotif as $k => $v) {
            if (!$v['nread']){ 
                $nread='notread';
                $this->CI->Mnotif->updatenread(array('nread'=>'1','dateread'=>date("Y-m-d H:i:s")),$v['idnotif'],$v['uuser'],$v['use_uuser']);
            } else {
                $nread = '';
            } 
            $sendtime = $this->CI->converttime->time_elapsed_string($v['datesend']); 
            ($v['nlink']=='') ? $link = "#" : $link = $v['nlink'];
            $list .= '<li>
                        <a href="'.$link.'" class="'.$nread.'">
                          <p class="text-wrap: break-word;"><span class="fa '.$v['nicon'].'"></span> '.$v['ncontent'].'</p>
                          <div class="text-right"><sup><i><b>'.$sendtime.'</b></i></sup></div>
                          
                        </a>
                      </li>';
            }
        
        return $list;
    }

    public function getallmynotif(){
        $user = $this->CI->session->userdata('user');
        $arrall= array('nread.idnotif','nread.uuser','nread.use_uuser','nicon','ncontent','datesend','nread','nlink');
        $countall = $this->CI->Mnotif->countnread(array('use_uuser'=>$user));
        
            $arrnotif = $this->CI->Mnotif->datanread(
                $arrall,
                $countall,
                1,
                array('use_uuser'=>$user)
            );
        
            $list='';
            foreach ($arrnotif as $k => $v) {
            if (!$v['nread']){ 
                $nread='notread';
                $this->CI->Mnotif->updatenread(array('nread'=>'1','dateread'=>date("Y-m-d H:i:s")),$v['idnotif'],$v['uuser'],$v['use_uuser']);
            } else {
                $nread = '';
            } 
            $sendtime = $this->CI->converttime->time_elapsed_string($v['datesend']); 
            ($v['nlink']=='') ? $link = "#" : $link = $v['nlink'];
            $list .= '<tr><td>
                        <a href="'.$link.'" class="'.$nread.'">
                          <p class="text-wrap: break-word;"><span class="fa '.$v['nicon'].'"></span> '.$v['ncontent'].'</p>
                          <div class="text-right"><sup><i><b>'.$sendtime.'</b></i></sup></div>
                          
                        </a>
                      </td></tr>';
            }
        
        return $list;
    }

    public function pushmynotif($dt){
        if(is_array($dt)){
            $this->CI->Mnotif->insertnread(
                        array(
                            'idnotif'=>$dt['idnotif'],
                            'uuser'=>$dt['uuser'],
                            'use_uuser'=>$dt['uuser'],
                            'nread'=>'0',
                            'datesend'=>date('Y-m-d H:i:s'),
                            )
                        );

        }
        else {
            echo 'error';
        }
    }

    public function pushNotifToOrg($dt){
        if(is_array($dt)){
            $arrorg = $this->CI->Mlogin->getallorg();
            foreach ($arrorg as $k => $v) {
                $this->CI->Mnotif->insertnread(
                        array(
                            'idnotif'=>$dt['idnotif'],
                            'uuser'=>$dt['uuser'],
                            'use_uuser'=>$v['uuser'],
                            'nread'=>'0',
                            'datesend'=>date('Y-m-d H:i:s'),
                            )
                        );
            }

        }
        else {
            echo 'error';
        }
    }
}

?>