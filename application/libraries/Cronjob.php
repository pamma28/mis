<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
class Cronjob {
	public function __construct() {
		
    }

    //https://api.atrigger.com/v1/tasks/create?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&timeSlice=1month&count=1&url=https%3A%2F%2Fpamma.net%2F&tag_type=schemem&tag_user=member17&first=2018-05-22T20%3A31%3A46Z

    //https://api.atrigger.com/v1/tasks/pause?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    
    //https://api.atrigger.com/v1/tasks/delete?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    

    public function createcron($varuser,array $vartags,$vardatetime,array $postData){
        
        $tz_from = 'Asia/Jakarta';
        $tz_to = 'UTC';
        $format = 'Y-m-d\TH:i:s\Z';

        $dt = new DateTime($vardatetime, new DateTimeZone($tz_from));
        $dt->setTimeZone(new DateTimeZone($tz_to));
        $utcdate = $dt->format($format);

        /*
        $firstDate = date_create_from_format('d/M/Y:H:i:s', $utcdate);
        $ret = ATrigger::doCreate("0minute", "https://mis.pamma.net/API/Api/".$varuser."/".md5($varuser), $vartags, $firstDate, 3, 3, $postData);
        print_r($ret);
        date_default_timezone_set('Asia/Jakarta');
        $tags = array();
        $tags['type']='test';
         
        $postData = array();
        $postData['type']='test';
         
        $firstDate = date_create_from_format('d/M/Y:H:i:s', '30/Mar/2018:08:30:01');
        ATrigger::doCreate("0minute", "https://pamma.net/myTask?something", $tags, $firstDate, 1, 5, $postData);
        */
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
        $periodtime = "5minute";
        $countexec = "5";
        $retries = "5";
        $url =  urlencode('https://mis.pamma.net/API/api/sendsms/'.$varuser.'/'.md5($varuser));

        $urlres = "https://api.atrigger.com/v1/tasks/create?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&timeSlice=".$periodtime.
                "&count=".$countexec.$txttags."&url=".$url."&retries=".$retries."&first=".$utcdate;
       
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($postData)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($urlres, false, $context);
        if ($result === FALSE) { /* Handle error */ 
            print('error');
        }

        print($result);

        /*
        */
    }

    public function deletecron(){
        //Tags:
        $tags = array();
        $tags['type']='test';
         
        //Delete
        ATrigger::doDelete($tags);
    }

    public function pausecron(){
        //Tags:
        $tags = array();
        $tags['type']='test';
         
        //Pause
        ATrigger::doPause($tags);
    }

    public function resumecron(){
        //Tags:
        $tags = array();
        $tags['type']='test';
         
        //Resume
        ATrigger::doResume($tags);
    }

}

?>