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

So, PEAR's HTTP_OAuth and Zend's Zend_OAuth win.  We'll do those first, and
worry about the PECL extension later.

Also, just in case you missed it:

** YOUR CODE IS RESPONSIBLE FOR OAUTH.  THIS LIBRARY DOES NOT PERFORM OAUTH,   **
** WE MERELY REUSE THE OAUTH OBJECT THAT YOUR CODE MUST CREATE AND INITIALIZE! **

Clear as mud?

Good.

This demo will try to work with all three supported OAuth implementations.
It will actively print out the state of the world, and let the user know what
is going on.  You *probably don't want to copy this code*, just use it as
a quick OAuth primer.

(Yes, doing all three inline, in the same code, is messy.  However, it lets
you easily compare how all three work.  If you don't like how one works,
you can switch to another, after all.)

*/

    include_once '../Imgur.php';
    Imgur::registerSPLAutoloader();

    define('OAUTH_LIB', 'PEAR');
    #define('OAUTH_LIB', 'ZEND');
    #define('OAUTH_LIB', 'PECL');

    define('CONSUMER_KEY', '');
    define('CONSUMER_SECRET', '');

    define('CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

    session_start();
    #unset($_SESSION['oauth_state']);
    if(!array_key_exists('oauth_state', $_SESSION)) {
        unset($_SESSION['token']);
        unset($_SESSION['token_secret']);
        $_SESSION['oauth_state'] = 0;
    } 

    /** @var HTTP_OAuth_Consumer */
    $oauth_pear = null;
    /** @var Zend_Oauth_Consumer */
    $oauth_zend = null;
    $zend_oauth_config = array();
    /** @var OAuth */
    $oauth_pecl = null;
    if(OAUTH_LIB == 'PEAR') {
        include_once 'HTTP/OAuth.php';
        include_once 'HTTP/OAuth/Consumer.php';
        $oauth_pear = new HTTP_OAuth_Consumer(
                        CONSUMER_KEY,
                        CONSUMER_SECRET,
                        (isset($_SESSION['token']) ? $_SESSION['token'] : null),
                        (isset($_SESSION['token_secret']) ? $_SESSION['token_secret'] : null)
                    );
    }
    if(OAUTH_LIB == 'ZEND') {
        include_once 'Zend/Loader/Autoloader.php';
        $throwaway = Zend_Loader_Autoloader::getInstance();
        $zend_oauth_config = array(
            'callbackUrl' => CALLBACK_URL,
            'siteUrl' => Imgur::$oauth1a_base_url,
            'consumerKey' => CONSUMER_KEY,
            'consumerSecret' => CONSUMER_SECRET
        );
        $oauth_zend = new Zend_Oauth_Consumer($zend_oauth_config);
    }
    if(OAUTH_LIB == 'PECL') {
        $oauth_pecl = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);
    }

// State = 0: The user has not attempted to request OAuth.
    echo "Step 1: We've already registered with Imgur.  Our consumer key is: ", CONSUMER_KEY, "<br>";
    if($_SESSION['oauth_state'] == 0) {
        echo "Step 2: You have just made a request.  Awesome.<br>";
        echo "Step 3: Let's ask the Provider for a token.<br>";
        if(OAUTH_LIB == 'PEAR') {
            try {
                $oauth_pear->getRequestToken(Imgur::$oauth1a_request_token_url, CALLBACK_URL);
            } catch(HTTP_OAuth_Consumer_Exception_InvalidResponse $e) {
                echo $e->getMessage();
                exit;
            }
            $_SESSION['token'] = $oauth_pear->getToken();
            $_SESSION['token_secret'] = $oauth_pear->getTokenSecret();
        }
        if(OAUTH_LIB == 'ZEND') {
            /** @var Zend_Oauth_Token_Request */
            $request_token = $oauth_zend->getRequestToken();
            #var_export($request_token);
            $_SESSION['token'] = $request_token->getToken();
            $_SESSION['token_secret'] = $request_token->getTokenSecret();
        }
        if(OAUTH_LIB == 'PECL') {
            $request_token = $oauth_pecl->getRequestToken(Imgur::$oauth1a_request_token_url);
            $_SESSION['token'] = $request_token['oauth_token'];
            $_SESSION['token_secret'] = $request_token['oauth_secret'];
        }
        echo "Step 4: Your token is {$_SESSION['token']}.  Click the following link to pop over to Imgur and authorize the demo: ";
    // You'll note that our URL is missing from this request.  This is because
    // our Consumer Key & Consumer Secret are paired with one specific endpoint,
    // which is then stored with the Provider.  They'll send the user back here.
        echo '<a href="',
             Imgur::$oauth1a_authorize_url,
             '?oauth_token=',
             urlencode($_SESSION['token']),
             '">Clicky.</a>';
        $_SESSION['oauth_state'] = 1;
        exit;
    }
