<?php

class Film_model extends ORM {
	public function __construct() {
		parent::__construct();
		$this->setTableName('film');
	}
	public function del($id) {
		if($this->find(array('id' => $id)) === false) {
			Api::error('404', 'The film does not exist');
		}
		$this->delete(array('id' => $id));
		return true;
	}
}
