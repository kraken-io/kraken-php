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

if ($data["success"]) {
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} else {
    echo "Fail. Error message: " . $data["error"];
}

?>