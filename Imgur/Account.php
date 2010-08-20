<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Authorized User Account
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_Account {
    
    /** @var string */
    public $url;
    /** @var bool */
    public $is_pro;
    /** @var string */
    public $default_album_privacy;
    /** @var bool */
    public $public_images;

    public function __construct() {
        if(Imgur::$has_oauth)
            $this->load();
    }


/**
 * Load the current OAuth user's account.
 **/
    public function load() {
        if(!Imgur::$has_oauth)
            throw new Imgur_Exception("Can't load an Account without setting an authorized OAuth adapter.");
        $json = Imgur::sendGET(Imgur::$api_url . '/account');
        Imgur::checkError($json);
        if(array_key_exists('account', $json))
            foreach($json['account'] as $k => $v)
                if(property_exists($this, $k))
                    $this->$k = $v;
    }

}
