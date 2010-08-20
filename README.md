# Imgur API for PHP

Licensed under LGPL3.

## Requirements

 - PHP 5.2.10 or better
 - allow_url_fopen enabled

### OAuth Requirements

One of:

 - HTTP_OAuth (PEAR, tested with 0.1.7)
 - Zend_Oauth (Zend Framework, tested with 1.10)

Support for the OAuth PECL extension will come soon.

### Optional Requirements

 - HTTP_Request2 (PEAR, tested with 0.5.2; required for HTTP_OAuth)
 - Zend_Http_Client (Zend Framework, tested with 1.10; required for Zend_Oauth)

## Recent Changes

 - Added **UNTESTED** OAuth support.  It's probably completely broken.
 - Added Account class to work with OAuth support.  Also **UNTESTED**.
 - Added HTTP adapters for OAuth support.
 - Added "demos" directory with small demo applications.
   - "httpadapters" demo contains a quick overview of how to upload a file and
     then fetch properties on it before deleting it, while also giving a very
     quick overview on how to use the HTTP adapters.
   - "oauth" demo for OAuth support.  If you are going to use OAuth but don't
     know how OAuth works, please read it.  If you do know how OAuth works,
     please read it.  If you don't care, please read it.
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
   - /account/albums
   - /account/albums/:ID
   - /account/albums_count
   - /account/albums_order
   - /account/albums_order/:ID
   - /gallery
   - /stats
 
## Quick HOWTO

    See demos/httpadapters.php for a quick usage demonstration.

    See demos/oauth.php for an **UNTESTED** OAuth demonstration.
