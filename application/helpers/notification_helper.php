<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('getmynotif'))
{
    function getmynotif()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Mnotif');
        $CI->load->database();
        $CI->load->Model(array('Mnotif'));
        $totmynotif     = $CI->Mnotif->gettotalunread($CI->session->userdata('user'));
        return($totmynotif);
    }
}


