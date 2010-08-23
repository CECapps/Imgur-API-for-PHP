<?php
/**
 * PHP Interface to v2 of the Imgur API
 * OAuth Demo: PECL's OAuth
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

// The joy of builtins.
    if(!extension_loaded('oauth'))
        die("OAuth extension not found.");
    $oauth_pecl = new OAuth(CONSUMER_KEY, CONSUMER_SECRET);


// State = 0: The user has not attempted to request OAuth.
    if($_SESSION['oauth_state'] == 0) {
        echo "Step 1: We've already registered with Imgur.  Our consumer key is: ", CONSUMER_KEY, "<br>";
        echo "Step 2: You have just made a request, and are thus reading this.  Awesome.<br>";
        echo "Step 3: Let's ask the Provider for a token:<br>";
    // We'll get back a hash with a handful of keys, but we only care about the token and the secret.
        $request_token = $oauth_pecl->getRequestToken(Imgur::$oauth1a_request_token_url, CALLBACK_URL);
    // We'll stash away the *REQUEST* Token & Secret in the user's session.
        $_SESSION['token'] = $request_token['oauth_token'];
        $_SESSION['token_secret'] = $request_token['oauth_token_secret'];
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
    // We need to update the object with the current token+secret pair.
        $oauth_pecl->setToken($_SESSION['token'], $_SESSION['token_secret']);
    // If there's an oauth_verifier present in GET/POST, it will automatically be grabbed.
        $access_token = $oauth_pecl->getAccessToken(Imgur::$oauth1a_access_token_url);
    // Replace the user's request token with their access token.
    // This is the *ACCESS* Token and Secret.  You should store these in your
    // database with the user's record.  We're putting them in the session only
    // so the demo will work.
        $prev_token = $_SESSION['token'];
        $_SESSION['token'] = $access_token['oauth_token'];
        $_SESSION['token_secret'] = $access_token['oauth_token_secret'];
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
    // Just like in the previous state, we need to update the object with the
    // current token+secret pair.
        $oauth_pecl->setToken($_SESSION['token'], $_SESSION['token_secret']);
    // And, off we go, setOAuth detects the class and does the right thing.
        Imgur::setOAuth($oauth_pecl);
    // We'll fall through at this point, so the demo in oauth.php can run.
        echo "Done!  We can make make OAuth requests on your behalf.<br><hr>";
    }
// State = ?: wat
    else {
        echo "Whoa, your OAuth state is totally bogus, dude.";
        exit;
    }
