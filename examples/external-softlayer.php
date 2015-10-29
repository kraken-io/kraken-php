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

if ($data["success"]) {
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} else {
    echo "Fail. Error message: " . $data["message"];
}

?>