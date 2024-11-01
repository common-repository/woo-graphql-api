<?php
/*
Plugin Name: Shopz GraphQL API for WooCommerce
Plugin URI: http://wordpress.org/plugins/shopz-graphql-api
Description: Exposing WooCommerce as a GraphQL API
Author: Shopz.io
Version: 2.1.5
Author URI: https://Shopz.io
Tested up to: 5.6
WC requires at least: 3.6.0
WC tested up to: 4.9.2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Autoload vendor files.
require_once __DIR__ . '/vendor/autoload.php';
// GraphQL Types.
require_once __DIR__ . '/includes/Types.php';
// Client initialization code.
require_once __DIR__ . '/includes/Client.php';
// Admin and Installation.
require_once __DIR__ . '/includes/Admin.php';
// Load Util functions.
require_once __DIR__ . '/includes/Helpers/Utils.php';
// Load create tokens table function.
require_once __DIR__ . '/includes/otp.php';
// Load plugin file to fix activation issues
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// define constants.
define ('WC_GQL_REST_HOST', get_site_url());
define ('WC_GQL_PLUGIN_VERSION', '2.1.5');

if(!get_option('wc_gql_salt')){
    WCGQL\active_plugin();
}

define ('WC_GQL_INTERNAL_KEY', get_option('wc_gql_internal_key'));
define ('WC_GQL_TOKEN_SALT', get_option('wc_gql_salt'));
define ('WC_GQL_TOKEN_KEY', WC_GQL_TOKEN_SALT);
define('WC_GQL_DB_VERSION', get_option('wc_gql_db_version'));

register_activation_hook (__file__, '\WCGQL\active_plugin');
register_activation_hook (__file__, '\WCGQL\otp_install');

add_action ('rest_api_init', '\WCGQL\register_graphql_endpoint');
add_action( 'admin_init', '\WCGQL\register_options');
add_action ('admin_menu', '\WCGQL\register_options_page');
add_action ('admin_menu', '\WCGQL\register_dashboard_page');
add_action('woocommerce_admin_order_data_after_shipping_address', '\WCGQL\Helpers\Order::register_admin_order_shipping_address');
add_action('woocommerce_admin_order_data_after_billing_address', '\WCGQL\Helpers\Order::register_admin_order_payment_address');
add_action( 'admin_post_nopriv_mobile_settings', '\WCGQL\saveMobileSetting' );
add_action( 'admin_post_mobile_settings', '\WCGQL\saveMobileSetting' );
add_filter( 'woocommerce_states', '\WCGQL\addCountriesStates' );

add_action( 'admin_post_nopriv_misc_settings', '\WCGQL\saveMiscSettings' );
add_action( 'admin_post_misc_settings', '\WCGQL\saveMiscSettings' );
add_action( 'admin_post_migrate_database_changes', '\WCGQL\migrateChangesManually' );

add_action( 'show_user_profile', '\WCGQL\extra_user_profile_fields' );
add_action( 'edit_user_profile', '\WCGQL\extra_user_profile_fields' );
add_action( 'personal_options_update', '\WCGQL\save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', '\WCGQL\save_extra_user_profile_fields' );

add_action( 'admin_post_nopriv_app_secrets', '\WCGQL\saveAppSecrets' );
add_action( 'admin_post_app_secrets', '\WCGQL\saveAppSecrets' );

add_action( 'admin_post_nopriv_send_notification', '\WCGQL\sendNotification' );
add_action( 'admin_post_send_notification', '\WCGQL\sendNotification' );

add_action('admin_notices', '\WCGQL\notificationSuccessMessage');
add_action('admin_notices', '\WCGQL\notificationErrorMessage');
