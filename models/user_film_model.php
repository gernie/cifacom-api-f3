<?php

class User_film_model extends ORM {
	const TYPE_LIKE	= 'like';
	const TYPE_SEEN	= 'seen';
	const TYPE_WLS	= 'wls';
	
	public function __construct() {
		parent::__construct();
		$this->setTableName('user_film');
	}
	
	public function add($film_id, $type) {
		$film = new Film_model;
		if($film->find(array('id' => $film_id)) === false) {
			Api::error('404', 'The film does not exist');
		}
		if($this->find(array('user_id' => $this->f3->user->getId(), 'film_id' => $film->id, 'type' => $type)) !== false) {
			return false;
		}
		$this->user_id = $this->f3->user->getId();
		$this->film_id = $film->id;
		$this->type = $type;
		$this->insert();
		return true;
	}
	public function del($film_id, $type) {
		$film = new Film_model;
		if($film->find(array('id' => $film_id)) === false) {
			Api::error('404', 'The film does not exist');
		}
		if($this->find(array('user_id' => $this->f3->user->getId(), 'film_id' => $film->id, 'type' => $type)) === false) {
			return false;
		}
		$this->delete(array('user_id' => $this->f3->user->getId(), 'film_id' => $film->id, 'type' => $type));
		return true;
	}
	public function getAll($type) {
		$this->setJoinTable('film', 'user_film.film_id', 'film.id');
		$user_film = $this->findAll(array('user_id' => $this->f3->user->getId(), 'type' => $type));
		$return = array();
		if($user_film !== false) {
			foreach($user_film as $film) {
				$return[] = array(
					'title'	=> $film['title'],
					'desc'	=> $film['desc'],
					'img'	=> $film['img'],
				);
			}
		}
		return $return;
	}
}
