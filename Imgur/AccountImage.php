<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Image container, for an Account Image
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_AccountImage extends Imgur_Image {

    protected function getURL() {
        return Imgur::$api_url . '/account/images';
    }

}
