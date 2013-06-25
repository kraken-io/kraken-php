<?php

class Kraken {
    protected $auth = array();

    public function __construct($key = '', $secret = '') {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
    }

    public function url($opts = array()) {
        $data = json_encode(array_merge($this->auth, $opts));
        $response = self::request($data, "https://api.kraken.io/v1/url");

        return $response;
    }

    public function upload($opts = array()) {
        if (!file_exists($opts['file'])) {
            return array(
                "success" => false,
                "error" => "File `" . $opts['file'] . "` does not exist"
            );
        }

        $file = '@' . $opts['file'];

        unset($opts['file']);

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge(
                $this->auth,
                $opts
            ))
        ));

        $response = self::request($data, "https://api.kraken.io/v1/upload");

        return $response;
    }

    private function request($data, $url) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }
}