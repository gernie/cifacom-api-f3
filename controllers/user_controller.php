<?php

class User_controller extends Controller {
	//public function add_facebook() {
	//	$fb = new Fb();
	//	if((bool) $fb->getUser() === false) {
	//		Api::valid(array('url' => $fb->getLoginUrl(array('scope' => 'email'))));
	//	} else {
	//		Api::valid($fb->get('/me'));
	//	}
	//}
	public function add_main() {
		$this->f3->user->required(User::ROLE_GUEST);
		Api::params(array('mail', 'pass'));
		$user = new User_model;
		Api::valid($user->add($_REQUEST['mail'], $_REQUEST['pass'], 100));
	}
	public function get_token() {
		$this->f3->user->required(User::ROLE_GUEST);
		Api::params(array('mail', 'pass'));
		if($this->f3->user->loginClassic($_REQUEST['mail'], $_REQUEST['pass'])) {
			Api::valid($this->f3->user->getToken());
		} else {
			Api::error(403, 'Incorrect login');
		}
	}
	public function get_me() {
		$_REQUEST['id'] = $this->f3->user->getId();
		$this->get_id();
	}
	public function get_id() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		if($_REQUEST['id'] != $this->f3->user->getId()) {
			$this->f3->user->required(User::ROLE_ADMIN);
		}
		$user = new User_model();
		$user = $user->find(array('id' => $_REQUEST['id']));
		if($user !== false) {
			Api::valid(array(
				'id'	=> (int) $user->id,
				'mail'	=>		 $user->mail,
				'pass'	=>		 $user->pass,
				'token'	=>		 $user->token,
				'role'	=> (int) $user->role,
			));
		} else {
			Api::error('404', 'The user does not exist');
		}
	}
	public function get_all() {
		$this->f3->user->required(User::ROLE_ADMIN);
		$user = new User_model();
		Api::valid($user->listAll());
	}
	
	public function set_me() {
		$_REQUEST['id'] = $this->f3->user->getId();
		$this->set_id();
	}
	public function set_id() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		if($_REQUEST['id'] != $this->f3->user->getId()) {
			$this->f3->user->required(User::ROLE_ADMIN);
		}
		$user = new User_model;
		$user->find(array('id' => $_REQUEST['id']));
		if($user !== false) {
			if(isset($_REQUEST['mail'])) {
				$mail = new User_model;
				if($mail->find(array('id !=' => $_REQUEST['id'], 'mail' => $_REQUEST['mail'])) !== false) {
					Api::error('409', 'Email address already exists');
				}
				$user->mail = $_REQUEST['mail'];
			}
			if(isset($_REQUEST['pass'])) {
				$user->pass = hash('sha512', $_REQUEST['pass']);
			}
			if(isset($_REQUEST['role'])) {
				$role_required = $this->f3->user->getRole()+1;
				if($_REQUEST['role'] >= $role_required) {
					Api::error(401, 'Requires role equal or more to '.$role_required.' (your role level: '.$this->f3->user->getRole().')');
				}
				$user->role = $_REQUEST['role'];
			}
			$user->update();
			Api::valid(true);
		} else {
			Api::error('404', 'The user does not exist');
		}
	}
	
	public function del_me() {
		$_REQUEST['id'] = $this->f3->user->getId();
		$this->del_id();
	}
	public function del_id() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		if($_REQUEST['id'] != $this->f3->user->getId()) {
			$this->f3->user->required(User::ROLE_ADMIN);
		}
		$user = new User_model;
		Api::valid($user->del($_REQUEST['id']));
	}
	
	public function add_film_like() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->add($_REQUEST['id'], User_film_model::TYPE_LIKE));
	}
	public function add_film_seen() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->add($_REQUEST['id'], User_film_model::TYPE_SEEN));
	}
	public function add_film_wls() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->add($_REQUEST['id'], User_film_model::TYPE_WLS));
	}
	
	public function del_film_like() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->del($_REQUEST['id'], User_film_model::TYPE_LIKE));
	}
	public function del_film_seen() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->del($_REQUEST['id'], User_film_model::TYPE_SEEN));
	}
	public function del_film_wls() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$user_film = new User_film_model;
		Api::valid($user_film->del($_REQUEST['id'], User_film_model::TYPE_WLS));
	}
	
	public function get_film_like() {
		$this->f3->user->required(User::ROLE_MEMBER);
		$user_film = new User_film_model;
		Api::valid($user_film->getAll(User_film_model::TYPE_LIKE));
	}
	public function get_film_seen() {
		$this->f3->user->required(User::ROLE_MEMBER);
		$user_film = new User_film_model;
		Api::valid($user_film->getAll(User_film_model::TYPE_SEEN));
	}
	public function get_film_wls() {
		$this->f3->user->required(User::ROLE_MEMBER);
		$user_film = new User_film_model;
		Api::valid($user_film->getAll(User_film_model::TYPE_WLS));
	}
}
