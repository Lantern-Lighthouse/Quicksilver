<?php

require('./vendor/autoload.php');
$base = \Base::instance();
$base->config('./app/Configs/config.ini');

if($base->get("SESSION.token_expire") < date('Y-m-d H:i:s')) {
    $base->clear("SESSION");
}

$base->run();