// State = 1: The user has just come back here from the Provider.
    elseif($_SESSION['oauth_state'] == 1) {
        echo "Step 5: You just authorized this demo for access.  Thanks!<br>";
        echo "Step 6: You've been sent back here with token ", htmlspecialchars($_REQUEST['oauth_token']), "<br>";
        echo "Step 7: Now I'll ask the Provider for access using the various tokens.<br>";
        if(OAUTH_LIB == 'PEAR') {
            $oauth_pear->getAccessToken(Imgur::$oauth1a_access_token_url, array_key_exists('oauth_verifier', $_REQUEST) ? $_REQUEST['oauth_verifier'] : null);
        // Replace the user's request token with their access token.
            $_SESSION['token'] = $oauth_pear->getToken();
            $_SESSION['token_secret'] = $oauth_pear->getTokenSecret();
        }
        if(OAUTH_LIB == 'ZEND') {
            /** @var Zend_Oauth_Token_Access */
            $request_token = new Zend_Oauth_Token_Request();
        // And this is why they have you serialize it in their example code.
            $request_token->setToken($_SESSION['token']);
            $request_token->setTokenSecret($_SESSION['token_secret']);
        // Zend's impl will read the right things straight out of $_GET.
            $access_token = $oauth_zend->getAccessToken($_GET, $request_token);
        // Replace the user's request token with their access token.
            $_SESSION['token'] = $access_token->getToken();
            $_SESSION['token_secret'] = $access_token->getTokenSecret();
        }
        if(OAUTH_LIB == 'PECL') {
            $oauth_pecl->setToken($_SESSION['token'], $_SESSION['token_secret']);
        // If there's an oauth_verifier present in GET/POST, it will automatically be grabbed.
            $access_token = $oauth_pecl->getAccessToken(Imgur::$oauth1a_access_token_url);
        // Replace the user's request token with their access token.
            $_SESSION['token'] = $access_token['oauth_token'];
            $_SESSION['token_secret'] = $access_token['oauth_secret'];
        }
        echo "Step 8: Success!  Your final access token is {$_SESSION['token']}.  ";
        echo "We can now proceed to step nine.  ";
        echo '<a href="',
             $_SERVER['PHP_SELF'],
             '">Clicky.</a>';
        $_SESSION['oauth_state'] = 2;
        exit;
    }
    elseif($_SESSION['oauth_state'] == 2) {
        echo "Step 9: You should have access with key {$_SESSION['token']}!  Here is your account info: <br><pre>";
        if(OAUTH_LIB == 'PEAR') {
        // The PEAR lib takes the user's tokens in the constructor.  We don't need to do anything.
            Imgur::setOAuth($oauth_pear);
        }
        if(OAUTH_LIB == 'ZEND') {
        // Once again, this is why they have you serialize it in their example code...
            $access_token = new Zend_Oauth_Token_Access();
            $access_token->setToken($_SESSION['token']);
            $access_token->setTokenSecret($_SESSION['token_secret']);
        // We can work with this, or we can call getHttpClient and use the ZendHttpClient wrapper.
            Imgur::setOAuth($access_token->getHttpClient($zend_oauth_config));
        }
        if(OAUTH_LIB == 'PECL') {
        // Second verse, same as the first.
            $oauth_pecl->setToken($_SESSION['token'], $_SESSION['token_secret']);
        // And, off we go.
            Imgur::setOAuth($oauth_pecl);
        }
        $account = new Imgur_Account();
        print_r($account);
        echo "</pre>";
    }
// State = ?: wat
    else {
        echo "Whoa, your OAuth state is totally bogus, dude.";
        exit;
    }

