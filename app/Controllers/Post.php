<?php

namespace Controllers;

class Post {
	public function getEnter(\Base $base) {
		if($base->get("SESSION.token") !== null) {
	        $base->set("content", "entry_form.html");
	        echo \Template::instance()->render("index.html");
        } else {
        	$base->reroute("/error");
        }
	}

    public function postEnter(\Base $base) {
		if($base->get("SESSION.token") === null) {
        	$base->reroute("/error");
        }

        $ch = curl_init();

        $postFields = [
	        "fetch-name-from-site" => $base->get("POST.fetch-name-from-site"),
	        "page-name" => $base->get("POST.page-name"),
	        "page-desc" => $base->get("POST.page-desc"),
	        "page-url" => $base->get("POST.page-url"),
	        "tags" => $base->get("POST.tags"),
	        "is-nsfw" => $base->get("POST.is-nsfw"),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/create",
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
        $base->reroute("/");
    }

    public function getEdit(\Base $base) {
		if($base->get("SESSION.token") !== null) {
			$postID = $base->get("PARAMS.entryID");

	        $data = file_get_contents($base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/" . $postID);
	        $result = json_decode($data, true);

	        $tags = "";
	        foreach($result["tags"] as $tag){
	        	$tags = $tags . $tag["name"] . ";";
	        }
	        
	        $base->set("entry", $result);
	        $base->set("tags", $tags);
	        $base->set("content", "edit_entry.html");
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
	        "page-name" => $base->get("POST.page-name"),
	        "page-desc" => $base->get("POST.page-desc"),
        ];

        $options = [
            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/" . $base->get("PARAMS.entryID") . "/edit",
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
        $base->reroute("/");
    }

    public function getDelete(\Base $base) {
		if($base->get("SESSION.token") !== null) {
			$postID = $base->get("PARAMS.entryID");

	        $ch = curl_init();
	        $options = [
	            CURLOPT_URL => $base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/" . $postID . "/delete",
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
}
