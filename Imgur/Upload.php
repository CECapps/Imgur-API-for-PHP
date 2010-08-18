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
/** @var string The hash of the image, as used in URLs.  Provided by Imgur */
    public $hash;
/** @var string The delete hash, used by delete an image.  Provided by Imgur */
    public $deletehash;
/** @var DateTime Creation date & time.  Provided by Imgur */
    public $datetime;
/** @var string The MIME type of the image.  Provided by Imgur */
    public $type;
/** @var bool Is this an animated image?  Provided by Imgur. */
    public $animated;
/** @var int Width of the image, in pixels.  Provided by Imgur. */
    public $width;
/** @var int Height of the image, in pixels.  Provided by Imgur. */
    public $height;
/** @var int Size of the image, in bytes.  Provided by Imgur. */
    public $size;
/** @var int Number of image views.  Provided by Imgur. */
    public $views;
/** @var int Bandwidth use of this image, in bytes?  Provided by Imgur. */
    public $bandwidth;
/** @var string URL to the image on Imgur. */
    public $link_original;
/** @var string URL to the page for the image on Imgur. */
    public $link_imgur_page;
/** @var string URL to the delete page for this image on Imgur. */
    public $link_delete_page;
/** @var string URL to the small square thumbnail for this image on Imgur. */
    public $link_small_square;
/** @var string URL to the large thumbnail for this image on Imgur. */
    public $link_large_thumbnail;


/**
 * Import bits and pieces from the expects JSON
 * @param array $json JSON data
 * @return Imgur_Upload
 **/
    public function importFromJSON($json) {
        foreach($json['upload']['image'] as $k => $v)
            if(property_exists($this, $k))
                $this->$k = $v;
        $this->datetime = new DateTime($this->datetime);
        foreach($json['upload']['links'] as $k => $v) {
            $urlk = 'link_' . $k;
            if(property_exists($this, $urlk))
                $this->$urlk = $v;
        }
        return $this;
    }


/**
 * Upload an image using a file on disk as the resource.
 * @param string $filename
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromDisk($filename) {
        $json = $this->uploadImage(base64_encode(file_get_contents($filename)));
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importFromJSON($json);
        return new Imgur_Error($json);
    }


/**
 * Upload an image using a string containing the image data.
 * @param string $file
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromString($file) {
        $json = $this->uploadImage(base64_encode($file));
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importFromJSON($json);
        return new Imgur_Error($json);
    }


/**
 * Upload an image using a URL to a remote image that Imgur will read.
 * @param string $url
 * @return mixed Imgur_Error object, or a status integer
 **/
    public function uploadImageFromURL($url) {
        $json = $this->uploadImage($url, 'url');
        if(is_array($json) && array_key_exists('upload', $json))
            return $this->importFromJSON($json);
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
