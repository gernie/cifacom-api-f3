<?php

class ControllerLoader {
	public function loader() {
		$f3 =& get_instance();
		$url = $f3->get('PARAMS');
		$strpos = strpos($url[0], '?');
		if($strpos == false) {
			$url = $url[0];
		} else {
			$url = substr($url[0], 0, $strpos);
		}
		$url = explode('/', substr($url, 1));
		foreach($url as &$element) {
			$element = strtolower($element);
		}
		$request_method = array(
			'get'		=> 'get',
			'post'		=> 'add',
			'put'		=> 'set',
			'delete'	=> 'del',
		);
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if(isset($url[0], $request_method[$url[0]]) === true) {
			if(isset($url[2]) === false OR $url[2] == '') {
				$url[2] = 'main';
			}
			if(count($url) < 3) {
				Api::error(404, 'Not Found');
			}
			$method		= $url[0];
			$controller	= $url[1];
			$action		= 2;
		} else {
			if(isset($url[1]) === false OR $url[1] == '') {
				$url[1] = 'main';
			}
			if(count($url) < 2) {
				Api::error(404, 'Not Found');
			}
			$controller	= $url[0];
			$action		= 1;
		}
		$action = implode('_', array_slice($url, $action));
		$controllers = array();
		if($opendir = opendir('./controllers')) {
			while(false !== ($controller_file = readdir($opendir))) {
				if($controller_file != '.' && $controller_file != '..') {
					$controllers[] = str_replace('_controller.php', '', $controller_file);
				}
			}
			closedir($opendir);
		}
		if(in_array($controller, $controllers) === false) {
			Api::error(404, 'Not Found');
		}
		$class_name = $controller.'_controller';
		$class = new $class_name;
		$method_name = $request_method[$method].'_'.$action;
		if(method_exists($class, $method_name) === false) {
			Api::error(404, 'Not Found');
		}
		$class->{$method_name}();
	}
}
