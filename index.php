<?php

require('./vendor/autoload.php');
$base = \Base::instance();
$base->config('./app/Configs/config.ini');

if(str_ends_with($base->get("QS.ATHEJA_SERVER_URL"), '/')) {
    $base->set("QS.ATHEJA_SERVER_URL", substr($base->get("QS.ATHEJA_SERVER_URL"), 0, strlen($base->get("QS.ATHEJA_SERVER_URL")) - 1));
}

if($base->get("SESSION.token_expire") < date('Y-m-d H:i:s')) {
    $base->clear("SESSION");
}

$base->run();
