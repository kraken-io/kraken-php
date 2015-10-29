<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "resize" => array(
        "width" => 100,
        "height" => 75,
        "strategy" => "crop"
    )
);

$data = $kraken->upload($params);

if ($data["success"]) {
    echo "Success. Optimized image URL: " . $data["kraked_url"];
} else {
    echo "Fail. Error message: " . $data["message"];
}

?>