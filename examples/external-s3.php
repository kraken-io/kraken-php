<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "s3_store" => array(
        "key" => "your-amazon-access-key",
        "secret" => "your-amazon-secret-key",
        "bucket" => "destination-bucket"
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