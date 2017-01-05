<?php

/**
* 
*/
class app
{
	// Requested URL
	private $url;

	// Http request type
	private $request_type;

	// Http request object, POST or GET
	private $request;

	// Method arguments
	private $arguments;

	// Controller parsed from arguments
	private $controller;

	// Controller method / action to be performed
	private $method;

	// Boolean to indicate of request is type of API
	private $is_api = FALSE;

	// Routing array with format 
	private $routes;

	// App constructor
	function __construct()
	{
		// Create request object
		$this->request = new request();

		// App routing
		$this->routes = array();

		// Initialize arguments
		$this->arguments = array();

		// Read request type, GET or POST
		$this->request_type = $_SERVER['REQUEST_METHOD'];
	}

	function start()
	{
		// Start parsing URL, i.e example.com/user/1
		$this->parse_url();

		if (!$this->verify_routing()) {
			//$this->show_error('routing not found');
                    $this->controller = 'page_not_found';
                    $this->method = 'index';
                    //echo "The page you're looking for has gone for vacation";
                    //exit();
		}

		$this->display();
	}

	private function display()
	{
		// File naming format. controller with 'name_controller' and method 'name_action'
		$controller = $this->controller . '_controller';
		$method = $this->method . '_action';

		try {
			// check if class and method exist
			if (class_exists($controller)) 
			{
			    $curr = new $controller(); // create object class

			    // check method to be called
			    if (method_exists($curr, $method)) 
			    {
			    	$size = count($this->arguments);
			    	if ($size == 0) {
			    		$curr->$method();
			    	}
			    	else if ($size == 1) {
			    		$curr->$method($this->arguments[0]);
			    	}
			    	else if ($size == 2) {
			    		$curr->$method($this->arguments[0], $this->arguments[1]);
			    	}
			    	else if ($size == 3) {
			    		$curr->$method($this->arguments[0], $this->arguments[1], $this->arguments[2]);
			    	}
			    }
			    else {
			    	$this->show_error('method ' . $controller . '::' . $method . ' not found.');
			    }
			}
		}
		catch (Exception $e) {
			$this->show_error($e->getMessage());
		}
	}

	function verify_routing()
	{
		// Check if routing hasnt added
		if (count($this->routes) == 0) {
			$this->show_error('no routes added');
		}

		// show default route at index 0, if url parameter is empty
		if (strlen($this->url) == 0) {
			$this->controller = $this->routes[0]['controller'];
			$this->method = $this->routes[0]['method'];

			return TRUE;
		}

		$parameters = explode('/', $this->url);
		// Check if it is request for API
		if ($parameters[0] == 'api') {
			$this->is_api = true;
			array_shift($parameters);
		}


		// $current_route = $this->url;

		if (count($parameters) == 1) {
			$current_route = $parameters[0];
		}
		else if (count($parameters) > 1) {
			if (is_numeric($parameters[1])) {
				$current_route = $parameters[0];
			}
			else{
				$current_route = $parameters[0] . '/' . $parameters[1];
			}
		}
		
		// echo $current_route . $this->is_api;

		foreach ($this->routes as $route) {
			// echo '<pre>';
			// print_r($route);
			// echo '</pre>';

			if ($route['type'] == $this->request_type && $route['route'] == $current_route && $this->is_api == $route['is_api']) {
				$this->controller = $route['controller'];
				$this->method = $route['method'];

				return TRUE;
			}
		}

		return FALSE;
	}

	function get($route, $controller, $method = 'index', $is_api = FALSE)
	{
		$this->addRoute('GET', $route, $controller, $method, $is_api);
	}

	function post($route, $controller, $method = 'index', $is_api = FALSE)
	{
		$this->addRoute('POST', $route, $controller, $method, $is_api);
	}

	function addRoute($type, $route, $controller, $method, $is_api)
	{
		$route = rtrim($route, '/');

		if (is_numeric($controller) || is_numeric($method))
		{
			$this->show_error('routing: controller or method cannot be numeric');
		}

		// Check if it is request for API
		if ($is_api == TRUE) {
			$route = str_replace('api/', '', $route);
		}

		$routing = array(
			'type' => $type,
			'route' => $route,
			'controller' => $controller,
			'method' => $method,
			'is_api' => $is_api
		);

		array_push($this->routes, $routing);
	}

	function parse_url()
	{
		// Get url arguments
		$url = $this->request->get('url');

		// Remove slash if found in last index of url, remove invalid characters
		$url = rtrim($url, '/');
		$this->url = filter_var($url, FILTER_SANITIZE_URL);

		$parameters = explode('/', $url);

		// Check if it is request for API
		if ($parameters[0] == 'api') {
			$this->is_api = true;
			array_shift($parameters);
		}

		$parameter_size = count($parameters);

		$start_index = 2; // Start get the parameters from index 2, i.e example.com/user/edit/1
		
		if ($parameter_size > 1) {
			if (is_numeric($parameters[1])) {
				$start_index = 1; // i.e example.com/user/1
			}
		}

		if ($parameter_size > 0) 
		{
			for ($i = $start_index; $i < $parameter_size; $i++) { 
				array_push($this->arguments, $parameters[$i]);
			}
		}

	}

	function info()
	{
		// for debugging purpose
		header("Content-Type: application/json");
		$app_detail = array(
						'url' => $this->url,
						'arguments' => $this->arguments,
						'controller' => $this->controller,
						'method' => $this->method,
						'is_api' => $this->is_api
					);
		echo json_encode(array('current' => $app_detail, 'routes' => $this->routes));
		exit;
	}

	static function show_error($message = '')
	{
		header("Content-Type: application/json");
		$error = array(
			'status' => 'error',
			'message' => $message
		);
		echo json_encode($error);
		exit;
	}

}