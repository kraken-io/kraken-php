<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "cf_store" => array(
        "user" => "your-rackspace-username",
        "key" => "your-rackspace-api-key",
        "container" => "destination-container"
    )
);

$data = $kraken->upload($params);

if ($data["success"]) {
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} else {
    echo "Fail. Error message: " . $data["error"];
}

?>