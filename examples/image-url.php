<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "url" => "http://url-to-image/file.jpg",
    "wait" => true
);

$data = $kraken->url($params);

if ($data["success"]) {
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} else {
    echo "Fail. Error message: " . $data["error"];
}

?>