<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit ();

global $wpdb;
// remove options
$wpdb->query("DELETE FROM `$wpdb->options` WHERE
    `option_name` = 'wc_gql_default_menu' OR
    `option_name` = 'wc_gql_internal_key' OR
    `option_name` = 'wc_gql_salt' OR
    `option_name` = 'wc_gql_shopz_app_data'
");
// remove users metadata
$wpdb->query("DELETE FROM `$wpdb->usermeta` WHERE
    `meta_key` = 'wc_gql_cart' OR
    `meta_key` = 'wc_gql_wish' OR
    `meta_key` = 'gq_session' OR
    `meta_key` = 'gq_lang' OR
    `meta_key` = 'gq_mob_lang' OR
    `meta_key` = 'wc_multiple_shipping_addresses'
");