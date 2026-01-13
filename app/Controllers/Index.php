<?php

namespace Controllers;

class Index
{
    public function getHome(\Base $base): void
    {
        $base->set("content", "home.html");
        echo \Template::instance()->render("index.html");
    }

    public function getSearch(\Base $base): void
    {
        if (is_null($base->get("GET.q"))) {
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

        if ((int)($statusCode / 100) !== 2) {
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

        switch (array_change_key_case($base->get("search_categories"), CASE_LOWER)[strtolower($postFields["category"])]["type"]) {
            case "articles":
                $base->set("content", "search.html");
                break;
            case "gallery":
                $base->set("content", "search_gallery.html");
                break;
        }

        echo \Template::instance()->render("index.html");
    }

    public function getError(\Base $base): void
    {
        $base->set("content", "error.html");
        echo \Template::instance()->render("index.html");
    }

    public function getReport(\Base $base): void
    {
        if (empty($base->get("SESSION")))
            $base->reroute("/");


        if (is_numeric($base->get("PARAMS.ID"))) {
            $data = file_get_contents($base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/" . $base->get("PARAMS.ID"));
            $result = json_decode($data, true);
            $base->set("entry_reported", $result);
        } else {
            $data = file_get_contents($base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $base->get("PARAMS.ID"));
            $result = json_decode($data, true);
            $base->set("user_reported_name", $result["username"]);
        }

        $base->set("reasons", [
            "1 Outdated Information - the content no longer exists",
            "3 Spam or Misleading Content - site is purely keywords, gibberish, or scams",
            "5 Intellectual Property - copyright or trademark infringement",
            "6 The \"Right to be Forgotten\" - GDPR/EU Specific",
            "8 Legal Issue - defamation, court orders, or illegal goods",
            "9 Explicit or Sexual Content - pornography in non-adult results or non-consensual imagery",
            "10 Harmful or Dangerous - malware, phishing, or sites promoting violence/terrorism",
            "10 Personal Information (Doxxing) - exposes private addresses, phone numbers, or ID numbers",
        ]);

        $base->set("content", "report_form.html");
        echo \Template::instance()->render("index.html");
    }

    public function postReport(\Base $base): void
    {
        $ch = curl_init();

        $reasons = [
            "1 Outdated Information - the content no longer exists",
            "3 Spam or Misleading Content - site is purely keywords, gibberish, or scams",
            "5 Intellectual Property - copyright or trademark infringement",
            "6 The \"Right to be Forgotten\" - GDPR/EU Specific",
            "8 Legal Issue - defamation, court orders, or illegal goods",
            "9 Explicit or Sexual Content - pornography in non-adult results or non-consensual imagery",
            "10 Harmful or Dangerous - malware, phishing, or sites promoting violence/terrorism",
            "10 Personal Information (Doxxing) - exposes private addresses, phone numbers, or ID numbers",
        ];


        $postFields = [
            "reason" => $reasons[$base->get("POST.reason") ?? 0],
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
            CURLOPT_HTTPHEADER => array("Authorization: Bearer " . $base->get("SESSION.token")),
        ];

        if (is_numeric($base->get("PARAMS.ID")))
            $options[CURLOPT_URL] = $base->get("QS.ATHEJA_SERVER_URL") . "/api/search/entry/" . $base->get("PARAMS.ID") . "/report";
        else
            $options[CURLOPT_URL] = $base->get("QS.ATHEJA_SERVER_URL") . "/api/user/" . $base->get("PARAMS.ID") . "/report";

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ((int)($statusCode / 100) !== 2) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log($error);
            $base->reroute("/error");
        }

        curl_close($ch);
        $base->reroute("/");
    }
}
