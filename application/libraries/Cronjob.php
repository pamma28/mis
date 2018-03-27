<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/atrigger/ATrigger.php";

//ATrigger::init("YOUR_APIKey","YOUR_APISecret");
ATrigger::init("5048410639605220993","WUz5Hc7fboRe36XlE57nCs2aBdTJ26");
class Cronjob {
	public function __construct() {
        parent::__construct();
		
    }

    //https://api.atrigger.com/v1/tasks/create?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&timeSlice=1month&count=1&url=https%3A%2F%2Fpamma.net%2F&tag_type=schemem&tag_user=member17&first=2018-05-22T20%3A31%3A46Z

    //https://api.atrigger.com/v1/tasks/pause?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    
    //https://api.atrigger.com/v1/tasks/delete?key=5048410639605220993&secret=WUz5Hc7fboRe36XlE57nCs2aBdTJ26&tag_type=schemem&tag_user=member17
    

    public function createcron(){
        $tags = array();
        $tags['type']='test';
         
        $postData = array();
        $postData['type']='test';
         
        $firstDate = date_create_from_format('d/M/Y:H:i:s', '01/Jan/2015:00:00:01');
         
        ATrigger::doCreate("0minute", "http://www.example.com/myTask?something", $tags, $firstDate, 1, 5, $postData);
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