<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/cronjob/vendor/autoload.php";

use GO\Scheduler;
$scheduler = new Scheduler();

class Cronjob extends Scheduler{
	public function __construct() {
        parent::__construct();
		
    }
    

    public function startcron(){

   		
    }

}

//$scheduler->php(FCPATH.'try.php')->everyMinute();
$scheduler->call(function () {
    echo "Hello";

    return " world!";
})->everyMinute()->output('my_file.log');

$scheduler->run();	

?>