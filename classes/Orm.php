<?php

//////////////////////////////////////////////////////////////
//   ____  _____  __  __                                    //
//  / __ \|  __ \|  \/  |                                   //
// | |  | | |__) | \  / |  by H4ris (adapté par SiteXw)     //
// | |  | |  _  /| |\/| |  v0.1 (Spaghetti Panzani Version) //
// | |__| | | \ \| |  | |                                   //
//  \____/|_|  \_\_|  |_|                                   //
//                                                          //
//////////////////////////////////////////////////////////////

abstract class ORM {
	protected $f3 = null;

	public $_config = array(
		'table'			=> '',
		'primarykey'	=> array(),
		'columns'		=> array(),
		'tableJoin'		=> '',
		'leftJoinRow'	=> '',
		'rightJoinRow'	=> '',
		'queryJoin'		=> '',
	);
	
	public function __construct() {
		$this->f3 =& get_instance();
		return $this;
	}
	
	public function getConnection($obj = null) {
		if(isset($_GLOBAL['ORM']) === false) {
			try {
				$_GLOBAL['ORM'] = new PDO('mysql:host='.$this->f3->get('db.host').';dbname='.$this->f3->get('db.db'), $this->f3->get('db.user'), $this->f3->get('db.pass'), array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			} catch(PDOException $e) {
				Api::error(500, 'Error connecting to database');
			}
		}
		return $_GLOBAL['ORM'];
	}

	public function setTableName($name) {
		$bdd = $this->getConnection($this);
		$this->_config['table'] = $name;
		$this->_config['columns'][$this->_config['table']] = array();
		if($req = $bdd->query('DESCRIBE ' . $this->_config['table'])) {
			foreach($req->fetchAll(PDO::FETCH_ASSOC) as $column) {
				if($column['Key'] == 'PRI')
					$this->_config['primarykey'][$this->_config['table']] = $column['Field'];
				else
					array_push($this->_config['columns'][$this->_config['table']], $column['Field']);
			}
		} else {
			echo 'Une erreur est survenue (SET TABLE NAME)';
			exit();
		}
	}

	public function setJoinTable($table, $val1, $val2) {
		$bdd = $this->getConnection($this);
		$this->_config['tableJoin'] = $table;
		$leftJoinRow = explode('.', $val1);
		$this->_config['leftJoinRow'] = $leftJoinRow[1];                
		$rightJoinRow = explode('.', $val2);
		$this->_config['rightJoinRow'] = $rightJoinRow[1];
		$this->_config['tableJoin'] = $table;
		$this->_config['queryJoin'] = "LEFT JOIN " . $table . " ON " . $val1 . "=" . $val2;
		$this->_config['columns'][$this->_config['tableJoin']] = array();
		if($req = $bdd->query('DESCRIBE ' . $this->_config['tableJoin'])) {
			foreach($req->fetchAll(PDO::FETCH_ASSOC) as $column) {
				if($column['Key'] == 'PRI')
					$this->_config['primarykey'][$this->_config['tableJoin']] = $column['Field'];
				else
					array_push($this->_config['columns'][$this->_config['tableJoin']], $column['Field']);
			}
		} else {
			echo 'Une erreur est survenue (JOIN TABLE)';
			exit();
		}
	}
	
	protected function whereOperator($where) {
		$where = explode(' ', $where);
		if(count($where) == 1) {
			$where[1] = '=';
		}
		return array($where[0], $where[1]);
	}

	public function query($query, $fetchAll = false, $values = array(), $toObj = false) {
		$bdd = $this->getConnection($this);
		$req = $bdd->prepare($query);
		if($req->execute($values)) {
			if(strpos($query, 'SELECT') !== false) {
				if($fetchAll) {
					return $req->fetchAll(PDO::FETCH_ASSOC);
				} else {
					$result = $req->fetch(PDO::FETCH_ASSOC);
					if($toObj && $result) {
						foreach($result as $key => $val)
							$this->$key = $val;
					}
					return $result;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function find($whereValue = null) {
		$bdd = $this->getConnection($this);
		if ($whereValue == null) {
			$primarykey = $this->_config['primarykey'][$this->_config['table']];
			$whereValue = array( $primarykey => $this->$primarykey );
		} else if (!is_array($whereValue)) {
			$whereValue = array( $this->_config['primarykey'][$this->_config['table']] => $whereValue );
		}
		$valueArr = array();
		$query = 'SELECT * FROM ' . $this->_config['table'] . ' ' . $this->_config['queryJoin'] . ' WHERE ';
		foreach ($whereValue as $key => $val) {
			$where = $this->whereOperator($key);
			$query .= '`' . $where[0] . '` ' . $where[1] . ' :' . $where[0] . ' AND ';
			$valueArr[$where[0]] = $val;
		}
		$query = substr($query, 0, -5);
		$req = $bdd->prepare($query);
		if($req->execute($valueArr)) {
			if($user = $req->fetch(PDO::FETCH_ASSOC)) {
				foreach($user as $key => $val)
					$this->$key = $val;
			} else {
				return false;
			}
		} else {
			echo 'Une erreur est survenue (FIND)';
			exit();
		}
		return $this;
	}

	public function listAll() {
		$bdd = $this->getConnection($this);
		$req = $bdd->query('SELECT * FROM ' . $this->_config['table'] . ' ' . $this->_config['queryJoin']);
		if($req->execute()) {
			if($res = $req->fetchAll(PDO::FETCH_ASSOC))
				return $res;
			else
				return false;
		} else {
			echo 'Une erreur est survenue (FIND ALL)';
			exit();
		}
	}

	public function findAll($whereValue = null) {
		$bdd = $this->getConnection($this);
		if ($whereValue == null) {
			$primarykey = $this->_config['primarykey'][$this->_config['table']];
			$whereValue = array( $primarykey => $this->$primarykey );
		} else if (!is_array($whereValue)) {
			$whereValue = array( $this->_config['primarykey'][$this->_config['table']] => $whereValue );
		}                
		$valueArr = array();
		$query = 'SELECT * FROM ' . $this->_config['table'] . ' ' . $this->_config['queryJoin'] . ' WHERE ';
		foreach ($whereValue as $key => $val) {
			$where = $this->whereOperator($key);
			$query .= '`' . $where[0] . '` ' . $where[1] . ' :' . $where[0] . ' AND ';
			$valueArr[$where[0]] = $val;
		}
		$query = substr($query, 0, -5);
		$req = $bdd->prepare($query);
		if($req->execute($valueArr)) {
			if($res = $req->fetchAll(PDO::FETCH_ASSOC))
				return $res;
			else
				return false;
		} else {
			echo 'Une erreur est survenue (FIND ALL)';
			exit();
		}
		return $this;
	}

	public function update($whereValue = null) {
		$bdd = $this->getConnection($this);
		if ($whereValue == null) {
			$primarykey = $this->_config['primarykey'][$this->_config['table']];
			$whereValue = array( $primarykey => $this->$primarykey );
		} else if (!is_array($whereValue)) {
			$whereValue = array( $this->_config['primarykey'][$this->_config['table']] => $whereValue );
		}
		$valueArr = array();
		$query = 'UPDATE ' . $this->_config['table'] . ' ' . $this->_config['queryJoin'] . ' SET ';
		foreach($this->_config['columns'][$this->_config['table']] as $column) {
			if(isset($this->$column)) {
				$colname = $this->_config['table'] . '_' . $column;
				$valueArr[$colname] = $this->$column;
				$query .= ' ' . $this->_config['table'] . '.' . $column . ' = :' . $colname . ',';
			}
		}
		if(!empty($this->_config['tableJoin'])) {
			foreach($this->_config['columns'][$this->_config['tableJoin']] as $column) {
				if(isset($this->$column)) {
					$colname = $this->_config['table'] . '_' . $column;
					$valueArr[$colname] = $this->$column;
					$query .= ' ' . $this->_config['tableJoin'] . '.' . $column . ' = :' . $colname . ',';
				}
			}
		}
		$query = substr($query, 0, -1) . ' WHERE ';                
		foreach ($whereValue as $key => $val) {
			$where = $this->whereOperator($key);
			$query .= '`' . $where[0] . '` ' . $where[1] . ' :w_' . $where[0] . ' AND ';
			$valueArr['w_'.$where[0]] = $val;
		}                
		$query = substr($query, 0, -5);                
		$req = $bdd->prepare($query);
		if(!$req->execute($valueArr)) {
			echo 'Une erreur est survenue (UPDATE)';
			exit();
		}
		return $this;
	}

	public function delete($whereValue = null) {
		$bdd = $this->getConnection($this);
		if ($whereValue == null) {
			$primarykey = $this->_config['primarykey'][$this->_config['table']];
			$whereValue = array( $primarykey => $this->$primarykey );
		} else if (!is_array($whereValue)) {
			$whereValue = array( $this->_config['primarykey'] => $whereValue );
		}
		$valueArr = array();
		$query = 'DELETE FROM ' . $this->_config['table'] . ' WHERE ';
		foreach ($whereValue as $key => $val) {
			$where = $this->whereOperator($key);
			$query .= '`' . $where[0] . '` ' . $where[1] . ' :' . $where[0] . ' AND ';
			$valueArr[$where[0]] = $val;
		}
		$query = substr($query, 0, -5);
		$req = $bdd->prepare($query);
		if(!$req->execute($valueArr)) {                
			echo 'Une erreur est survenue (DELETE)';
			exit();
		}
		return $this;
	}

	public function deleteAll() {
		$bdd = $this->getConnection($this);
		$req = $bdd->query('DELETE FROM ' . $this->_config['table']);
		if(!$req->execute()) {                
			echo 'Une erreur est survenue (DELETE ALL)';
			exit();
		}
		return $this;
	}

	public function insert() {
		$bdd = $this->getConnection($this);

		// INSERT INTO PRIMARY TABLE
		$query = 'INSERT INTO ' . $this->_config['table'];
		$writeColumn = array();
		$valueArray = array();
		foreach($this->_config['columns'][$this->_config['table']] as $column) {
			if(isset($this->$column)) {
				$valueArray[$column] = $this->$column;
				array_push($writeColumn, $column);
			}
		}
		$req = $bdd->prepare($query . '(`' . join('`,`', $writeColumn) . '`) VALUES(:' . join(', :', $writeColumn) . ');');
		if(!$req->execute($valueArray)) {
			echo 'Une erreur est survenue (INSERT)';
			exit();
		} else {
			if($bdd->lastInsertId()) {
				$this->find($bdd->lastInsertId());
			}
		}

		// INSERT INTO JOIN TABLE
		if(!empty($this->_config['tableJoin'])) {
			$queryJoin = 'INSERT INTO ' . $this->_config['tableJoin'] . '(' . $this->_config['rightJoinRow'] . ') VALUES(:primaryVal);';
			$req = $bdd->prepare($queryJoin);
			$rowName = $this->_config['leftJoinRow'];
			if(!$req->execute(array('primaryVal' => $this->$rowName))) {
				echo 'Une erreur est survenue (INSERT IN JOIN TABLE)';
				exit();
			} else {
				$this->update();
			}
		}

		return $this;
	}

	public function formToObj($post, $replaceBy = array()) {
		foreach($post as $key => $val) {
			if (isset($replaceBy[$key]))
				$this->$replaceBy[$key] = $val;
			elseif (in_array($key, $this->_config['columns'][$this->_config['table']]) || in_array($key, $this->_config['columns'][$this->_config['tableJoin']]))
				$this->$key = $val;
		}
		return $this;
	}

	public function set($key, $value) {
		$this->$key = $value;
		return $this;
	}
}
