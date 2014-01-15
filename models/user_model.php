<?php

class User_model extends ORM {
	public function __construct() {
		parent::__construct();
		$this->setTableName('user');
	}
	
	public function add($mail, $pass, $role) {
		if($this->find(array('mail' => $mail)) !== false) {
			Api::error('409', 'Email address already exists');
		}
		$this->mail		= $mail;
		$this->pass		= hash('sha512', $pass);
		$this->token	= Api::newToken();
		$this->role		= (int) $role;
		$this->insert();
		return true;
	}
	public function del($id) {
		if($this->find(array('id' => $id)) === false) {
			Api::error('404', 'The user does not exist');
		}
		$this->delete(array('id' => $id));
		return true;
	}
}
