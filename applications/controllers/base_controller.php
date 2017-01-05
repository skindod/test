<?php

/**
 * 
 */
class base_controller extends slm_controller {

    function __construct() {
        parent::__construct();
        //if (session::get('log_status') == true) {
        //    $this->redirect('dashboard');
        //}
    }

    function custom_render($content, $data = array()) 
    {
	$this->render_template('header', $data);
	$this->render($content, $data);
	$this->render_template('footer', $data);
    }
}
