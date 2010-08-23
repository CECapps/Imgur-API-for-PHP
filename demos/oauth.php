<?php
/**
 * PHP Interface to v2 of the Imgur API
 * OAuth Demo
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

/*

You are going to want to read this entire comment block.

Provider = Imgur
Consumer = *YOUR CODE*
User = The user

OAuth works thusly:
    1) The Provider issues a Consumer Key and a Consumer Secret to the Consumer.
    2) A User makes a request to the Consumer.
    3) The Consumer asks the Provider for a Request Token and Request Secret.
       These are usually stored in the User's session.
    4) The Consumer redirects the User to the Provider with the Request Token.
       Most examples now set the user's session state to "1".
    5) The User authorizes the Consumer's access with the Provider.
    6) The Provider sends the User back to the Consumer with (another?) Request Token.
    7) The Consumer sends the returned Request Token and the previously sent
       Request Secret to the Provider.
    8) The Provider returns an Access Token and Access Secret.  These are
       usually stored in the user's session, but they can be long-lasting
       and might deserve a better long-term storage location.
    9) Further requests from Consumer to Provider require the Consumer Key,
       the Consumer Secret, the Access Token and the Access Secret.

Really, except for the token jugling, it's pretty similar to most SSO mechanisms
that have ever existed that weren't a complete disaster (lol, SAML).  Of course,
OAuth is *NOT* SSO.

When we hit step 9, the OAuth object has all it needs to perform requests.  The
key, critical part is that the OAuth object must handle requests itself so
that it can properly sign the data being sent over the wire.  This makes life
for us kind of interesting, as we then have to consider how to work with
different HTTP request implementations:

 - PEAR's HTTP_OAuth uses HTTP_Request2.  HTTP_OAuth_Consumer_Request acts as
   a gateway to the request object, but doesn't inherit from it.  This is annoying,
   because we already have a HTTP_Request2 adapter.  We'll need another adapter.
 - Zend's Zend_OAuth uses an extended Zend_Http_Client, and will happily give
   it to us when we ask.  This is good, because we can just reuse the existing
   adapter.  Yay, reuse!
 - The OAuth PECL extension uses CURL, but can not return the CURL handle.
   The fetch() method is entirely responsible for sending requests, but is
   horribly documented.  The second argument to fetch() is the POST data, but
   that fact isn't actually listed anywhere.  Sigh.

After working with the code, I personally *vastly* prefer Zend's implementation.

Also, just in case you missed it:

** YOUR CODE IS RESPONSIBLE FOR OAUTH.  THIS LIBRARY DOES NOT PERFORM OAUTH,   **
** WE MERELY REUSE THE OAUTH OBJECT THAT YOUR CODE MUST CREATE AND INITIALIZE! **

Clear as mud?

Good.

This demo will work with all three supported OAuth implementations.  You *probably
don't want to copy this code*, just use it as a quick OAuth primer.

*/

// Uncomment *ONE* of the following includes to try out each supported library.
// Zend is the preferred library.  It's worth noting that you *CAN* switch
// libraries at any time.  OAuth is a standard, and the keys & tokens are entirely
// interchangable.
    #require_once './oauth-pear.php';
    require_once './oauth-zend.php';
    #require_once './oauth-pecl.php';

// The demo can only run if 
    if($_SESSION['oauth_state'] != 2)
        die("You didn't properly get an OAuth verification.  Bad user, no cookie.");

// Let's start the demo by...
    $account = new Imgur_Account();
    echo "Here's your account info: <pre>";
    print_r($account);
    echo "</pre><hr>";

// Do we have images?
    echo "You have ",
         $account->getImageCount(),
         " images in your account.  Let's fetch some of them:<hr>";

// It's worth pointing out an inconsistency here: You must call load after
// creating *any* image list, because the ArrayObject constructor is funny.
    $il = new Imgur_AccountImages();
    $il->load();
    if(!count($il))
        echo "(Pretend there are images here; none were fetched.)";
    foreach($il as $image) {
        printf(
            '<a href="%s">%s</a>, a %dx%d file uploaded on %s.  You own it because I can see the delete hash: %s<br>',
            $image->link_imgur_page,
            $image->hash,
            $image->height,
            $image->width,
            $image->datetime->format('Y-m-d H:i:s'),
            $image->deletehash
        );
    }
// Let's pick a random image.
    $random_image_hash = array_rand($il->getArrayCopy(), 1);
    $ai = new Imgur_AccountImage($random_image_hash);
    printf(
        '<hr>Picked random image to grab from the account: <a href="%s">%s</a>, %dx%d, %d bytes at %s; Delete Hash %s<br>',
        $ai->link_imgur_page,
        $ai->hash,
        $ai->width,
        $ai->height,
        $ai->size,
        $ai->datetime->format('Y-m-d H:i:s'),
        $ai->deletehash
    );
