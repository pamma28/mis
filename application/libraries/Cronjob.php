<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
class Cronjob {
	public function __construct() {
		
    }

    //https://api.atrigger.com/v1/tasks/create?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&timeSlice=1month&count=1&url=https%3A%2F%2Fpamma.net%2F&tag_type=schemem&tag_user=member17&first=2018-05-22T20%3A31%3A46Z

    //https://api.atrigger.com/v1/tasks/pause?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    
    //https://api.atrigger.com/v1/tasks/delete?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    
    

    public function createcron($varuser,array $vartags,$vardatetime,array $postData,$varcountexec=null,$varperiodtime=null){
        
        $tz_from = 'Asia/Jakarta';
        $tz_to = 'UTC';
        $format = 'Y-m-d\TH:i:s\Z';

        $dt = new DateTime($vardatetime, new DateTimeZone($tz_from));
        $dt->setTimeZone(new DateTimeZone($tz_to));
        $utcdate = $dt->format($format);

        $txttags = '';
        foreach ($vartags as $k => $v) {
            $txttags .= "&tag_".$k."=".$v;
        }

                /* period time parameter      
                *minute : Example: &timeSlice=180minute , &timeSlice=5minute
                *hour : Example: &timeSlice=1hour
                *day : Example: &timeSlice=6day
                *month : Example: &timeSlice=3month
                *year : Example: &timeSlice=1year
                */
        $periodtime = ($varperiodtime=="") ? "0minute" : $varperiodtime;
        $countexec = ($varcountexec=="") ? "1" : $varcountexec;
        $retries = "3";
        $url =  urlencode('https://mis.pamma.net/API/api/doing/'.$varuser.'/'.md5($varuser));

        $urlres = "https://api.atrigger.com/v1/tasks/create?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&timeSlice=".$periodtime.
                "&count=".$countexec.$txttags."&url=".$url."&retries=".$retries."&first=".$utcdate;
       
        return $this->callurl($urlres,$postData);

    }

    

    public function deletecron($varuser,$vartags){
       $txttags = '';
        foreach ($vartags as $k => $v) {
            $txttags .= "&tag_".$k."=".$v;
        }

        $urlres = "https://api.atrigger.com/v1/tasks/delete?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26".$txttags;
        return $this->callurl($urlres,array('do'=>'deletecron'));
    }

    public function pausecron($varuser,$vartags){
        $txttags = '';
        foreach ($vartags as $k => $v) {
            $txttags .= "&tag_".$k."=".$v;
        }

        $urlres = "https://api.atrigger.com/v1/tasks/pause?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26".$txttags;
        return $this->callurl($urlres,array('do'=>'pausecron'));
    }

    public function resumecron($varuser,$vartags){
        $txttags = '';
        foreach ($vartags as $k => $v) {
            $txttags .= "&tag_".$k."=".$v;
        }

        $urlres = "https://api.atrigger.com/v1/tasks/resume?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26".$txttags;
        return $this->callurl($urlres,array('do'=>'resumecron'));
    }

    private function callurl($urlres, $postData=null){
        $buildquery = (!empty($postData)) ? http_build_query($postData) : array();
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => $buildquery
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($urlres, false, $context);
        if ($result === FALSE) { 
            /* Handle error */ 
            $errorjs = array('error'=>'Cannot call url'); 
            print(json_encode($errorjs));
        } else {
            $dcdres = json_decode($result);
            if ($dcdres->type=="OK"){
                $ret = array("status"=>"1");
            } else {
                $ret = array("status"=>$dcdres->message);
            }
            print(json_encode($ret));
        }

        return json_encode($ret);
    }

}

?>