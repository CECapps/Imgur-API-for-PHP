<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface: PEAR's HTTP_Request2 *VIA* PEAR's HTTP_OAuth
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_HTTPAdapter_OAuth_PECLOAuth implements Imgur_HTTPAdapter {

    /** @var OAuth */
    protected $instance;

    public function __construct() {}

/**
 * Create a new instance of the adapter by wrapping an existing object.
 * @param HTTP_OAuth_Consumer $object
 * @return Imgur_HTTPAdapter_OAuth_HTTPOAuthConsumer
 **/
    public static function createByWrapping($object) {
        $foo = new Imgur_HTTPAdapter_OAuth_PECLOAuth();
        $foo->wrap($object);
        return $foo;
    }

/**
 * Take a different copy of our desired object, post-creation.
 * @param HTTP_OAuth_Consumer_Request $object
 **/
    public function wrap($object) {
        if($object instanceof OAuth)
            $this->instance = $object;
        else
            throw new Imgur_Exception("PECLOAuth::wrap was not passed an object that could be worked with.");
    }

/**
 * Return the HTTP request object that we're wrapping
 * @return HTTP_OAuth_Consumer_Request
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
    // Let's splice in our data with any query string that might be present.
        $kaboom = parse_url($url);
        $qs = array();
        if(array_key_exists('query', $kaboom))
            parse_str($kaboom['query'], $qs);
        $kaboom['query'] = http_build_query($qs + $data);
        $url = $kaboom['scheme'] . '://' . $kaboom['host'] . $kaboom['path'] . '?' . $kaboom['query'];
    // And finally, send the actual request.
        try {
            $this->instance->fetch(
                $url,
                null,
                OAUTH_HTTP_METHOD_GET,
                array(
                    'User-Agent' => sprintf(Imgur::$user_agent, Imgur::$key)
                )
            );
        } catch(OAuthException $e) {
            throw new Imgur_Exception("Could not successfully do a sendGET: " . $e->getMessage(), null, $e);
        }
        return $this->instance->getLastResponse();
    }

/**
 * Send a POST request to the specified URL with the specified payload.
 * @param string $url
 * @param string $data
 * @return string Remote data
 **/
    public function sendPOST($url, $data = array()) {
        $data['_fake_status']   = '200';
    // Send the actual request.
        try {
            $this->instance->fetch(
                $url,
                $data,
                OAUTH_HTTP_METHOD_POST,
                array(
                    'User-Agent' => sprintf(Imgur::$user_agent, Imgur::$key)
                )
            );
        } catch(OAuthException $e) {
            throw new Imgur_Exception("Could not successfully do a sendPOST: " . $e->getMessage(), null, $e);
        }
        return $this->instance->getLastResponse();
    }

}
