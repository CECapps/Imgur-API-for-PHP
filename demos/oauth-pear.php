<?php
/**
 * PHP Interface to v2 of the Imgur API
 * OAuth Demo: PEAR's HTTP_OAuth
 * 
 * @author "McGlockenshire"
 * @link http://github.com/McGlockenshire/Imgur-API-for-PHP
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 **/

    error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
    ini_set('display_errors', true);
    include_once '../Imgur.php';
    Imgur::registerSPLAutoloader();
// We need an "oauth-config.php" file in the current directory.  The demo code
// ships with an "oauth-config.default.php" that must be copied and modified.
    require_once __DIR__ . '/oauth-config.php';

    if(!strlen(CONSUMER_KEY))
        die("OAuth Consumer Key not set.");
    if(!strlen(CONSUMER_SECRET))
        die("OAuth Consumer Secret not set.");
    #unset($_SESSION['oauth_state']);
    if(!array_key_exists('oauth_state', $_SESSION)) {
        unset($_SESSION['token']);
        unset($_SESSION['token_secret']);
        $_SESSION['oauth_state'] = 0;
    }

// You must have the PEAR directories in your include_path.  They're present by default.
    include_once 'HTTP/OAuth.php';
    include_once 'HTTP/OAuth/Consumer.php';
    $oauth_pear = new HTTP_OAuth_Consumer(
                    CONSUMER_KEY,
                    CONSUMER_SECRET,
                    (isset($_SESSION['token']) ? $_SESSION['token'] : null),
                    (isset($_SESSION['token_secret']) ? $_SESSION['token_secret'] : null)
                );

// State = 0: The user has not attempted to request OAuth.
    if($_SESSION['oauth_state'] == 0) {
        echo "Step 1: We've already registered with Imgur.  Our consumer key is: ", CONSUMER_KEY, "<br>";
        echo "Step 2: You have just made a request, and are thus reading this.  Awesome.<br>";
        echo "Step 3: Let's ask the Provider for a token:<br>";
        try {
        // The inclusion of the callback URL in the token request causes the remote
        // service to know where to send the user back.  It should be the same
        // callback URL that you registered with the servicve to get your key.
            $oauth_pear->getRequestToken(Imgur::$oauth1a_request_token_url, CALLBACK_URL);
        } catch(HTTP_OAuth_Consumer_Exception_InvalidResponse $e) {
            echo $e->getMessage();
            exit;
        }
    // We'll stash away the *REQUEST* Token & Secret in the user's session.
    // On the next pageview, they'll be loaded into the OAuth Consumer
    // when it's created, so we don't need to set them manually.
        $_SESSION['token'] = $oauth_pear->getToken();
        $_SESSION['token_secret'] = $oauth_pear->getTokenSecret();
        if(strlen($_SESSION['token']) && strlen($_SESSION['token_secret'])) {
            echo "Step 4: Your token is {$_SESSION['token']}.  Click the following link to pop over to Imgur and authorize the demo: ";
            echo '<a href="',
                 Imgur::$oauth1a_authorize_url,
                 '?oauth_token=',
                 urlencode($_SESSION['token']),
                 '">Clicky.</a>';
            $_SESSION['oauth_state'] = 1;
        } else {
            echo "Something went wrong.  You should probably see an error message above.<br>";
        }
        exit;
    }
// State = 1: The user has just come back here from the Provider.
    elseif($_SESSION['oauth_state'] == 1) {
        echo "Step 5: You just authorized this demo for access.  Thanks!<br>";
        echo "Step 6: You've been sent back here with token ",
             htmlspecialchars($_REQUEST['oauth_token']),
             " and verifier ",
             htmlspecialchars($_REQUEST['oauth_verifier']),
             "<br>";
        echo "Step 7: Now I'll ask the Provider for access using the various tokens.<br>";
    // This can probably throw a variety of exceptions as well, but I haven't
    // encountered any of them.  Also note the express passing of oauth_verifier here.
        $oauth_pear->getAccessToken(Imgur::$oauth1a_access_token_url, array_key_exists('oauth_verifier', $_REQUEST) ? $_REQUEST['oauth_verifier'] : null);
    // This is the *ACCESS* Token and Secret.  You should store these in your
    // database with the user's record.  We're putting them in the session only
    // so the demo will work.  It's worth noting again that on subsequent pageviews,
    // the new Access Token & Secret are automatically passed into the OAuth Consumer.
        $prev_token = $_SESSION['token'];
        $_SESSION['token'] = $oauth_pear->getToken();
        $_SESSION['token_secret'] = $oauth_pear->getTokenSecret();
        if(strlen($_SESSION['token']) && strlen($_SESSION['token_secret']) && $_SESSION['token'] != $prev_token) {
            echo "Step 8: Success!  Your final access token is {$_SESSION['token']}.  ";
            echo "We can now proceed to step nine.  ";
            echo '<a href="',
                 $_SERVER['PHP_SELF'],
                 '">Clicky.</a>';
            $_SESSION['oauth_state'] = 2;
        } else {
            echo "Something went wrong.  Didn't get the right tokens.  You should probably see an error message above.<br>";
            echo "Be aware that these tokens are one-use only.  You <i>might</i> need to reset your session.";
        }
        exit;
    }
// State = 2: The user should have access.
    elseif($_SESSION['oauth_state'] == 2) {
        echo "Step 9: You should have access with key {$_SESSION['token']}!<br>";
    // setOAuth will detect the type of object and automatically load the proper
    // HTTP adapter, so we don't need to do any special work.
        Imgur::setOAuth($oauth_pear);
    // We'll fall through at this point, so the demo in oauth.php can run.
        echo "Done!  We can make make OAuth requests on your behalf.<br><hr>";
    }
// State = ?: wat
    else {
        echo "Whoa, your OAuth state is totally bogus, dude.";
        exit;
    }
