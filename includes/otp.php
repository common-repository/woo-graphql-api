<?php

namespace WCGQL;

function otp_install()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'otp_tokens';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id int(10) NOT NULL AUTO_INCREMENT,
		telephone VARCHAR(20) NOT NULL,
		code VARCHAR(10) NOT NULL,
		is_valid Tinyint(1) NOT NULL DEFAULT 1,
		createdAt DATETIME NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}