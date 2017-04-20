<?php
// CORS
enable_cors();

// Require the QRss class
require 'src/QRss.php';

// Get the feed and cache for 10 min
(new QRss($_REQUEST['url']))->cache_for('10 minutes')->json();

// Get the fresh feed ignoring cache
//(new QRss('https://en.blog.wordpress.com/feed/'))->fresh()->json();

/**
 * Enable CORS support
 */
function enable_cors() {
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, PUT, PATCH, DELETE, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}
