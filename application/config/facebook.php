<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['facebook']['api_id'] = '282691945173937';
$config['facebook']['app_secret'] = 'afad04302e987025d4e25f0a659d3235';
$config['facebook']['redirect_url'] = 'http://localhost/mis';
$config['facebook']['permissions'] = array(
  'email',
  'user_location',
  'user_birthday'
);
