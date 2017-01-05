<?php

# Application name
define('APP', 'CompSuite');

# Base URL
define('BASE_URL', 'http://redangadmin.secretgroup.my/');

# Public URL for css, js
define('PUBLIC_URL', BASE_URL . 'public/');

# Application directory
define('APP_DIR', dirname( dirname( __FILE__ )) . '/');

# either development or production mode, for dev we do not use https
define('APP_MODE', 'development');

require 'autoload.php';
require 'lang_english.php';
require 'config_table.php';
require 'config_var.php';

# add the external libarry used
require_once 'applications/libs/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php';
require_once 'applications/libs/PHPExcel1.8.0/Classes/PHPExcel.php';
require_once 'applications/libs/PHPExcel1.8.0/Classes/PHPExcel/IOFactory.php';

# DB related vars
$slm_db = array(
    'host' => 'localhost',
    'name' => 'secretgr_redangapp',
    'login' => 'secretgr_hotel',
    'password' => '67925395',
    'error_mode' => 'warning', # only silent/warning/exception is allowed
    'error_log' => '/home/secretgroup/public_html/redangapp/redangapp.log'
);


?>