<?php

namespace WCGQL\Helpers;

use Firebase\JWT\JWT;

class Session extends \WC_Session_Handler
{
    /**
     * Helper function to get session data from DB.
     *
     * @param string $session_key
     * @return stdClass
     */
    public static function get_session_from_db($session_key)
    {
        global $wpdb;

        $session_data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_sessions WHERE `session_key` = %s",
                $session_key
            )
        );

        return $session_data;
    }

    public static function generateSecrets()
    {
        $token = md5(time());
        $secret = sha1($token . WC_GQL_TOKEN_SALT);
        return array($token, $secret);
    }

    public static function generateSessionToken($payload)
    {
        $sessionToken = JWT::encode([
            'iss' => 'shopz.io',
            'nbf' => time() - 13,
            'data' => $payload,
        ], WC_GQL_TOKEN_KEY, 'HS512');
        return $sessionToken;
    }

    public static function decodeSentToken($token)
    {
        try {
            $decoded = (array)JWT::decode($token, WC_GQL_TOKEN_KEY, ['HS512']);
            $data = (array)Utils::get_prop($decoded, 'data');
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function setGlobalSessionId()
    {
        $headers = getallheaders();
        if (isset($headers['X-Session-Id'])) {
            $GLOBALS['gq_session_id'] = $headers['X-Session-Id'];
            return;
        }

        if (isset($headers['x-session-id'])) {
            $GLOBALS['gq_session_id'] = $headers['x-session-id'];
            return;
        }
        $GLOBALS['gq_session_id'] = '';
    }

    public static function setGlobalLanguageCode()
    {
        $headers = getallheaders();
        $GLOBALS['gq_lang'] = $headers['Language']?? $headers['language']?? null;    
    }

    public function create_session($customerID)
    {
        wp_set_current_user($customerID);
        $this->set('cart', 'fake cart');
        $this->save_data();
        $cache_prefix = \WC_Cache_Helper::get_cache_prefix(WC_SESSION_CACHE_GROUP);
        return $customerID;
    }

    /**
     * Get the session cookie, if set. Otherwise return false.
     *
     * Session cookies without a customer ID are invalid.
     *
     * @return bool|array
     */
    public function get_session_cookie()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
        $session_expiring = 0;
        $session_expiration = 0;
        $customer_id = get_current_user_id();
        $to_hash = $user_id . '|' . $session_expiration;
        $cookie_hash = hash_hmac('md5', $to_hash, wp_hash($to_hash));

        $cookie_key = "wp_woocommerce_session_" . COOKIEHASH;

        $cookie_value = $user_id . "||" . $session_expiration . "||" . $session_expiring . "||" . $cookie_hash;

        return array($customer_id, $session_expiration, $session_expiring, $cookie_hash);
    }
}
