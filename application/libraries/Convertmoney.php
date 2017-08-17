<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');		

class Convertmoney {

	function convert($money)
	{
		$fmt = new NumberFormatter( 'id_ID', NumberFormatter::CURRENCY );
		$result = $fmt->formatCurrency($money,"IDR");
    
    return $result;
	}

}