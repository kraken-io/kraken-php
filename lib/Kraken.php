<?php

class Kraken
{

    protected $auth = array();

    public function __construct($key = '', $secret = '')
    {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
    }

    public function url($opts = array())
    {
        $data = json_encode(array_merge($this->auth, $opts));
        $response = self::request($data, "https://api.kraken.io/v1/url");

        return $response;
    }

    private function json_decode_array($input)
    {
        $from_json = json_decode($input, true);
        return $from_json ? $from_json : $input;
    }

    public function multi_url($urls, $opts = array())
    {
        $multi_curl = curl_multi_init();
        $requests = array();
        $responses = array();
        $options = array_merge($this->auth, $opts);
        foreach($urls as $url)
        {
            $options['url'] = $url;
            $curl = self::request(json_encode($options), "https://api.kraken.io/v1/url", true);
            curl_multi_add_handle($multi_curl, $curl);
            $requests[$url] = $curl;
        }

        $running = null;
        do curl_multi_exec($multi_curl, $running);
        while ($running);

        foreach($requests as $url => $handle)
        {
            $responses[$url] = curl_multi_getcontent($handle);
            curl_multi_remove_handle($multi_curl, $handle);
        }
        curl_multi_close($multi_curl);

        return array_map(array('self', 'json_decode_array'), $responses);
    }

    public function upload($opts = array())
    {
        if (!isset($opts['file']))
        {
            return array(
                "success" => false,
                "error" => "File parameter was not provided"
            );
        }

        if (preg_match("/\/\//i", $opts['file']))
        {
            $opts['url'] = $opts['file'];
            unset($opts['file']);
            return $this->url($opts);
        }

        if (!file_exists($opts['file']))
        {
            return array(
                "success" => false,
                "error" => "File `" . $opts['file'] . "` does not exist"
            );
        }

        if( class_exists( 'CURLFile') ) {
			$file = new CURLFile( $opts['file'] );
		} else {
			$file = '@' . $opts['file'];
		}

        unset($opts['file']);

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge(
                            $this->auth, $opts
            ))
        ));

        $response = self::request($data, "https://api.kraken.io/v1/upload");

        return $response;
    }

    public function status()
    {
        $data = array('auth' => array(
            'api_key' => $this->auth['auth']['api_key'],
            'api_secret' => $this->auth['auth']['api_secret']
        ));
        $response = self::request(json_encode($data), "https://api.kraken.io/user_status");

        return $response;
    }

    private function request($data, $url, $return_handle = false)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);

        if ($return_handle)
            return $curl;

        $response = json_decode(curl_exec($curl), true);
        $error = curl_errno($curl);

        curl_close($curl);

        if ($error > 0) {
            throw new RuntimeException(sprintf('cURL returned with the following error code: "%s"', $error));
        }

        return $response;
    }

}
