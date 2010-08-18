<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Upload container, representing both pending and successful uploads.
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_Upload extends Imgur_Image {

/** @var string The name of the file.*/
    public $name;
/** @var string The title of the image, in order to supply context. This shows up on the Imgur website. */
    public $title;
/** @var string The caption of the image, in order to supply even more context. This shows up on the Imgur website. */
    public $caption;
/** @var bool Successfully uploaded? */
    protected $uploaded = false;

/**
 * Constructor.  Removes the auto-fetching behavior of the Image base class.
 * @return Imgur_Upload
 **/
    public function __construct($hash = '') {}


/**
 * Import bits and pieces from the expects JSON
 * @param array $json JSON data
 * @return Imgur_Upload
 **/
    public function importUploadFromJSON($json) {
        foreach($json['upload']['image'] as $k => $v)
            if(property_exists($this, $k))
                $this->$k = $v;
        $this->datetime = new DateTime($this->datetime);
        foreach($json['upload']['links'] as $k => $v) {
            $urlk = 'link_' . $k;
            if(property_exists($this, $urlk))
                $this->$urlk = $v;
        }
        $this->uploaded = true;
        return $this;
    }


/**
 * Upload an image using a file on disk as the resource.
 * @param string $filename
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromDisk($filename) {
        if($this->uploaded)
            return new Imgur_Error(array('error'=>array('message'=>'You already uploaded an image using this object.')));
        $json = $this->uploadImage(base64_encode(file_get_contents($filename)));
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importUploadFromJSON($json);
        return new Imgur_Error($json);
    }


/**
 * Upload an image using a string containing the image data.
 * @param string $file
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromString($file) {
        if($this->uploaded)
            return new Imgur_Error(array('error'=>array('message'=>'You already uploaded an image using this object.')));
        $json = $this->uploadImage(base64_encode($file));
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importUploadFromJSON($json);
        return new Imgur_Error($json);
    }


/**
 * Upload an image using a URL to a remote image that Imgur will read.
 * @param string $url
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromURL($url) {
        if($this->uploaded)
            return new Imgur_Error(array('error'=>array('message'=>'You already uploaded an image using this object.')));
        $json = $this->uploadImage($url, 'url');
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importUploadFromJSON($json);
        return new Imgur_Error($json);
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
                'image' => $data,
                'type' => $type,
                'name' => $this->name,
                'title' => $this->title,
                'caption' => $this->caption
            )
        );
        $json = json_decode($post, true);
        return $json;
    }

}
