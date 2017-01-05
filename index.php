<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

# Include config file
require 'applications/config/config.php';

# Initialize app
$app = new app();

# Add routings (route, controller, method = 'index', is_api = FALSE)

# handle login and logout
$app->get('login5', 'login', 'login6'); 
$app->get('login8', 'login', 'login8'); 

# Start app
$app->start();

?>
