<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('webTitle'))
{
    function webTitle()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Msetting');
        $CI->load->database();
        return($CI->Msetting->getset('webtitle'));
    }
}

if (!function_exists('webLogo'))
{
    function webLogo()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Msetting');
        $CI->load->database();
        return(base_url('upload/system/'.$CI->Msetting->getset('weblogo')));
    }
}

if (!function_exists('webDescription'))
{
    function webDescription()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Msetting');
        $CI->load->database();
        return($CI->Msetting->getset('webdescription'));
    }
}

if (!function_exists('webTag'))
{
    function webTag()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Msetting');
        $CI->load->database();
        return($CI->Msetting->getset('webtag'));
    }
}


