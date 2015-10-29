<?php

require_once("Kraken.php");

$kraken = new Kraken("your-api-key", "your-api-secret");

$params = array(
    "file" => "/path/to/image/file.jpg",
    "wait" => true,
    "azure_store" => array(
        "account" => "your-azure-account",
        "key" => "your-azure-storage-access-key",
        "container" => "destination-container"
    )
);

$data = $kraken->upload($params);