<?php

namespace WCGQL\Helpers;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;

class CloudLogger
{
    public static function error($error, $options = '')
    {
        $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
        $appId = get_option('shopz-app-id', 'undefined');
        $ddTags = "appId:". $appId;
        $rawPayload = file_get_contents('php://input');
        $processedPayload = self::processPayload($rawPayload);
        
        $body = [
            'level' => 'error',
            'message' => $error->getMessage(),
            'meta' => [
                'hostname' => get_site_url(),
                'ddsource' => 'php',
                'service' => 'shopz-plugin',
                'ddtags' => $ddTags,
                'title' => $error->getMessage(),
                'trace' => FormattedError::createFromException($error, $debug),
                'payload' => $processedPayload,
                'cart' => WC()->cart->get_cart(),
                'session' => WC()->session->get_session_data(),
            ]
        ];
         
        $body = wp_json_encode($body);
        
        $options = [
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
                'X-Parse-Application-Id' => 'EpxJ14t7s9aSJIturx1klEIz3H17wk7h'
            ]
        ];
        $endpoint = 'https://shopz-parse.dokku.shopz.io/parse/functions/addLog';
        wp_remote_post($endpoint, $options);
    }

    private static function processPayload($payload)
    {
        $data = json_decode($payload, true);
        $variables = $data['variables'];
        if (isset($variables['password'])) {
            $data['variables']['password'] = '[REDACTED]';
        }

        return json_encode($data);
    }
}
