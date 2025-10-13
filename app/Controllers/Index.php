<?php

namespace Controllers;

class Index {
    public function getHome(\Base $base): void {
        $base->set("content", "home.html");
        echo \Template::instance()->render("index.html");
    }
}

