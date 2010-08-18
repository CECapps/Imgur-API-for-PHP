# Imgur API for PHP

Licensed under LGPL3.

## Requirements

 - PHP 5.2 or better
 - allow_url_fopen enabled

## What Works

 - /upload: Upload an image
   - From a file on disk (via filename)
   - From a file in memory (as a variable)
   - From a URL
 - /image: Fetch information about an image
 - /delete: Delete an image

## What Doesn't Work Yet

 - Everything else, including galleries and all of the methods that require authentication.

## TODO
 
 - More completeness, in order of priority and likelyhood to get completed:
   - /account/images
     - Seems to accept a POST.  Does this work like /upload?  Need to check.
   - /account/images/:HASH
     - Does this work like /image?  Sure seems like it.  Accepts a DELETE method.
   - /account/images_count
   - /account
   - /account/albums
   - /account/albums/:ID
   - /account/albums_count
   - /account/albums_order
   - /account/albums_order/:ID
   - /gallery
   - /stats
 
## Quick HOWTO

    // It includes a SPL autoloader, just in case your project doesn't already
    // follow the normal underscores-as-directory-separators pattern.
        include 'Imgur.php';
        Imgur::registerSPLAutoloader();
    // Get your API key from <http://imgur.com/register/api_anon>
        Imgur::$key = '...';
    // Let's upload a file.
        $upload = new Imgur_Upload();
        $upload->name = 'The Filename Imgur Sees Uploaded.png'; // Optional.
        $upload->title = 'A Short Descriptive Title, Optional';
        $upload->caption = 'A longer bit of descriptive text.  Also optional.';
        try {
            /** @var Imgur_Image */
            $image = $upload->uploadImageFromDisk('foo.png');
        // Or ...
            $image = $upload->uploadImageFromString($data);
        // Or ...
            $image = $upload->uploadImageFromURL($url);
        } catch(Imgur_Exception $e) {
            echo "It totally busted: ", $e->getMessage();
            exit;
        }
        echo "It worked!  Yay!  The image is here: {$$image->link_imgur_page}\n";

    // Can we pull down the image info again?
        $same_image = new Imgur_Image($image->hash);
        echo "Image successfully loaded: {$same->hash} == {$image->hash}\n";
    // Now, can we delete the image?  Because we're anonymous, only the image that
    // was returned by the uploader has the delete hash/
        echo "Image deleted?  " . var_export($image->delete(), true) . "\n";
