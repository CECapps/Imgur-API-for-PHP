<?php

// Set this to your Key and Secret, as given to you when registering to use the API.
    define('CONSUMER_KEY', '');
    define('CONSUMER_SECRET', '');

// Leave this alone, unless detecting PHP_SELF is buggy on your machine.
    define('CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    session_start();

