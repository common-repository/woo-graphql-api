INSERT INTO wp_usermeta(user_id, meta_key, meta_value)
select user_id, 'mobile_username', meta_value from wp_usermeta where meta_key = 'billing_phone'