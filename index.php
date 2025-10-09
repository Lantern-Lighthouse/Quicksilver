<?php

require('./vendor/autoload.php');
$base = \Base::instance();
$base->config('./app/Configs/config.ini');

$base->run();
