<?php

require(dirname(__FILE__).'/facebook/facebook.php');

class Fb extends Facebook {
	protected $f3 = null;
	
	public function __construct() {
		$this->f3 =& get_instance();
		parent::__construct($this->f3->get('facebook'));
	}
	public function get($query) {
		try {
			$data = $this->api($query);
		} catch(FacebookApiException $e) {
			if($this->f3->get('DEBUG') == 3) {
				var_dump($e);
			}
			$data = null;
		}
		return $data;
	}
	
	protected function setPersistentData($key, $value) {
		if (!in_array($key, self::$kSupportedKeys)) {
			self::errorLog('Unsupported key passed to setPersistentData.');
			return;
		}
		$session_var_name = $this->constructSessionVariableName($key);
		$this->f3->set('SESSION.'.$session_var_name, $value);
	}
	protected function getPersistentData($key, $default = false) {
		if(!in_array($key, self::$kSupportedKeys)) {
			self::errorLog('Unsupported key passed to getPersistentData.');
			return $default;
		}
		$session_var_name = $this->constructSessionVariableName($key);
		if($this->f3->get('SESSION.'.$session_var_name) !== false) {
			return $this->f3->get('SESSION.'.$session_var_name);
		} else {
			return $default;
		}
	}
	protected function clearPersistentData($key) {
		if(!in_array($key, self::$kSupportedKeys)) {
			self::errorLog('Unsupported key passed to clearPersistentData.');
			return;
		}
		$session_var_name = $this->constructSessionVariableName($key);
		$this->f3->set('SESSION.'.$session_var_name, null);
	}
}
