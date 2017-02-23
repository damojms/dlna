<?php

require_once '../vendor/autoload.php';

$f3 = \Base::instance();

$f3->config('../app/configs/config.ini');
$f3->config('../app/configs/routes.ini');

$f3->set('DB', new DB\SQL('sqlite:'.$f3->{DBPATH}));

$f3->run();
