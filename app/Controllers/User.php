<?php

namespace Controllers;

class User {
    public function getLogin(\Base $base) {
        $base->set("content", "login.html");
        echo \Template::instance()->render("index.html");
    }

    public function getRegister(\Base $base) {
        $base->set("content", "register.html");
        echo \Template::instance()->render("index.html");
    }

    public function postLogin(\Base $base) {
        $ch = curl_init();

        $postFields = [
            "username" => $base->get("POST.username"),
            "password" => $base->get("POST.password"),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
        ];

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if((int)($statusCode / 100) === 2) {
            $error = curl_error($ch);
            curl_close($ch);
            $base->reroute("/error");
        }

        curl_close($ch);
        $base->set("SESSION.token", $response['session_token']);
        $base->set("SESSION.token_expire", $response['expires_at']);
        $base->reroute("/");
    }

    public function postRegister(\Base $base) {
       $ch = curl_init();

        $postFields = [
            "username" => $base->get("POST.username"),
            "displayname" => $base->get("POST.displayname"),
            "email" => $base->get("POST.email"),
            "password" => $base->get("POST.password"),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
        ];

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if((int)($statusCode / 100) === 2) {
            $error = curl_error($ch);
            curl_close($ch);
            $base->reroute("/error");
        }

        curl_close($ch);
        $base->reroute("/login");
    }

    public function getLogout(\Base $base){
        $base->clear("SESSION");
        $base->reroute("/login");
    }
}
