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

if (!function_exists('labeltottransfer'))
{
    function labeltottransfer(){
        $CI =& get_instance();
        $CI->load->model('Mtransfer');
        $tot = $CI->Mtransfer->gettotaltransfernotconfirm();
        if ($tot>0){
            echo '<span class="label label-warning pull-right" title="Total Validation Payment (Not Processed)">'.$tot.'</span>';
        }
    }
}

if (!function_exists('labeltotalactivetest'))
{
    function labeltotalactivetest(){
        $CI =& get_instance();
        $CI->load->model('Mtest');
        $tot = $CI->Mtest->gettotalactivetest();
        if ($tot>0){
            echo '<span class="label label-success pull-right" title="Total Active Test">'.$tot.'</span>';
        }
    }
}

if (!function_exists('labeltotresulttest'))
{
    function labeltotresulttest(){
        $CI =& get_instance();
        $CI->load->model('Mtest');
        $tot = $CI->Mtest->gettotresulttest();
        if ($tot>0){
            echo '<span class="label label-info pull-right" title="Total Result Test (Not Assessed)">'.$tot.'</span>';
        }
    }
}