<?php

class User extends ORM {
	const ROLE_ADMIN	= 200;
	const ROLE_MEMBER	= 100;
	const ROLE_GUEST	= 0;
	
	protected $id = 0;
	protected $mail = null;
	protected $pass = null;
	protected $token = null;
	protected $role = 0;
	
	public function __construct() {
		parent::__construct();
		$this->setTableName('user');
		if(isset($_REQUEST['token'])) {
			$this->loginToken($_REQUEST['token']);
		}
	}
	
	public function loginClassic($mail, $pass) {
		$is_object = is_object($this->find(array(
			'mail' => $mail,
			'pass' => hash('sha512', $pass),
		)));
		$this->id	= (int) $this->id;
		$this->role	= (int) $this->role;
		return $is_object;
	}
	public function loginToken($token) {
		$is_object = is_object($this->find(array(
			'token' => $token,
		)));
		$this->id	= (int) $this->id;
		$this->role	= (int) $this->role;
		return $is_object;
	}
	
	public function required($role) {
		if($this->getRole() < $role) {
			Api::error(401, 'Requires role equal or more to '.$role.' (your role level: '.$this->getRole().')');
		} 
	}
	
	public function getId()		{  return $this->id; }
	public function getMail()	{  return $this->mail; }
	public function getPass()	{  return $this->pass; }
	public function getToken()	{  return $this->token; }
	public function getRole()	{  return $this->role; }
}
