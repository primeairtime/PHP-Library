<?php
define('MAIN_ROOT', realpath(dirname(__file__)) . '/');
require_once MAIN_ROOT . "Logger.php";

class http
{


    public static function config($key, $default = null)
    {

        static $config;


        if ($config === null) {
            $config = include MAIN_ROOT . 'config.php';

        }

        return (isset($config[$key])) ? $config[$key] : $default;
    }


    public function hyphenate($str)
    {
        return implode("-", str_split($str, 5));
    }


    function revalidate_key()
    {
        if (self::config('use_token')) {
            return self::config('token');
        }

        $access = self::mobifin_post("reauth", "", false);

        if (isset($access['token'])) {
            file_put_contents("key.txt", json_encode($access));
            return $access['token'];
        } else {
            return "";
        }

    }

    function auth_key()
    {


        if (self::config('use_token')) {
            return self::config('token');
        }

        $data_string = '{
 "username" : "' . self::config('username') . '",
 "password": "' . self::config('password') . '"
}';


        if (file_exists("key.txt")) {

            $get_content = file_get_contents("key.txt");
            $access = json_decode($get_content, true);
            $date = date("Y-m-d H:i:s", strtotime("-5 hours", strtotime($access['expires'])));

            if (strtotime(date("Y-d-m H:i:s")) >= strtotime($date)) {
                $access = self::mobifin_post("auth", $data_string, true);
                if (isset($access['token'])) {
                    file_put_contents("key.txt", json_encode($access));
                    return $access['token'];
                } else {
                    return "";
                }
            } else {
                return $access['token'];
            }


        } else {

            $access = self::mobifin_post("auth", $data_string, true);

            if (isset($access['token'])) {
                file_put_contents("key.txt", json_encode($access));
                return $access['token'];
            } else {
                return "";
            }

        }
    }

    function mobifin_post($path, $data_string, $post = true)
    {

        $url = (self::config('enviroment') == "live") ?
            "https://clients.primeairtime.com/api/" : "https://recharge4.com/demo/";


        $token = "";
        if ($path != "auth") {
            $token = self::auth_key();
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "accept: application/json, application/*+json",
            "accept-encoding: gzip,deflate",
            "Authorization: Bearer " . $token,
            "cache-control: no-cache",
            "Connection: Keep-Alive",
            "Keep-Alive: 300",
            "content-type: application/json",
            ));

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($result, true);


        if (in_array($httpcode, array("404", "503"))) {

            header('HTTP/1.0 404" "Not found');
            return array("error" => array("status" => 404));
        }

        return $response;
    }

}




?>