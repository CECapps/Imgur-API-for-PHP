<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Error container.
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_Error {
    
    public $message;
    public $request;
    public $method;
    public $format;
    public $parameters;

    public $headers;

    public function __construct($data = array()) {
        global $http_response_header;
        $this->headers = $http_response_header;
        if(!is_array($data) || !array_key_exists('error', $data)) {
            $this->message = '**UNKNOWN ERROR**: No error chunk found.';
            return;
        }
        if(!is_array($data['error']) || !count($data['error'])) {
            $this->message = '**UNKNOWN ERROR**: Error chunk was empty.';
            return;
        }
        foreach($data['error'] as $k => $v)
            if(property_exists($this, $k))
                $this->$k = $v;
    }

}
