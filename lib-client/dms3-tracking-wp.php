<?php

/*
 * DeMomentSomTres Tracking Library
 * Plugin usage tracking for WordPress
 * Based on Yoast_Tracking
 */

// include only file
if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    die();
}

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
//            add_action('demomentsomtres_tracking', array($this, 'tracking'));
        }

        /**
         * Main tracking function.
         */
        function tracking() {
            // Start of Metrics
            global $blog_id, $wpdb;

            $hash = get_option('DeMomentSomTres_Tracking_Hash');

            if (!isset($hash) || !$hash || empty($hash)) {
                $hash = md5(site_url());
                update_option('DeMomentSomTres_Tracking_Hash', $hash);
            }

            $data = get_transient('DeMomentSomTres_tracking_cache');
            if (!$data) {

                $pts = array();
                foreach (get_post_types(array('public' => true)) as $pt) {
                    $count = wp_count_posts($pt);
                    $pts[$pt] = $count->publish;
                }

                $comments_count = wp_count_comments();

                // wp_get_theme was introduced in 3.4, for compatibility with older versions, let's do a workaround for now.
                if (function_exists('wp_get_theme')) {
                    $theme_data = wp_get_theme();
                    $theme = array(
                        'name' => $theme_data->display('Name', false, false),
                        'theme_uri' => $theme_data->display('ThemeURI', false, false),
                        'version' => $theme_data->display('Version', false, false),
                        'author' => $theme_data->display('Author', false, false),
                        'author_uri' => $theme_data->display('AuthorURI', false, false),
                    );
                    if (isset($theme_data->template) && !empty($theme_data->template) && $theme_data->parent()) {
                        $theme['template'] = array(
                            'version' => $theme_data->parent()->display('Version', false, false),
                            'name' => $theme_data->parent()->display('Name', false, false),
                            'theme_uri' => $theme_data->parent()->display('ThemeURI', false, false),
                            'author' => $theme_data->parent()->display('Author', false, false),
                            'author_uri' => $theme_data->parent()->display('AuthorURI', false, false),
                        );
                    } else {
                        $theme['template'] = '';
                    }
                } else {
                    $theme_data = (object) get_theme_data(get_stylesheet_directory() . '/style.css');
                    $theme = array(
                        'version' => $theme_data->Version,
                        'name' => $theme_data->Name,
                        'author' => $theme_data->Author,
                        'template' => $theme_data->Template,
                    );
                }

                $plugins = array();
                foreach (get_option('active_plugins') as $plugin_path) {
                    if (!function_exists('get_plugin_data'))
                        require_once( ABSPATH . 'wp-admin/includes/admin.php' );

                    $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);

                    $slug = str_replace('/' . basename($plugin_path), '', $plugin_path);
                    $plugins[$slug] = array(
                        'version' => $plugin_info['Version'],
                        'name' => $plugin_info['Name'],
                        'plugin_uri' => $plugin_info['PluginURI'],
                        'author' => $plugin_info['AuthorName'],
                        'author_uri' => $plugin_info['AuthorURI'],
                    );
                }

                $data = array(
                    'site' => array(
                        'hash' => $hash,
                        'version' => get_bloginfo('version'),
                        'multisite' => is_multisite(),
                        'users' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id) WHERE 1 = 1 AND ( {$wpdb->usermeta}.meta_key = %s )", 'wp_' . $blog_id . '_capabilities')),
                        'lang' => get_locale(),
                        'directory' => dirname(__FILE__),
                    ),
                    'pts' => $pts,
                    'comments' => array(
                        'total' => $comments_count->total_comments,
                        'approved' => $comments_count->approved,
                        'spam' => $comments_count->spam,
                        'pings' => $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'"),
                    ),
                    'info' => apply_filters('demomentsomtres_tracking_filters', array()),
                    'theme' => $theme,
                    'plugins' => $plugins,
                );

                $args = array(
                    'body' => $data
                );
                wp_remote_post('http://dms3.cat/tracking/tracking.php', $args);

                // Store for a week, then push data again.
                set_transient('DeMomentSomTres_tracking_data', $data, true);
                set_transient('DeMomentSomTres_tracking_cache', true, 7 * 60 * 60 * 24);
            }
        }

    }

    $demomentsomtres_tracking = new DeMomentSomTres_Tracking();
}

