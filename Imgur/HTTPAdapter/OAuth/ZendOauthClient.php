<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Interface: Zend_Oauth_Client (which isa Zend_Http_Client)
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_HTTPAdapter_OAuth_ZendOauthClient extends Imgur_HTTPAdapter_ZendHttpClient implements Imgur_HTTPAdapter {

/**
 * Create a new instance of the adapter by wrapping an existing object.
 * Because we'll be working with a real Zend_Http_Client, we can properly
 * inherit from the normal ZendHttpClient adapter.
 * @param Zend_Oauth_Client $object
 * @return Imgur_HTTPAdapter_OAuth_ZendOauthClient
 **/
    public static function createByWrapping($object) {
        $foo = new Imgur_HTTPAdapter_OAuth_ZendOauthClient();
        $foo->wrap($object);
        return $foo;
    }

}
