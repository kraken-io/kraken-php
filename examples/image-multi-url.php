<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$urls = array(
    "http://url-to-images/file1.jpg",
    "http://url-to-images/file2.jpg",
    "http://url-to-images/file3.jpg"
);

$params = array(
    "wait" => true
);

$responses = $kraken->multi_url($urls, $params);

foreach($responses as $url => $data)
{
    if ($response["success"]) {
        echo "Success for image " . $url . ". Optimized image URL: ". $data["kraked_url"];
    } else {
        echo "Failed for image " . $url . ". Error message: " . $data["error"];
    }
}

?>