<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Image;
use WCGQL\Helpers\Country;
use WCGQL\Helpers\State;
use WCGQL\Helpers\Product;
use WCGQL\Translators\TranslatorsFactory;

trait OrderTypeResolver
{

    public function OrderType_store($root, $args, $ctx)
    {
        return null;
    }

    public function OrderType_products($root, $args, $ctx)
    {
        $order_id = $root['order_id'];
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        $res = [];
        foreach ($items as $order_product) {

            $_product = new Product($order_product->get_product());
            $product = $_product->get_product();

            if (!$product instanceof \WC_Product) {
                continue;
            }

            
            TranslatorsFactory::get_translator()->translate_product($_product);

            $res[] = array(
                'order_product_id' => $order_product->get_product_id(),
                'product_id' => $order_product->get_product_id(),
                'name' => $_product->get_name(),
                'price' => $order_product->get_subtotal(),
                'subtotal' => $order_product->get_total(),
                'total' => $order_product->get_total() + $order_product->get_total_tax(),
                'tax' => $order_product->get_total_tax(),
                'model' => '',
                'reward' => 0,
                'quantity' => $order_product->get_quantity(),
                'order_id' => $order_id,
                'tax_mode' => get_option( 'woocommerce_tax_display_shop' ),
                'image' => Image::getMainImage($product->get_id(), $product->get_image_id(), true),
            );

        }

        return $res;
    }

    public function OrderType_paymentZone($root, $args, $ctx)
    {
        $state = new State($root['billing_country'], $root['billing_zone']);
        return $state->toSchema();
    }

    public function OrderType_paymentCountry($root, $args, $ctx)
    {
        $billing_country = $root['billing_country'];
        return Country::getCountryByID($billing_country);
    }

    public function OrderType_shippingZone($root, $args, $ctx)
    {
        $state = new State($root['shipping_country'], $root['shipping_zone']);
        return $state->toSchema();
    }

    public function OrderType_shippingCountry($root, $args, $ctx)
    {
        $shipping_country = $root['shipping_country'];
        return Country::getCountryByID($shipping_country);
    }

    public function OrderType_language($root, $args, $ctx)
    {
        return null;
    }

    public function OrderType_currency($root, $args, $ctx)
    {
        return null;
    }

}

?>