<?php

class user {
	protected $f3 = null;
	
	const ROLE_ADMIN	= 200;
	const ROLE_MEMBER	= 100;
	const ROLE_GUEST	= 0;
	
	protected $id = 0;
	protected $mail = null;
	protected $pass = null;
	protected $token = null;
	protected $role = 0;
	
	public function __construct() {
		$this->f3 =& get_instance();
	}
	
	public function loginClassic($mail, $pass) {
		return $this->loginHydrate($this->f3->db->exec('SELECT * FROM user WHERE mail = :mail AND pass = :pass LIMIT 1', array(
			':mail' => $mail,
			':pass' => hash('sha512', $pass),
		)));
	}
	public function loginToken($token) {
		return $this->loginHydrate($this->f3->db->exec('SELECT * FROM user WHERE token = :token LIMIT 1', array(
			':token' => $token,
		)));
	}
	protected function loginHydrate($user) {
		if(count($user) == 0) {
			return false;
		} else {
			$this->id		= (int) $user[0]['id'];
			$this->mail		= 		$user[0]['mail'];
			$this->pass		= 		$user[0]['pass'];
			$this->token	= 		$user[0]['token'];
			$this->role		= (int) $user[0]['role'];
			return true;
		}
	}
	
	public function required($role=User::ROLE_MEMBER) {
		if(isset($_GET['token'])) {
			$this->loginToken($_GET['token']);
		}
		if($this->getRole() < $role) {
			Api::error(401, 'Requires role equal or more to '.$role.' (role level: '.$this->getRole().')');
		} 
	}
	
	public function getId()		{  return $this->id; }
	public function getMail()	{  return $this->mail; }
	public function getPass()	{  return $this->pass; }
	public function getToken()	{  return $this->token; }
	public function getRole()	{  return $this->role; }
}