/**
 * Adds tracking parameters for WP SEO settings. Outside of the main class as the class could also be in use in other plugins.
 *
 * @param array $options
 * @return array
 */
//function wpseo_tracking_additions($options) {
//    $opt = get_wpseo_options();
//
//    $options['wpseo'] = array(
//        'xml_sitemaps' => isset($opt['enablexmlsitemap']) ? 1 : 0,
//        'force_rewrite' => isset($opt['forcerewritetitle']) ? 1 : 0,
//        'opengraph' => isset($opt['opengraph']) ? 1 : 0,
//        'twitter' => isset($opt['twitter']) ? 1 : 0,
//        'strip_category_base' => isset($opt['stripcategorybase']) ? 1 : 0,
//        'on_front' => get_option('show_on_front'),
//    );
//    return $options;
//}
//
//add_filter('demomentsomtres_tracking_filters', 'wpseo_tracking_additions');
//// include only file
//if (!defined('ABSPATH')) {
//  die();
//}
//
//define('DMS3_TRACKING_OPTIONS','DMS3TRACKING');
//
//class dms3_tracking {
//  public static function init() {
//    $options = get_option(DMS3_TRACKING_OPTIONS);
//
//    self::check_opt_in_out();
//
//    // ask user if he wants to allow tracking
//    if (is_admin() && !isset($options['allow_tracking'])) {
//      add_action('admin_notices', array(__CLASS__, 'tracking_notice'));
//    }
//
//    add_action(DMS3_CRON, array(__CLASS__, 'send_data'));
//    // todo - write this properly, so it doesn't run each time, $force ...
//    dms3_tracking::setup_cron();
//  } // init
//
//
//  // register additional cron interval
//  public static function register_cron_intervals($schedules) {
//    $schedules['dms3_weekly'] = array(
//      'interval' => DAY_IN_SECONDS * 7,
//      'display' => 'Once a Week');
//
//    return $schedules;
//  } // cron_intervals
//
//
//  // clear cron scheadule
//  public static function clear_cron() {
//    wp_clear_scheduled_hook(DMS3_CRON);
//  } // clear_cron
//
//
//  // setup cron job when user allows tracking
//  public static function setup_cron() {
//    $options = get_option(DMS3_TRACKING_OPTIONS);
//
//    if (isset($options['allow_tracking']) && $options['allow_tracking'] === true) {
//      if (!wp_next_scheduled(DMS3_CRON)) {
//        wp_schedule_event(time() + 300, 'dms3_weekly', DMS3_CRON);
//      }
//    } else {
//      self::clear_cron();
//    }
//  } // setup_cron
//
//
//  // save user's choice for (not) allowing tracking
//  public static function check_opt_in_out() {
//    $options = get_option(DMS3_TRACKING_OPTIONS);
//
//    if (isset($_GET['dms3_tracking']) && $_GET['dms3_tracking'] == 'opt_in') {
//      $options['allow_tracking'] = true;
//      update_option(DMS3_TRACKING_OPTIONS, $options);
//      self::send_data(true);
//      wp_redirect(remove_query_arg('dms3_tracking'));
//      die();
//    } else if (isset($_GET['dms3_tracking']) && $_GET['dms3_tracking'] == 'opt_out') {
//      $options['allow_tracking'] = false;
//      update_option(DMS3_TRACKING_OPTIONS, $options);
//      wp_redirect(remove_query_arg('dms3_tracking'));
//      die();
//    }
//  } // check_opt_in_out
//
//
//  // display tracking notice
//  public static function tracking_notice() {
//    $optin_url = add_query_arg('dms3_tracking', 'opt_in');
//    $optout_url = add_query_arg('dms3_tracking', 'opt_out');
//
//    echo '<div class="updated"><p>';
//    echo __( 'Please help us improve <strong>Google Maps Widget</strong> by allowing us to track anonymous usage data. Absolutely <strong>no sensitive data is tracked</strong> (<a href="http://www.googlemapswidget.com/plugin-tracking-info/" target="_blank">complete disclosure &amp; details of our tracking policy</a>).', 'google-maps-widget');
//    echo '&nbsp;&nbsp;<a href="' . esc_url($optin_url) . '" style="vertical-align: baseline;" class="button-secondary">' . __('Allow', 'google-maps-widget') . '</a>';
//    echo '&nbsp;&nbsp;<a href="' . esc_url($optout_url) . '" class="">' . __('Do not allow tracking', 'google-maps-widget') . '</a>';
//    echo '</p></div>';
//  } // tracking_notice
//
//
//  // send usage data once a week to our server
//  public static function send_data($force = false) {
//echo "<pre>".print_r(!$force,true)."</pre>";
//    $options = get_option(DMS3_TRACKING_OPTIONS);
//echo "<pre>".print_r($options,true)."</pre>";
//    if ($force == false && (!isset($options['allow_tracking']) || $options['allow_tracking'] !== true)) {
//      return;
//    }
//    if ($force == false && ($options['last_tracking'] && $options['last_tracking'] > strtotime( '-6 days'))) {
//      return;
//    }
//
//    $data = self::prepare_data();
//    $request = wp_remote_post('http://dms3.cat/tracking/tracking.php', array(
//                              'method' => 'POST',
//                              'timeout' => 10,
//                              'redirection' => 3,
//                              'httpversion' => '1.0',
//                              'body' => $data,
//                              'user-agent' => 'DMS3/' . DMS3_VER));
//echo "<pre>".print_r($request,true)."</pre>";
//    $options['last_tracking'] = current_time('timestamp');
//    update_option(DMS3_TRACKING_OPTIONS, $options);
//  } // send_data
//
//
//  // get and prepare data that will be sent out
//  public static function prepare_data() {
//    $options = get_option(DMS3_TRACKING_OPTIONS);
//    $data = array();
//
//    $data['url'] = home_url();
//    $data['wp_version'] = get_bloginfo('version');
//    $data['dms3_version'] = DMS3_VER;
//    $data['dms3_first_version'] = $options['first_version'];
//    $data['dms3_first_install'] = $options['first_install'];
//
//    $data['dms3_count'] = 0;
//    $sidebars = get_option('sidebars_widgets', array());
//    foreach ($sidebars as $sidebar_name => $widgets) {
//      if (strpos($sidebar_name, 'inactive') !== false || strpos($sidebar_name, 'orphaned') !== false) {
//        continue;
//      }
//      if (is_array($widgets)) {
//        foreach ($widgets as $widget_name) {
//          if (strpos($widget_name, 'googlemapswidget') !== false) {
//            $data['dms3_count']++;
//          }
//        }
//      }
//    } // foreach sidebar
//
//    if (get_bloginfo('version') < '3.4') {
//      $theme = get_theme_data(get_stylesheet_directory() . '/style.css');
//      $data['theme_name'] = $theme['Name'];
//      $data['theme_version'] = $theme['Version'];
//    } else {
//      $theme = wp_get_theme();
//      $data['theme_name'] = $theme->Name;
//      $data['theme_version'] = $theme->Version;
//    }
//
//    // get current plugin information
//    if (!function_exists('get_plugins')) {
//      include ABSPATH . '/wp-admin/includes/plugin.php';
//    }
//
//    $plugins = get_plugins();
//    $active_plugins = get_option('active_plugins', array());
//
//    foreach ($active_plugins as $plugin) {
//      $data['plugins'][$plugin] = @$plugins[$plugin];
//    }
//
//    return $data;
//  } // prepare_data
//} // class DMS3_tracking
?>