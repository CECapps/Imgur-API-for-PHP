<?php
/**
 * PHP Interface to v2 of the Imgur API
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur {

    public static $key;
    
    public static $user_agent = 'ImgurAPI-PHP/1.0 (http://github.com/McGlockenshire/Imgur-API-for-PHP; key=%s)';

    public static $api_url = 'http://api.imgur.com/2';

/**
 * No constructor, this is a static class.
 **/
    private function __construct() {}


/**
 * Add an SPL Autoloader, just in case the one currently
 * being used by our calling code doesn't follow the expected rules.
 **/
    public static function registerSPLAutoloader() {
        spl_autoload_register(array('Imgur', 'autoloader'));
    }


/**
 * The actual SPL Autoload callback, registered by registerSPLAutoloader.
 * @param string $class Class name
 * @return bool
 **/
    public static function autoloader($class) {
        if(strpos($class, 'Imgur_') !== 0)
            return false;
        $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if(!file_exists($filename))
            return false;
        require_once $filename;
    }


/**
 * Check a JSON array for invalidness.
 * @param array $json
 * @return bool
 **/
    public static function checkError($json) {
        if(!is_array($json))
            throw new Imgur_Exception("No data was returned.");
        if(array_key_exists('error', $json))
            throw new Imgur_Exception($json['error']['message']);
        return true;
    }


/**
 * Create a POST to the specified URL.
 * @param string $url
 * @param array $data POST data
 * @return string Returned data
 **/
    public static function sendPOST($url, $data = array()) {
        $data['key']            = Imgur::$key;
        $data['_fake_statuses'] = 'true';
        $data['_format']        = 'json';
        $data = http_build_query($data);
        $stream = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'user_agent' => sprintf(Imgur::$user_agent, Imgur::$key),
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data,
                'ignore_errors' => true
            )
        ));
        return file_get_contents($url, false, $stream);
    }


/**
 * Create a URL to the specified URL.
 * @param string $url
 * @param array $data POST data
 * @return string Returned data
 **/
    public static function sendGET($url, $data = array()) {
        $data['key']            = Imgur::$key;
        $data['_fake_statuses'] = 'true';
        $data['_format']        = 'json';
    // Let's splice in our data with any query string that might be present.
        $kaboom = parse_url($url);
        $qs = array();
        if(array_key_exists('query', $kaboom))
            parse_str($kaboom['query'], $qs);
        $kaboom['query'] = http_build_query($qs + $data);
        $url = $kaboom['scheme'] . '://' . $kaboom['host'] . $kaboom['path'] . '?' . $kaboom['query'];
    // Now, where were we?
        $stream = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'user_agent' => sprintf(Imgur::$user_agent, Imgur::$key),
                'ignore_errors' => true
            )
        ));
        return file_get_contents($url, false, $stream);
    }

}
