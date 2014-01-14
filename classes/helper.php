<?php

class Helper {
	public function __construct() {
		function &get_instance() {
			global $f3;
			return $f3;
		}
		function site_url($url='') {
			$f3 =& get_instance();
			return $f3->get('site_url').$url;
		}
	}
}