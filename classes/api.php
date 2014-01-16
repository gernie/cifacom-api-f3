<?php

class Api {
	public static function allVarToGlobalRequest() {
		$f3 =& get_instance();
		parse_str($f3->get('BODY'), $data);
		$_REQUEST = array_merge($_REQUEST, $data);
	}
	public static function response($data, $code=200, $error=false) {
		header('Content-type: application/json; charset=utf-8');
		//header('HTTP/1.1 '.$code.' Not Found');
		$response = array(
			'meta' => array(
				'code' => $code
			),
			'data' => $data,
		);
		if($error != false) {
			$response['meta']['error'] = $error;
		}
		exit(json_encode($response));
	}
	public static function valid($data, $sortable=false) {
		if($sortable === true) {
			$offset = (isset($_REQUEST['offset']) AND is_numeric($_REQUEST['offset']) AND $_REQUEST['offset'] >= 0)?(int)$_REQUEST['offset']:0;
			$limit = (isset($_REQUEST['limit']) AND is_numeric($_REQUEST['limit']) AND $_REQUEST['offset'] >= 0)?(int)$_REQUEST['limit']:null;
			if($offset !== 0 OR $limit != null) {
				$data = array_slice($data, $offset, $limit);
			}
		}
		return self::response($data, 200);
	}
	public static function error($code, $error=false) {
		return self::response(array(), $code, $error);
	}
	public static function newToken() {
		$f3 =& get_instance();
		$carac = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$token = '';
		for($i=1; $i<=(int)$f3->get('tokenSize'); $i++) {
			$token .= $carac[rand(0, strlen($carac)-1)];
		}
		return $token;
	}
	public static function params($params) {
		if(is_array($params) === false) {
			$params = array($params);
		}
		foreach($params as $param) {
			if(isset($_REQUEST[$param]) === false) {
				Api::error(400, 'Missing argument : '.implode($params, ', '));
			}
		}
	}
}