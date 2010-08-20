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
    
    public static $user_agent = 'ImgurAPI-PHP/1.2 (http://github.com/McGlockenshire/Imgur-API-for-PHP; key=%s)';

    public static $api_url = 'http://api.imgur.com/2';

    public static $http_adapter_class = 'Imgur_HTTPAdapter_PHPStream';

    /** @var Imgur_HTTPAdapter */
    protected static $http_adapter;

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
        # @TODO: Deal with HTTP errors here, if we can?
        return true;
    }


/**
 * Create/retrieve a copy of our favored HTTP adapter
 * @return Imgur_HTTPAdapter
 **/
    public static function getHTTPAdapter($create = true) {
        if(!isset(self::$http_adapter) && $create)
            self::$http_adapter = new self::$http_adapter_class();
        return self::$http_adapter;
    }


/**
 * Set our favored HTTP adapter
 * @param Imgur_HTTPAdapter $adapter
 **/
    public static function setHTTPAdapter(Imgur_HTTPAdapter $adapter) {
        self::$http_adapter = $adapter;
    }


/**
 * Create a POST to the specified URL.
 * @param string $url
 * @param array $data POST data
 * @return string Returned data
 **/
    public static function sendPOST($url, $data = array()) {
        $data['key']        = Imgur::$key;
        $data['_format']    = 'json';
        $http = self::getHTTPAdapter();
        $res = $http->sendPOST($url, $data);
        #print_r($res);
        return $res;
    }


/**
 * Create a URL to the specified URL.
 * @param string $url
 * @param array $data POST data
 * @return string Returned data
 **/
    public static function sendGET($url, $data = array()) {
        $data['key']        = Imgur::$key;
        $data['_format']    = 'json';
        $http = self::getHTTPAdapter();
        $res = $http->sendGET($url, $data);
        #print_r($res);
        return $res;
    }

}
