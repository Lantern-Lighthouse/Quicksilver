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

    public function postEnter(\Base $base): void {
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
}
