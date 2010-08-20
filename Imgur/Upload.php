<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Upload container, representing both pending and successful uploads.
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_Upload {

/** @var string The name of the file.*/
    public $name;
/** @var string The title of the image, in order to supply context. This shows up on the Imgur website. */
    public $title;
/** @var string The caption of the image, in order to supply even more context. This shows up on the Imgur website. */
    public $caption;

    public function __construct($name = '', $title = '', $caption = '') {
        if(isset($name))
            $this->name = $name;
        if(isset($title))
            $this->title = $title;
        if(isset($caption))
            $this->caption = $caption;
    }


/**
 * Upload an image using a file on disk as the resource.
 * @param string $filename
 * @return Imgur_Image Or throws an exception on failure.
 **/
    public function uploadImageFromDisk($filename) {
        $json = $this->uploadImage(base64_encode(file_get_contents($filename)));
        Imgur::checkError($json);
        $img = new Imgur_Image();
        return $img->loadFromJSON($json['upload']);
    }


/**
 * Upload an image using a string containing the image data.
 * @param string $file
 * @return Imgur_Image Or throws an exception on failure.
 **/
    public function uploadImageFromString($file) {
        $json = $this->uploadImage(base64_encode($file));
        Imgur::checkError($json);
        $img = new Imgur_Image();
        return $img->loadFromJSON($json['upload']);
    }


/**
 * Upload an image using a URL to a remote image that Imgur will read.
 * @param string $url
 * @return Imgur_Image Or throws an exception on failure.
 **/
    public function uploadImageFromURL($url) {
        $json = $this->uploadImage($url, 'url');
        Imgur::checkError($json);
        $img = new Imgur_Image();
        return $img->loadFromJSON($json['upload']);
    }


/**
 * Perform the requested upload.
 * @param string $file
 * @param string $type
 * @return array JSON
 **/
    protected function uploadImage($data, $type = 'base64') {
        $post = Imgur::sendPOST(
            Imgur::$api_url . '/upload',
            array(
                'image'     => $data,
                'type'      => $type,
                'name'      => $this->name,
                'title'     => $this->title,
                'caption'   => $this->caption
            )
        );
        $json = json_decode($post, true);
        return $json;
    }

}
