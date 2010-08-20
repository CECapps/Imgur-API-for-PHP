<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface: Zend_Http_Client
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_HTTPAdapter_ZendHttpClient implements Imgur_HTTPAdapter {


    /** @var Zend_Http_Client */
    protected $instance;

    public function __construct() {
        $this->instance = new Zend_Http_Client();
    }

/**
 * Create a new instance of the adapter by wrapping an existing object.
 * @param Zend_Http_Client $object
 * @return Imgur_HTTPAdapter_ZendHttpClient
 **/
    public static function createByWrapping($object) {
        $foo = new Imgur_HTTPAdapter_ZendHttpClient();
        $foo->wrap($object);
        return $foo;
    }

/**
 * Take a different copy of our desired object, post-creation.
 * @param Zend_Http_Client $object
 **/
    public function wrap($object) {
        if($object instanceof Zend_Http_Client)
            $this->instance = $object;
        else
            throw new Imgur_Exception("ZendHttpClient::wrap was not passed an object that could be worked with.");
    }

/**
 * Return the HTTP request object that we're wrapping
 * @return Zend_Http_Client
 **/
    public function &unwrap() {
        return $this->instance;
    }

/**
 * Send a GET request to the specified URL with the specified query string.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendGET($url, $data = array()) {
        $data['_fake_status']   = '200';
    // Zend makes it easier than the others...
        $this->instance->setConfig(array(
            'useragent' => sprintf(Imgur::$user_agent, Imgur::$key)
        ));
        $this->instance->setMethod(Zend_Http_Client::GET);
        $this->instance->setUri($url);
        $this->instance->setParameterGet($data);
        try {
            /** @var Zend_Http_Response */
            $response = $this->instance->request();
            return $response->getBody();
        } catch(Exception $e) {
            throw new Imgur_Exception("Unknown Failure during HTTP Request", null, $e);
        }
    }

/**
 * Send a POST request to the specified URL with the specified payload.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendPOST($url, $data = array()) {
        $data['_fake_status']   = '200';
    // Zend makes it easier than the others...
        $this->instance->setConfig(array(
            'useragent' => sprintf(Imgur::$user_agent, Imgur::$key)
        ));
        $this->instance->setMethod(Zend_Http_Client::POST);
        $this->instance->setUri($url);
        $this->instance->setParameterPost($data);
        try {
            /** @var Zend_Http_Response */
            $response = $this->instance->request();
            return $response->getBody();
        } catch(Exception $e) {
            throw new Imgur_Exception("Unknown Failure during HTTP Request", null, $e);
        }
    }

}
