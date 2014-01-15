<?php

class Controller {
	protected $f3 = null;
	public function __construct() {
		$this->f3 =& get_instance();
		API::allVarToGlobalRequest();
	}
}