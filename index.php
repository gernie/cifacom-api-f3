<?php

$f3 = require('framework/base.php');

$f3->config('config.ini');

new Helper();
new Session();

$f3->db = new DB\SQL('mysql:host='.$f3->get('db.host').';dbname='.$f3->get('db.db').'', $f3->get('db.user'), $f3->get('db.pass'));
$f3->user = new User();

$f3->run();
