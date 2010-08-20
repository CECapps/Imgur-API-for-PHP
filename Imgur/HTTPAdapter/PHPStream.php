<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface: PHP Streams
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_HTTPAdapter_PHPStream implements Imgur_HTTPAdapter {

    protected $options = array();

    public function __construct($context_options = array()) {
        $default_options = array(
            'user_agent'    => sprintf(Imgur::$user_agent, Imgur::$key),
            'ignore_errors' => true
        );
        $this->options = array_merge($default_options, $context_options);
    }


/**
 * Create a new instance of the adapter by wrapping an existing object.
 * @return Imgur_HTTPAdapter_PHPStream
 **/
    public static function createByWrapping($object) { throw new Imgur_Exception("PHPStream can not be wrapped, there is nothing to wrap."); }

/**
 * Take a different copy of our desired object, post-creation.
 * @param object $object
 **/
    public function wrap($object) { throw new Imgur_Exception("PHPStream can not be wrapped, there is nothing to wrap."); }

/**
 * Return the HTTP request object that we're wrapping
 * @return object
 **/
    public function &unwrap() { throw new Imgur_Exception("PHPStream can not be unwrapped, there is nothing to unwrap."); }


/**
 * Send a GET request to the specified URL with the specified query string.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendGET($url, $data = array()) {
        $data['_fake_status']   = 200;
    // Let's splice in our data with any query string that might be present.
        $kaboom = parse_url($url);
        $qs = array();
        if(array_key_exists('query', $kaboom))
            parse_str($kaboom['query'], $qs);
        $kaboom['query'] = http_build_query($qs + $data);
        $url = $kaboom['scheme'] . '://' . $kaboom['host'] . $kaboom['path'] . '?' . $kaboom['query'];
        $options = $this->options;
        $options['method'] = 'GET';
    // Now, where were we?
        $stream = stream_context_create(array('http' => $options));
        return file_get_contents($url, false, $stream);
    }


/**
 * Send a POST request to the specified URL with the specified payload.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendPOST($url, $data = array()) {
        $data['_fake_status']   = 200;
        #print_r($data);
        $options = $this->options;
        $options['method'] = 'POST';
        $options['header'] = 'Content-type: application/x-www-form-urlencoded';
        $options['content'] = http_build_query($data);
        $stream = stream_context_create(array( 'http' => $options ));
        return file_get_contents($url, false, $stream);
    }

}
