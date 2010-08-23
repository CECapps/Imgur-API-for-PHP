<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Image List
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

abstract class Imgur_ImageList extends ArrayObject {

    public function load() {
        $data = Imgur::sendGET($this->getURL());
        $json = json_decode($data, true);
        Imgur::checkError($json);
        $class = $this->getImageClass();
        foreach($json['images'] as $image) {
            $img = new $class();
            $img->loadFromJSON($image);
            $this[ $img->hash ] = $img;
        }
    }

    abstract protected function getURL();
    abstract protected function getImageClass();
    
}
