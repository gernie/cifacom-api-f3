<?php

$f3 = require('framework/base.php');
$f3->config('config.ini');

new Helper();
$f3->user = new User;
//$f3->route('GET *', function() {
//	Api::error(404, 'Not Found');
//});

$f3->run();
