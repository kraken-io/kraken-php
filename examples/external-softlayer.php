<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");


// complete SoftLayer object storage example using the 'url' option
$params = array(
    "url" => "http://awesome-website.com/images/header.jpg",
    "wait" => true,
    "sl_store" => array(
        "user" => "your-softlayer-account",
        "key" => "your-softlayer-key",
        "container" => "destination-container",
        "region" => "your-container-location",
        "cdn_url" => true,
        "path" => "images/layout/header.jpg"
    )
);

$data = $kraken->upload($params);

if (!empty($data["success"])) {

    // optimization succeeded
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} elseif (isset($data["message"])) {

    // something went wrong with the optimization
    echo "Optimization failed. Error message from Kraken.io: " . $data["message"];
} else {

    // something went wrong with the request
    echo "cURL request failed. Error message: " . $data["error"];
}