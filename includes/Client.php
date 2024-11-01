<?php

namespace WCGQL;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;
use GraphQL\GraphQL;
use WCGQL\GQL\Types;
use WCGQL\Helpers\Session;
use WCGQL\Helpers\User;
use WCGQL\Helpers\Utils;
use WCGQL\Helpers\Wishlist;
use WCGQL\Translators\TranslatorsFactory;
use WCGQL\Helpers\CloudLogger;

function register_graphql_endpoint()
{
    register_rest_route('shopz', 'graphql', [
        'methods' => 'POST',
        'callback' => '\WCGQL\process_graphql'
    ]);

    register_rest_route('shopz', 'authenticate', [
        'methods' => 'POST',
        'callback' => '\WCGQL\generateSecrets'
    ]);

    Session::setGlobalSessionId();

    if (null === WC()->GQL_User) {
        WC()->GQL_User = User::getCustomerFromSessionToken();
    }
}

function initApi()
{
    setCurrentLanguage();

    Session::setGlobalSessionId();

    if (null === WC()->GQL_User || !is_user_logged_in()) {
        WC()->GQL_User = User::getCustomerFromSessionToken();
    }

    if (defined('WC_ABSPATH')) {
        // WC 3.6+ - Cart and notice functions are not included during a REST request.
        include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
        include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
    }

    if (null === WC()->session) {
        WC()->session = new Session();
        WC()->session->init();
    }

    if ( null === WC()->customer ) {
        WC()->customer = new \WC_Customer( User::getCustomerFromSessionToken()->get_id(), true );
    }

    if ( null === WC()->cart ) {
        WC()->cart = new \WC_Cart();
        WC()->cart->get_cart();
    }

    if (null === WC()->GQL_Wishlist) {
        WC()->GQL_Wishlist = new Wishlist();
    }
}


function setCurrentLanguage()
{
    Session::setGlobalLanguageCode();
 
    if (get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\WPMLTranslator") {
        // Force functions to use mobile language 
        global $sitepress;
        $currentLanguageCode = TranslatorsFactory::get_translator()->get_language()['code'];
        $sitepress->switch_lang($currentLanguageCode, true);
    }
}

$errorHandler = function(array $errors, callable $formatter) {
    array_map(function($error){
        CloudLogger::error($error);
    }, $errors);

    return array_map($formatter, $errors);
}; 

function process_graphql()
{
    global $errorHandler;

    $debug = false;
    if (!empty($_GET['debug'])) {
        set_error_handler(function ($severity, $message, $file, $line) use (&$phpErrors) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
        $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
    }

    try {
        // Parse incoming query and variables
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $raw = file_get_contents('php://input') ?: '';
            $data = json_decode($raw, true);
        } else {
            $data = $_REQUEST;
        }
        $data += ['query' => null, 'variables' => null];

        $types = Types::Instance();

        initApi();

        $result = GraphQL::executeQuery(
            $types::$schema,
            $data['query'],
            null,
            [],
            (array)$data['variables']
        )->setErrorsHandler($errorHandler);
        $output = $result->toArray($debug);
        $httpStatus = 200;
    } catch (\Throwable $error) {
        $httpStatus = 500;
        CloudLogger::error($error);
        $output['errors'] = [
            FormattedError::createFromException($error, $debug)
        ];
    }

    return $output;
}

add_action( 'upgrader_process_complete', '\WCGQL\migrateChanges', 10, 2 );
