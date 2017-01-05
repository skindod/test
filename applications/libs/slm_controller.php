<?php

class slm_controller {

    protected $request;
    private $directory;
    protected $controller;

    function __construct() {
	global $slm_directory;
	$this->request = new request();
	$this->directory = $slm_directory;

	$path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[1]))
            $this->controller = $path[1];
    }

    # render single view file

    function render($view, $data = array(), $location = 'view') {
	$file = $this->directory[$location] . $view . '.php';

	if (strpos($view, " ") !== false) {
	    app::show_error('file name could not have space');
	}

	if (file_exists($file)) {
	    include_once($file);
	} else {
	    app::show_error('view file not exists at ' . $file);
	}
    }

    # render template view

    function render_template($view, $data = array()) {
	$this->render($view, $data, 'template');
    }

    function render_html($html) {
	echo $html;
    }

    function render_json($data = array()) {
	header('Content-type: application/json');
	echo json_encode($data);
	exit;
    }

    function redirect($path) {
	header("Location: " . BASE_URL . $path);
    }

}

?>