<?php

namespace Controllers;

class Index {
    public function getHome(\Base $base): void {
        $base->set("content", "home.html");
        echo \Template::instance()->render("index.html");
    }

    public function getSearch(\Base $base): void {
        if(is_null($base->get("GET.q"))) {
            $base->reroute("/error");
        }

        $ch = curl_init();

        $postFields = [
            "query" => $base->get("GET.q"),
            "category" => $base->get("GET.cat") ?? array_key_first($base->get("search_categories")),
            "nsfw" => $base->get("GET.safe"),
            "min_karma" => $base->get("GET.threshold"),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/search",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
            CURLOPT_HTTPHEADER => array("Authorization: Bearer " . $base->get("SESSION.token")),
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if((int)($statusCode / 100) !== 2) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log($error);
            $base->reroute("/error");
        }

        curl_close($ch);

        $base->set("entries_count", $response["total_results"]);
        $base->set("encoded_query", urlencode($base->get("GET.q")));
        $base->set("entries", $response["results"]);
        $base->set("categories", array_keys($base->get("search_categories")));

        switch(array_change_key_case($base->get("search_categories"), CASE_LOWER)[strtolower($postFields["category"])]["type"]){
            case "articles":
                $base->set("content", "search.html");
            case "gallery":
                $base->set("content", "search_gallery.html");
        }
        
        echo \Template::instance()->render("index.html");
    }

    public function getError(\Base $base): void {
        $base->set("content", "error.html");
        echo \Template::instance()->render("index.html");
    }
}

