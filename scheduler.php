<?php 

require_once __DIR__."/application/third_party/cronjob/vendor/autoload.php";

use GO\Scheduler;
$scheduler = new Scheduler();
date_default_timezone_set('Asia/Jakarta');

class Cronjob extends Scheduler{
	public function __construct() {
        parent::__construct();
		
    }
    

    public function startcron(){

   		
    }

}

//$scheduler->php(FCPATH.'try.php')->everyMinute();
$scheduler->php('try.php')->everyMinute();

$scheduler->run();	

?>