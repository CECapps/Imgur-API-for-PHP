<?php
/**
 * PHP Interface to v2 of the Imgur API
 * HTTP Adapter Demo
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

    include '../Imgur.php';
    Imgur::registerSPLAutoloader();
    
    echo "Please enter your anonymous access key: ";
    Imgur::$key = trim(fgets(STDIN));

// Comment out these two lines to use the PHPStream adapter, which is the default.
    include_once 'HTTP/Request2.php';
    Imgur::$http_adapter_class = 'Imgur_HTTPAdapter_PEARHTTPRequest2';
// And uncomment these two lines to use Zend_Http_Client instead.
    #include_once 'Zend/Loader/Autoloader.php';
    #$zal = Zend_Loader_Autoloader::getInstance();
    #Imgur::$http_adapter_class = 'Imgur_HTTPAdapter_ZendHttpClient';

    $u = new Imgur_Upload();
    $u->name = 'spacer.gif';
    $u->title = 'A 1x1 transparent GIF file.';
    $u->caption = 'For the entertainment value.';

    try {
        $i = $u->uploadImageFromDisk('./spacer.gif');
    } catch(Imgur_Exception $e) {
        echo "OH NOES!  ", $e->getMessage(), "\n";
        if(method_exists($e, 'getPrevious')) {
            $p = $e->getPrevious();
            if($p && $p instanceof Exception)
                echo "\tPrevious Exception: ", $p->getMessage(), "\n";
        }
        exit;
    }

    printf(
        "Uploaded image %s, %dx%d, %d bytes at %s; Page URL: %s, Delete Hash %s\n",
        $i->hash,
        $i->width,
        $i->height,
        $i->size,
        $i->datetime->format('Y-m-d H:i:s'),
        $i->link_imgur_page,
        $i->deletehash
    );

    if($i->delete()) {
        echo "Image successfully deleted.\n";
    } else {
        echo "Image could not be deleted!  Delete URL should be: {$i->link_delete_page}\n";
    }
