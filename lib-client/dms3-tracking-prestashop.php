<?php

/*
 * DeMomentSomTres Tracking Library for Prestahop
 * Plugin usage tracking
 * Inspired on Yoast_Tracking
 */

// include only file
if (!defined('_PS_VERSION_')):
    header('HTTP/1.0 403 Forbidden');
    die();
endif;

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
        public function __construct() {
            
        }

        /**
         * Main tracking function.
         * @param mixed $info
         * @param string $package
         */
        public function tracking($event = 'GenericEvent', $info = array()) {

            $context = Context::getContext();
            $package = 'Prestashop';

            $hash = md5($_SERVER['SERVER_NAME']);

            $data = array(
                'site' => array(
                    'hash' => $hash,
                    'system' => $package,
                    'directory' => $context->shop->physical_uri,
                    'version' => _PS_VERSION_,
                    'event' => $event,
                    'language' => $context->language->iso_code,
                ),
                'data' => $info,
                'modules' => Module::getModulesInstalled(),
                'theme' => $context->shop->theme_name,
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
            $context = stream_context_create($options);
            $response = file_get_contents('http://dms3.cat/tracking/tracking.php', false, $context);
            return true;
        }

    }

}

// Example call to import information into DeMomentSomTres_Tracking() using the new DMS3Tracking Hook
function hookDMS3Tracking($params) {
    $data = array();
    $demomentsomtres_tracking->add_info($data);
}

?>