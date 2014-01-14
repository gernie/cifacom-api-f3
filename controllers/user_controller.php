<?php

class User_controller extends Controller {
	//public function register() {
	//	$fb = new Fb();
	//	Api::response($fb->getUser());
	//	if((bool) $fb->getUser() === false) {
	//		Api::valid(array('url' => $fb->getLoginUrl()));
	//	} else {
	//		Api::valid($fb->get('/me'));
	//	}
	//}
	public function get_token() {
		Api::params(array('mail', 'pass'));
		if($this->f3->user->loginClassic($_GET['mail'], $_GET['pass'])) {
			Api::valid(array('token' => $this->f3->user->getToken()));
		} else {
			Api::error(403, 'Incorrect login');
		}
	}
	public function get_me() {
		$this->f3->user->required();
		Api::valid(array('user' => array(
			'id'	=> $this->f3->user->getId(),
			'mail'	=> $this->f3->user->getMail(),
			'pass'	=> $this->f3->user->getPass(),
			'token'	=> $this->f3->user->getToken(),
			'role'	=> $this->f3->user->getRole(),
		)));
	}
	public function get_id() {
		$this->f3->user->required(User::ROLE_ADMIN);
		Api::params('id');
		$user = $this->f3->db->exec('SELECT * FROM user WHERE id = :id LIMIT 1', array(':id' => $_GET['id']));
		if(count($user) == 1) {
			Api::valid(array('user' => $user[0]));
		} else {
			Api::valid(array('user' => null));
		}
	}
	public function get_all() {
		$this->f3->user->required(User::ROLE_ADMIN);
		Api::valid(array('users' => $this->f3->db->exec('SELECT * FROM user')));
	}
}
