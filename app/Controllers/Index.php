<?php

namespace Controllers;

class Index {
    public function getHome(\Base $base): void {
        $base->set("content", "home.html");
        echo \Template::instance()->render("index.html");
    }

    public function getSearch(\Base $base): void {
        $query = $base->get("GET.q");
        $data = file_get_contents($base->get("QS.ATHEJA_SERVER_URL") . "/api/search?q=" . $query);
        $result = json_decode($data, true);
        $resultCount = $result["total_results"];
        $base->set("entries_count", $resultCount);
        $base->set("entries", $result["results"]);
        $base->set("content", "search.html");
        echo \Template::instance()->render("index.html");
    }

    public function getError(\Base $base): void {
        $base->set("content", "error.html");
        echo \Template::instance()->render("index.html");
    }
}

