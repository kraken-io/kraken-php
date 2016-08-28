<?php

class Kraken {
    protected $auth = array();
    private $timeout;
    private $proxyParams;

    public function __construct($key = '', $secret = '', $timeout = 30, $proxyParams = array()) {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
        $this->timeout = $timeout;
        $this->proxyParams = $proxyParams;
    }

    public function url($opts = array()) {
        $data = json_encode(array_merge($this->auth, $opts));
        $response = self::request($data, 'https://api.kraken.io/v1/url', 'url');

        return $response;
    }

    public function upload($opts = array()) {
        if (!isset($opts['file'])) {
            return array(
                "success" => false,
                "error" => "File parameter was not provided"
            );
        }

        if (!file_exists($opts['file'])) {
            return array(
                "success" => false,
                "error" => 'File `' . $opts['file'] . '` does not exist'
            );
        }

        if (class_exists('CURLFile')) {
			$file = new CURLFile($opts['file']);
		} else {
			$file = '@' . $opts['file'];
		}

        unset($opts['file']);

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge($this->auth, $opts))
        ));

        $response = self::request($data, 'https://api.kraken.io/v1/upload', 'upload');

        return $response;
    }

    public function status() {
        $data = array('auth' => array(
            'api_key' => $this->auth['auth']['api_key'],
            'api_secret' => $this->auth['auth']['api_secret']
        ));

        $response = self::request(json_encode($data), 'https://api.kraken.io/user_status', 'url');

        return $response;
    }

    private function request($data, $url, $type) {
        $curl = curl_init();

        if ($type === 'url') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        // Force continue-100 from server
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.85 Safari/537.36");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);
        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        if (isset($this->proxyParams['proxy'])) {
            curl_setopt($curl, CURLOPT_PROXY, $this->proxyParams['proxy']);
        }

        $response = json_decode(curl_exec($curl), true);

        if ($response === null) {
            $response = array (
                "success" => false,
                "error" => 'cURL Error: ' . curl_error($curl)
            );
        }

        curl_close($curl);

        return $response;
    }
}
