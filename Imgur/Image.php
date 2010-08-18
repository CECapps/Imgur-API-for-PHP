<?php
/**
 * PHP Interface to v2 of the Imgur API
 * Image container.
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

class Imgur_Image {

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
 * Constructor.  Try to automatically pull down information about a specific image.
 * This automatically discards errors, btw.  Bad form.
 * @return Imgur_Upload
 **/
    public function __construct($hash = '') {
        if(strlen($hash))
            $this->load($hash);
    }


/**
 * Do that whole fetching thing.
 * @return Imgur_Image or throws an exception on failure.
 **/
    public function load($hash) {
        $json = json_decode(Imgur::sendGET(Imgur::$api_url . '/image/' . $hash), true);
        Imgur::checkError($json);
        return $this->loadFromJSON($json['image']);
    }


/**
 * Load data from returned JSON
 * @param array $json
 * @return Imgur_Image
 **/
    public function loadFromJSON($json) {
        foreach($json['image'] as $k => $v)
            if(property_exists($this, $k))
                $this->$k = $v;
        $this->datetime = new DateTime($this->datetime);
        foreach($json['links'] as $k => $v) {
            $urlk = 'link_' . $k;
            if(property_exists($this, $urlk))
                $this->$urlk = $v;
        }
        return $this;
    }


/**
 * Nuke it from orbit.  It's the only way to be sure.
 * @return bool Or throws an exception on failure.
 **/
    public function delete() {
        if(!$this->deletehash)
            return false;
        $json = json_decode(Imgur::sendGET(
            Imgur::$api_url . '/delete/' . $this->deletehash,
            array( '_method' => 'delete' ) // Because I don't want to futz with another stream wrapper.
        ), true);
        Imgur::checkError($json);
        return $json['delete']['message'] == 'Success';
    }

}
