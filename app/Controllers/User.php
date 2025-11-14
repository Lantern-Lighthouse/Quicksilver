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

        if((int)($statusCode / 100) !== 2) {
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

        if((int)($statusCode / 100) !== 2) {
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

    public function getEdit(\Base $base) {
		if($base->get("SESSION.token") !== null) {
			$username = $base->get("PARAMS.userID");

	        $data = file_get_contents($base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $username);
	        $result = json_decode($data, true);

	        $base->set("entry", $result);
	        $base->set("content", "edit_user.html");
	        echo \Template::instance()->render("index.html");
        } else {
        	$base->reroute("/error");
        }
    }

    public function postEdit(\Base $base) {
		if($base->get("SESSION.token") === null) {
        	$base->reroute("/error");
        }

        $ch = curl_init();

        $postFields = [
	        ...($base->get("POST.username") != $base->get("POST.username-bef") ? ["username" => $base->get("POST.username")] : []),
	        "displayname" => $base->get("POST.displayname"),
	        ...($base->get("POST.email") != $base->get("POST.email-bef") ? ["email" => $base->get("POST.email")] : []),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $base->get("PARAMS.userID") . "/edit",
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
        $base->reroute("/user/" . $base->get("POST.username"));
    }

    public function getDelete(\Base $base) {
		if($base->get("SESSION.token") !== null) {
			$userID = $base->get("PARAMS.userID");

	        $ch = curl_init();
	        $options = [
	            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $userID . "/delete",
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_POST => true,
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
	        $base->reroute("/");
        } else {
        	$base->reroute("/error");
        }
    }

    public function getUser(\Base $base) {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $base->get("PARAMS.userID"),
            CURLOPT_RETURNTRANSFER => true,
        ];

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if((int)($statusCode / 100) !== 2) {
            $error = curl_error($ch);
            curl_close($ch);
            $base->reroute("/error");
        }

        curl_close($ch);
        $base->set("user", $response);
        $base->set("content", "user.html");
        echo \Template::instance()->render("index.html");
    }
}
