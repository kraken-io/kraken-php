<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "url" => "http://url-to-image/file.jpg",
    "wait" => true
);

$data = $kraken->url($params);

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