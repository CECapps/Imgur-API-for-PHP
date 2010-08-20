# Imgur API for PHP

Licensed under LGPL3.

## Requirements

 - PHP 5.2.10 or better
 - allow_url_fopen enabled

### Optional Requirements

 - HTTP_Request2 (PEAR, tested with 0.5.2)
 - Zend_Http_Client (Zend Framework, tested with 1.10)

## Recent Changes

 - Added HTTP adapters in preperation for OAuth support.
 - Added "demos" directory with small demo applications.
   - "httpadapters" demo contains a quick overview of how to upload a file and
     then fetch properties on it before deleting it, while also giving a very
     quick overview on how to use the HTTP adapters.
 - 5.2.10 singled out as the minimum version, up from just 5.2.x

## What Works

 - /upload: Upload an image
   - From a file on disk (via filename)
   - From a file in memory (as a variable)
   - From a URL
 - /image: Fetch information about an image
 - /delete: Delete an image

## What Doesn't Work Yet

 - Everything else.

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

    See demos/httpadapters.php for a quick usage demonstration.
