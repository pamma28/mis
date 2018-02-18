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

if (!function_exists('linkAllNotif'))
{
    function linkAllNotif()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        
        switch($CI->session->userdata("role")){
            case 1 : $link = "Admin/Dashboard/allnotification"; break;
            case 2 : $link = "Organizer/Dashboard/allnotification"; break;
            case 3 : $link = "Member/Dashboard/allnotification"; break;
            default: $link = "error"; break;
        }
        $nlink     = base_url($link);
        return($nlink);
    }
}

