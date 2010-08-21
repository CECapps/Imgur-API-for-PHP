<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface: PEAR's HTTP_Request2 *VIA* PEAR's HTTP_OAuth
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_HTTPAdapter_OAuth_HTTPOAuthConsumer implements Imgur_HTTPAdapter {

    /** @var HTTP_OAuth_Consumer_Request */
    protected $instance;

    public function __construct() {}

/**
 * Create a new instance of the adapter by wrapping an existing object.
 * @param HTTP_OAuth_Consumer $object
 * @return Imgur_HTTPAdapter_OAuth_HTTPOAuthConsumer
 **/
    public static function createByWrapping($object) {
        $foo = new Imgur_HTTPAdapter_OAuth_HTTPOAuthConsumer();
        $consumer_request = clone $object->getOAuthConsumerRequest();
    // Oh, what a mess.
        $consumer_request->setSecrets($object->getSecrets());
        $consumer_request->setParameters(array(
            'oauth_consumer_key' => $object->getKey(),
            'oauth_signature_method' => $object->getSignatureMethod(),
            'oauth_token' => $object->getToken()
        ));
        $foo->wrap($consumer_request);
        return $foo;
    }

/**
 * Take a different copy of our desired object, post-creation.
 * @param HTTP_OAuth_Consumer_Request $object
 **/
    public function wrap($object) {
        if($object instanceof HTTP_OAuth_Consumer)
            $this->instance = $object->getOAuthConsumerRequest();
        elseif($object instanceof HTTP_OAuth_Consumer_Request)
            $this->instance = $object;
        else
            throw new Imgur_Exception("HTTPOAuthConsumer::wrap was not passed an object that could be worked with.");
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
        $this->instance->setHeader('User-Agent', sprintf(Imgur::$user_agent, Imgur::$key));
        $this->instance->setMethod('GET');
        $this->instance->setUrl($url);
        try {
            /** @var HTTP_Request2_Response */
            $response = $this->instance->send();
            return $response->getBody();
        } catch(HTTP_Request2_Exception $e) {
            throw new Imgur_Exception("HTTP Request Failure", null, $e);
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
    // Send the actual request.
        $this->instance->setHeader('User-Agent', sprintf(Imgur::$user_agent, Imgur::$key));
        $this->instance->setMethod('POST');
        $this->instance->setUrl($url);
        foreach($data as $k => $v)
            $this->instance->addPostParameter($k, $v);
        try {
            /** @var HTTP_Request2_Response */
            $response = $this->instance->send();
            return $response->getBody();
        } catch(HTTP_Request2_Exception $e) {
            throw new Imgur_Exception("HTTP Request Failure", null, $e);
        } catch(Exception $e) {
            throw new Imgur_Exception("Unknown Failure during HTTP Request", null, $e);
        }
    }

}
