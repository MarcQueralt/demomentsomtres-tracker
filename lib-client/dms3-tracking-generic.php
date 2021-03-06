<?php

/*
 * DeMomentSomTres Tracking Library
 * Plugin usage tracking
 * Based on Yoast_Tracking
 */

// include only file
// * TODO Evitar la crida directa 
//if (!defined('ABSPATH')) {
//    header('HTTP/1.0 403 Forbidden');
//    die();
//}

/**
 * Class that creates the tracking functionality for WP SEO, as the core class might be used in more plugins,
 * it's checked for existence first.
 *
 * NOTE: this functionality is opt-in. Disabling the tracking in the settings or saying no when asked will cause
 * this file to not even be loaded.
 */
if (!class_exists('DeMomentSomTres_Tracking')) {

    class DeMomentSomTres_Tracking {

        /**
         * Class constructor
         */
        function __construct() {
            
        }

        /**
         * Main tracking function.
         * @param mixed $info
         * @param string $package
         */
        function tracking($info = array(), $package = 'Unspecified', $event = 'GenericEvent') {

//            print_r($_SERVER);
//            exit;
            $hash = md5($_SERVER['SERVER_NAME']);
            $data = array(
                'site' => array(
                    'hash' => $hash,
                    'system' => $package,
                    'event' => $event,
                    'directory' => dirname(__FILE__),
                ),
                'data' => $info,
            );
            $args = array(
                'body' => $data
            );
            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($args),
                ),
            );
//            print_r($options);
            $context = stream_context_create($options);
            $response = file_get_contents('http://dms3.cat/tracking/tracking.php', false, $context);
//            print_r($response);
        }

    }

    $demomentsomtres_tracking = new DeMomentSomTres_Tracking();
}
?>