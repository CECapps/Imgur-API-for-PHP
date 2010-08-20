<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

interface Imgur_HTTPAdapter {


/**
 * Create a new instance of the adapter by wrapping an existing object.
 * @return Imgur_HTTPAdapter
 **/
    public static function createByWrapping($object);

/**
 * Take a different copy of our desired object, post-creation.
 * @param object $object
 **/
    public function wrap($object);

/**
 * Return the HTTP request object that we're wrapping
 * @return object
 **/
    public function &unwrap();

/**
 * Send a GET request to the specified URL with the specified query string.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendGET($url, $data = array());

/**
 * Send a POST request to the specified URL with the specified payload.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendPOST($url, $data = array());

}
