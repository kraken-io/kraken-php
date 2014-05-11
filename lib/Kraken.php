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

        $file = '@' . $opts['file'];

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
        $data = json_encode($this->auth);
        
        $response = self::request($data, "https://api.kraken.io/user_status");

        return $response;
    }

    private function get_subaccounts() {
        $data = json_encode($this->auth);
        
        $response = self::request($data, "https://api.kraken.io/v1/subaccounts", "GET");
        
        return $response;
    }
    
    private function get_subaccount($sub_account_key = false) {
        if ($sub_account_key === false) {
            return false;
        }
        
        $data = json_encode($this->auth);
        
        $response = self::request($data, "https://api.kraken.io/v1/subaccounts/" . $sub_account_key, "GET");
        
        return $response;
    }
    
    private function create_subaccount($sub_account_name = "") {
        if ($sub_account_key == "") {
            return false;
        }
        
        $data = json_encode(array_merge($this->auth, array("name" => $sub_account)));
        
        $response = self::request($data, "https://api.kraken.io/v1/subaccounts");
        
        return $response;
    }
    
    private function delete_subaccount($sub_account_key = false) {
        if ($sub_account_key === false) {
            return false;
        }
        
        $data = json_encode($this->auth);
        
        $response = self::request($data, "https://api.kraken.io/v1/subaccounts", "DELETE");
        
        return $response;
    }

    private function request($data, $url, $post_get_delete = "POST")
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $post_get_delete);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);

        $response = json_decode(curl_exec($curl), true);
        $error = curl_errno($curl);

        curl_close($curl);

        if ($error > 0) {
            throw new RuntimeException(sprintf('cURL returned with the following error code: "%s"', $error));
        }

        return $response;
    }

}
