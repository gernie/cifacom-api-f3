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
			} else {
				API::error(501, 'Facebook API error');
			}
			$data = null;
		}
		return $data;
	}
}
