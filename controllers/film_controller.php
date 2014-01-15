<?php

class Film_controller extends Controller {
	public function get_id() {
		$this->f3->user->required(User::ROLE_MEMBER);
		Api::params('id');
		$film = new Film_model;
		$film = $film->find(array('id' => $_REQUEST['id']));
		if($film !== false) {
			Api::valid(array(
				'title'	=> $film->title,
				'desc'	=> $film->desc,
				'img'	=> $film->img,
			));
		} else {
			Api::error('404', 'The film does not exist');
		}
	}
	public function get_all() {
		$this->f3->user->required(User::ROLE_MEMBER);
		$film = new Film_model;
		Api::valid($film->listAll());
	}
	
	public function add_main() {
		$this->f3->user->required(User::ROLE_ADMIN);
		Api::params(array('title', 'desc', 'img'));
		$film = new Film_model;
		$film->title	= $_REQUEST['title'];
		$film->desc		= $_REQUEST['desc'];
		$film->img		= $_REQUEST['img'];
		$film->insert();
		Api::valid(true);
	}
	
	public function del_id() {
		$this->f3->user->required(User::ROLE_ADMIN);
		Api::params('id');
		$film = new Film_model;
		Api::valid($film->del($_REQUEST['id']));
	}
}
