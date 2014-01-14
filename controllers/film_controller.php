<?php

class Film_controller extends Controller {
	public function get_id() {
		$this->f3->user->required();
		Api::params('id');
		$film = $this->f3->db->exec('SELECT * FROM film WHERE id = :id LIMIT 1', array(':id' => $_GET['id']));
		if(count($film) == 1) {
			Api::valid(array('film' => $user[0]));
		} else {
			Api::valid(array('film' => null));
		}
	}
	public function get_all() {
		$this->f3->user->required();
		Api::valid(array('films' => $this->f3->db->exec('SELECT * FROM film')));
	}
	
	public function insert_main() {
		$this->f3->user->required(User::ROLE_ADMIN);
		Api::params(array('title', 'desc', 'img'));
		$this->f3->db->exec('INSERT INTO film (`title`, `desc`, `img`) VALUES (:title, :desc, :img)', array(
			':title'	=> $_GET['title'],
			':desc'		=> $_GET['desc'],
			':img'		=> $_GET['img'],
		));
		Api::valid(array('insert' => true));
	}
}
