<?php

/*
|--------------------------------------------------------------------------
| Test Case & WordPress Bootstrapper
|--------------------------------------------------------------------------
*/

if (!defined('ABSPATH')) {
    define('WP_USE_THEMES', false);
    $wpLoad = dirname(__DIR__) . '/web/wp/wp-load.php';
    if (file_exists($wpLoad)) {
        require_once $wpLoad;
    }
}
