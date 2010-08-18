=Imgur API for PHP=

==Requirements==

 - PHP 5.2 or better
 - allow_url_fopen enabled

==What Works==

 - /upload: Upload an image
   - From a file on disk (via filename)
   - From a file in memory (as a variable)
   - From a URL
 - /image: Fetch information about an image
 - /delete: Delete an image

==What Doesn't Work Yet==

 - Everything else, including galleries and all of the methods that require authentication.

==How To Use==

    include 'Imgur.php';
    $upload = new Imgur_Upload();
    $upload->name = 'The Filename Imgur Sees Uploaded.png'; // Optional.
    $upload->title = 'A Short Descriptive Title, Optional';
    $upload->caption = 'A longer bit of descriptive text.  Also optional.';
    $result = $upload->uploadImageFromDisk('foo.png');
    // Or ...
    $result = $upload->uploadImageFromString($data);
    // Or ...
    $result = $upload->uploadImageFromURL($url);
    if($result instanceof Imgur_Error)
        echo "OH NOES IT BROKE!  {$result->message}\n";
    else
        echo "It worked!  Yay!  The image is here: {$upload->link_imgur_page}\n";

    // Can we pull down the image info again?
    $img = new Imgur_Image($upload->hash);
    echo "Image successfully loaded: {$img->hash}\n";
    // Now, can we delete the image?  Because we're anonymous, only the uploader
    // actually has the delete hash, but because this is demo code...
    $img->deletehash = $upload->deletehash();
    echo "Image deleted?  " . $img->delete() . "\n";

It's worth noting that the Imgur_Upload class inherits from Imgur_Image, meaning
that you can call the delete method directly from the uploaded instance.  This is,
of course, insane.
