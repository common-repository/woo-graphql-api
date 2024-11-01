<?php

namespace WCGQL\Helpers;

class Cart
{
    public static function add_products($products)
    {
        if (!is_array($products) || count($products) == 0) {
            throw new \Exception("Can't add to cart, empty product list");
        }

        $res = true;
        foreach ($products as $product) {
            $res = ($res && self::add_product($product));
        }
        return $res;
    }

    public static function add_product($input)
    {
        if (!self::validate_item($input)) {
            throw new \Exception("Can't add to cart, invalid product");
        }

        $product_id = $input['product_id'];
        $options = $input['options'] ?? false;
        $quantity = $input['quantity'];
        $variation_id = 0;
        $variation = null;
        
        if ($options) {
            foreach ($options as $key => $option) {
                $options[$key]['product_option_id'] = $option['option_id'];
            }
            $variation = Variation::get_variation((new Product($input['product_id']))->get_product(), $options);

            $variation_id = $variation['variation_id'] ?? 0;
        }

        $cart_id = WC()->cart->add_to_cart(
            $input['product_id'],
            $input['quantity'],
            $variation_id
        );
        if (!$cart_id) {
            throw new \Exception("Can't add to cart" . print_r([
                $input['product_id'],
                $input['quantity'],
                $variation_id
            ], true));
        }

        do_action('woocommerce_add_to_cart', $cart_id, $product_id, $quantity, $variation_id, $variation, []);
        return $cart_id;
    }

    private static function validate_item($item)
    {
        if (
            !is_numeric($item['product_id']) ||
            !is_numeric($item['quantity'])
        ) {
            return false;
        }
        return true;
    }

    public static function update_quantity($cart_id, $quantity)
    {
        if ($quantity == 0) {
            return WC()->cart->remove_cart_item($cart_id);
        }

        $cart_items = WC()->cart->get_cart();
        $values = $cart_items[$cart_id];
        $_product = $values['data'];
        $quantity = apply_filters('woocommerce_stock_amount_cart_item', wc_stock_amount(preg_replace('/[^0-9\.]/', '', $quantity)), $cart_id);

        if ('' === $quantity || $quantity === $values['quantity']) {
            throw new \Exception("Can't update cart, invalid quantity or the same as current ($quantity)");
        }

        $passed_validation = apply_filters('woocommerce_update_cart_validation', true, $cart_id, $values, $quantity);

        if (!$passed_validation) {
            throw new \Exception("Can't update cart, didn't pass woocommerce validation");
        }

        if ($_product->is_sold_individually() && $quantity > 1) {
            throw new \Exception("Can't update cart, sold individually product and quantity ($quantity) is more than 1");
        }

        if (!$_product->is_in_stock()) {
            throw new \Exception("Can't update cart, product is out of stock");
        }

        if ($_product->managing_stock() && !$_product->has_enough_stock($quantity)) {
            throw new \Exception("Can't update cart, product is out of stock");
        }

        return WC()->cart->set_quantity($cart_id, $quantity);
    }

    public static function to_schema()
    {
        if (! defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        
        $couponCode = WC()->GQL_User->getUserMeta('coupon');
        WC()->cart->calculate_totals();
        $cart = WC()->cart;
        $shippingMethod = WC()->session->get('chosen_shipping_methods');
        $paymentMethod = Payment::getPaymentMethod();
        $coupon = new \WC_Coupon($couponCode);

        return array(
            'weight' => $cart->get_cart_contents_weight(),
            'tax' => $cart->get_total_tax(),
            'total' => $cart->total,
            'subtotal' => $cart->get_displayed_subtotal(),
            'coupon_discount' => $cart->get_coupon_discount_amount($coupon->get_code(), WC()->cart->display_cart_ex_tax),
            'coupon_code' => (string) $couponCode,
            'has_shipping' => $cart->needs_shipping(),
            'has_stock' => is_bool($cart->check_cart_item_stock()),
            'shipping_total' => self::getShippingTotal(),
            'shipping_tax' => $cart->get_shipping_tax(),
            'shipping_method_code' => is_array($shippingMethod)? reset($shippingMethod): '',
            'payment_method_code' => $paymentMethod?? ''
        );
    }

    private static function getShippingTotal()
    {
        $tax_display_mode = get_option('woocommerce_tax_display_cart');

        if ('excl' === $tax_display_mode) {
            return WC()->cart->get_shipping_total();
        } else {
            return WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax();
        }
    }
}
