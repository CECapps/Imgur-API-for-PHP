# Imgur API for PHP

Licensed under LGPL3.

## Requirements

 - PHP 5.2.10 or better
 - The SPL extension (always enabled since 5.3, enabled by default otherwise)

In order to actually make HTTP requests, you must have one of the following
options available to you:

 - allow_url_fopen enabled in php.ini
 - Zend_Http_Client (Zend Framework, tested with 1.10; also required for Zend_Oauth)
 - HTTP_Request2 (PEAR, tested with 0.5.2; also required for HTTP_OAuth)

In order to make use of any API calls that require authentication, you must use
a supported OAuth library:

 - Zend_Oauth (Zend Framework, tested with 1.10)
 - HTTP_OAuth (PEAR, tested with 0.1.7 -- subtly broken, see Changes)
 - The OAuth PECL Extension (tested with 1.0-dev)

If you are not using one of these OAuth libraries, you will need to create your own
HTTP adapter by implementing the Imgur_HTTPAdapter interface.  See the code and
comments in Imgur::setOAuth for the expected adapter naming scheme.

## Recent Changes

 - Completely rebuilt the "oauth.php" demo scripts.  The three libraries now have
   their own individual include files.  Added a config file for keys.
 - Added Account class to work with OAuth support.
   - The getImageCount method "works", but the endpoint is throwing a 404.
     The method currently returns -1 until this is fixed by Imgur.
 - Added AccountImages, AccountImage with read-only support.  Abstract class
   ImageList added to support AccountImages now and albums later.  ImageList is
   an ArrayObject.  Demo in "oauth.php"
 - Added support for the following OAuth implementations:
   - Zend Framework's Zend_Oauth has been tested and works.  *The Zend implementation
     is preferred above the other options.*
   - PECL's OAuth extension has been tested and works.
   - PEAR's HTTP_OAuth works, but has some gzip problems.  You may need to modify
     pear/HTTP/Request2/Adapter/Socket.php; search for "deflate" and comment out
     the line.  I'm trying to figure out whether it's a bug with the version I'm
     using or a problem with the compression coming from Imgur.
 - Added HTTP adapters for not-OAuth use:
   - PHP's built-in streams system, used by default
   - PEAR's HTTP_Request2
   - Zend's Zend_Http_Client
 - Added "demos" directory with small demo applications.
   - "httpadapters.php" demo contains a quick overview of how to upload a file and
     then fetch properties on it before deleting it, while also giving a very
     quick overview on how to use the HTTP adapters.
   - "oauth.php" demo for OAuth support.  If you are going to use OAuth but don't
     know how OAuth works, please read it.  If you do know how OAuth works,
     please read it.  If you don't care, please read it.  Read it.

## What Works

 - /account: Grab Account info
 - /account/images_count: Get the number of images that the account owns.
   - This is currently broken, the endpoint returns a 404.
 - /account/images: List of all images in the account
 - /account/image/:HASH: Fetch information about an account image
 - /upload: Upload an image
   - From a file on disk (via filename)
   - From a file in memory (as a variable)
   - From a URL
 - /image: Fetch information about an image
 - /delete: Delete an image

## What Doesn't Work Yet

 - Everything else.

## TODO

 - API Endpoints: 
   - /account/images
     - Need to add upload support
   - /account/images/:HASH
     - Need to add delete support
   - /account/albums
   - /account/albums/:ID
   - /account/albums_count
   - /account/albums_order
   - /account/albums_order/:ID
   - /gallery
   - /stats
 - Probably could use a CURL HTTP adapter, in the unlikely situation where allow_url_fopen
   is disabled and both PEAR and Zend's HTTP clients are unavailable.
 - Refactoring:
   - Global config in the Imgur class is sloppy.  Make it optional, allow passing in
     configuration information during instance creation.
   - Fix inconsistencies.
   - The PEAR OAuth HTTP adapter is just horrible, horrible code.
   - In fact, the HTTP adapter interface itself is pretty bad.
 - Complete documentation instead of just demo code.

## Quick HOWTO

 - See demos/httpadapters.php for a quick usage demonstration.
 - See demos/oauth.php for an OAuth demonstration.
