# Imgur API for PHP

Licensed under LGPL3.

## Requirements

 - PHP 5.2.10 or better

In order to actually make HTTP requests, you must have one of the following
options available to you:

 - allow_url_fopen enabled in php.ini
 - Zend_Http_Client (Zend Framework, tested with 1.10; also required for Zend_Oauth)
 - HTTP_Request2 (PEAR, tested with 0.5.2; also required for HTTP_OAuth)

In order to make use of any API calls that require authentication, you must use
a supported OAuth library:

 - Zend_Oauth (Zend Framework, tested with 1.10)
 - HTTP_OAuth (PEAR, tested with 0.1.7 -- subtly broken, see Changes)
 - OAuth PECL Extension support is planned for the near future

## Recent Changes

 - Added *basic* OAuth support:
   - Zend's OAuth implementation has been tested and works.
   - PEAR's HTTP_OAuth works, but has some gzip problems.  You may need to modify
     pear/HTTP/Request2/Adapter/Socket.php; search for "deflate" and comment out
     the line.  I'm trying to figure out whether it's a bug with the version I'm
     using or a problem with the compression coming from Imgur.  Zend's OAuth
     impl. is currently the preferred version.
   - OAuth support is in, but the various Authenticated APIs have not yet been coded.
 - Added Account class to work with OAuth support.
 - Added HTTP adapters for OAuth support.
 - Added "demos" directory with small demo applications.
   - "httpadapters" demo contains a quick overview of how to upload a file and
     then fetch properties on it before deleting it, while also giving a very
     quick overview on how to use the HTTP adapters.
   - "oauth" demo for OAuth support.  If you are going to use OAuth but don't
     know how OAuth works, please read it.  If you do know how OAuth works,
     please read it.  If you don't care, please read it.

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
   - /account/albums
   - /account/albums/:ID
   - /account/albums_count
   - /account/albums_order
   - /account/albums_order/:ID
   - /gallery
   - /stats
 
## Quick HOWTO

 - See demos/httpadapters.php for a quick usage demonstration.
 - See demos/oauth.php for an OAuth demonstration.

