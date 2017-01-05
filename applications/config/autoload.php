<?php

$slm_directory = array(
    'lib' 		=> APP_DIR . 'libs/',
    'model' 	=> APP_DIR . 'models/',
    'view' 		=> APP_DIR . 'views/',
    'template' 	=> APP_DIR . 'templates/',
    'controller'=> APP_DIR . 'controllers/',
);

# use autoloading to load all class automatically when instantiate
set_include_path(implode(PATH_SEPARATOR, $slm_directory));
spl_autoload_register();

?>