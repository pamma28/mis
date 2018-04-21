<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');		

require_once APPPATH."/third_party/mpdf/autoload.php";
class Mpdf{

	

	function generatepdf(){
	$mpdf = new \Mpdf\Mpdf();
	// Write some HTML code:
	$mpdf->WriteHTML('Hello World');
	// Output a PDF file directly to the browser
	$mpdf->Output('file.pdf',\Mpdf\Output\Destination::INLINE);
		
	}
}