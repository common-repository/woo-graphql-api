<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Address;
use WCGQL\Helpers\Cart;
use WCGQL\Helpers\Currency;
use WCGQL\Helpers\Menu;
use WCGQL\Helpers\OrdersFactory;
use WCGQL\Helpers\Order;
use WCGQL\Helpers\Payment;
use WCGQL\Helpers\Post;
use WCGQL\Helpers\PostsCategory;
use WCGQL\Helpers\Product;
use WCGQL\Helpers\Shipping;
use WCGQL\Helpers\Utils;
use WCGQL\Helpers\Variation;
use WCGQL\Translators\TranslatorsFactory;
use WCGQL\Helpers\ClientException;

trait RootQueryTypeResolver
{

    public function RootQueryType_manufacturers($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_manufacturer($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_informations($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_information($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_session($root, $args, $ctx)
    {
        if (!isset($GLOBALS['gq_session_id']) || empty($GLOBALS['gq_session_id'])) {
            $id = WC()->GQL_User->get_token();
            $GLOBALS['gq_session_id'] = $id;
        } else {
            $id = sanitize_text_field($GLOBALS['gq_session_id']);
        }
        return [
            'id' => $id
        ];
    }

    public function RootQueryType_cart($root, $args, $ctx)
    {
        return Cart::to_schema();
    }

    public function RootQueryType_customerGroup($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_customerGroups($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_language($root, $args, $ctx)
    {
        return TranslatorsFactory::get_translator()->get_language();
    }

    public function RootQueryType_languages($root, $args, $ctx)
    {
        return TranslatorsFactory::get_translator()->get_languages();
    }

    public function RootQueryType_currency($root, $args, $ctx)
    {
        $currency_code = get_woocommerce_currency();
        return Currency::getCurrency($currency_code);
    }

    public function RootQueryType_currencies($root, $args, $ctx)
    {
        $currencies = get_woocommerce_currencies();
        $res = [];

        foreach ($currencies as $key => $val) {
            $currency = Currency::getCurrency($key);
            $res[] = $currency;
        }

        return $res;
    }

    public function RootQueryType_banners($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_wishlist($root, $args, $ctx)
    {
        return WC()->GQL_Wishlist->to_schema();
    }

    public function RootQueryType_order($root, $args, $ctx)
    {
        return Order::to_schema($args['id']);
    }

    public function RootQueryType_orders($root, $args, $ctx)
    {
        $orders = (new OrdersFactory())
            ->limit(Utils::get_prop($args, 'limit'))
            ->offset(Utils::get_prop($args, 'start'))
            ->get_orders();
            
        return array_map(function ($order) {
            return Order::to_schema($order->get_id());
        }, $orders);
    }

    public function RootQueryType_paymentAddress($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new \Exception('User not logged in');
        }

        return Shipping::getShippingAddress('billing');
    }

    public function RootQueryType_shippingAddress($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new \Exception('User not logged in');
        }

        return Shipping::getShippingAddress('shipping');
    }

    public function RootQueryType_paymentMethods($root, $args, $ctx)
    {
        return Payment::getPaymentGateways();
    }

    public function RootQueryType_shippingMethods($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new \Exception('User not logged in');
        }

        $address_id = WC()->GQL_User->getUserMeta('shipping_address');
        if (!$address_id) {
            return [];
        }

        $address = Address::getAddressByID($address_id);
        if (!$address) {
            throw new \Exception("Address ($address_id) is not valid");
        }

        Shipping::setCurrentAddressAsUserMeta();
        
        return Shipping::getShippingMethods($address);
    }

    public function RootQueryType_loggedIn($root, $args, $ctx)
    {
        return WC()->GQL_User->from_api_to_schema();
    }

    public function RootQueryType_faqs($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_news($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_allnews($root, $args, $ctx)
    {
        return null;
    }

    public function RootQueryType_menu($root, $args, $ctx)
    {
        $name = Utils::get_prop($args, 'name');
        return (new Menu($name))->to_schema();
    }

    public function RootQueryType_productVariationPrice($root, $args, $ctx)
    {
        return Variation::getVariationPrice((new Product($args['product_id']))->get_product(), $args['options']);
    }

    public function RootQueryType_productVariationData($root, $args, $ctx)
    {
        return Variation::productVariationData((new Product($args['product_id']))->get_product(), $args['options']);
    }

    public function RootQueryType_availableOptions($root, $args, $ctx)
    {
        return Variation::getAvailableOptions((new Product($args['product_id']))->get_product(), $args['options']);
    }

    public function RootQueryType_posts_category($root, &$args, &$ctx)
    {
        return (new PostsCategory($args['id']))->to_schema();
    }

    public function RootQueryType_post($root, &$args, &$ctx)
    {
        return (new Post($args['id']))->to_schema();
    }

    public function RootQueryType_siteInfo($root, $args, &$ctx)
    {
        global $wpdb;
        if ($args['key'] != WC_GQL_INTERNAL_KEY) return null;
        $mysqlversion = $wpdb->get_results("select version();");
        return [
            'phpversion' => phpversion(),
            'phpinfo' =>  Utils::phpinfo_dump(),
            'mysqlversion' => $mysqlversion[0]->{'version()'},
            'pluginversion' => WC_GQL_PLUGIN_VERSION,
            'plugins' =>  Utils::getSitePlugins()
        ];
    }

    public function RootQueryType_siteConfig($root, $args, &$ctx) 
    {
        return [
            'tax_display_shop' => get_option( 'woocommerce_tax_display_shop' ),
            'tax_display_cart' => get_option( 'woocommerce_tax_display_cart' ),
            'hide_out_of_stock_items' => 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ? true : false,
            'price_suffix' => get_option( 'woocommerce_price_display_suffix' )
        ];
    }
}
