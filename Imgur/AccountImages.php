<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Image List for an Account
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_AccountImages extends Imgur_ImageList {

    protected function getURL() {
        return Imgur::$api_url . '/account/images';
    }

    protected function getImageClass() {
        return 'Imgur_AccountImage';
    }

}
