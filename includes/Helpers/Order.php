<?php

namespace WCGQL\Helpers;

class Order
{
    public static function set_confirmation($order_id, $confirmation)
    {
        $order = wc_get_order($order_id);
        $attachment = $confirmation['attachment'];
        $text = $confirmation['text'];
        $note = "Order #$order_id was updated!\n Added bank transfer image: <a href='$attachment'>Image</a>";
        if (!empty($text)) {
            $note .= "\n\rClientComment: $text";
        }
        $order->add_order_note($note);
        $order->set_status('on-hold');
        return $order->save();
    }

    public static function create_order($input)
    {
        if (! defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        
        WC()->cart->calculate_totals();
        
        $shipping_rate = Shipping::getShippingRate();
        $shippingAddress = Shipping::getShippingAddress('shipping', true);
        $billingAddress = Shipping::getShippingAddress('billing', true);
        $shippingMethod = WC()->session->get('chosen_shipping_methods')[0];
        if (Utils::get_prop($input, 'payment_code')) {
            $payment_code = Utils::get_prop($input, 'payment_code');
            $data = self::formatOrderDataArrayV1($input, $billingAddress);
        } else {
            $payment_code = Payment::getPaymentMethod();
            $data = self::formatOrderDataArrayV2($shippingAddress, $billingAddress);
        }

        $data['payment_method'] = $payment_code;
        $data['shipping_method'] = $shippingMethod;

        $cart = WC()->cart;

        if (!WC()->GQL_User ||
            !$shipping_rate ||
            !$shippingAddress ||
            !$billingAddress ||
            !$shippingMethod ||
            !$payment_code ||
            count($cart->cart_contents) == 0
        ) {
            return false;
        }

        $order = wc_create_order();
        $order_id = $order->get_id();

        $order->set_customer_id(WC()->GQL_User->get_id());

        $note = Utils::get_prop($input, 'comment');
        $phone = Utils::get_prop($billingAddress, 'telephone');
        $coupon = WC()->GQL_User->getUserMeta('coupon');

        foreach ($cart->cart_contents as $item) {
            $order->add_product(
                Utils::get_prop($item, 'data'),
                Utils::get_prop($item, 'quantity'),
                array(
                    'variation_id' => Utils::get_prop($item, 'variation_id'),
                )
            );
        }

        if (!empty($coupon)) {
            $order->apply_coupon($coupon);
        }

        if (!empty($note)) {
            $data['order_comments'] = $note;
            $order->set_customer_note($note);
        }

        if (!empty($phone)) {
            $order->set_props(
                array(
                  'billing_phone' => $phone,
                )
            );
        }

        WC()->checkout->create_order_fee_lines($order, $cart);

        // set shipping method
        $shipping_item = new \WC_Order_Item_Shipping();
        $shipping_item->set_order_id($order_id);
        $shipping_item->set_shipping_rate($shipping_rate);
        $order->add_item($shipping_item);

        // set shipping & payment addresses
        $order->set_address($shippingAddress, 'shipping');
        $order->set_address($billingAddress, 'billing');
        \update_post_meta($order_id, 'custom_fields', $shippingAddress['custom_field']);
        \update_post_meta($order_id, 'shipping_custom_fields', $shippingAddress['custom_field']);
        \update_post_meta($order_id, 'billing_custom_fields', $billingAddress['custom_field']);

        // set payment method
        $payment_gateways = \WC_Payment_Gateways::instance()->get_available_payment_gateways();
        $order->set_payment_method(isset($payment_gateways[$payment_code])?$payment_gateways[$payment_code]:$payment_code);

        $order->calculate_totals();

        $status = self::getOrderStatus($order, $payment_code);
        $order->update_status($status);
        $order->save();
        
        do_action('woocommerce_checkout_update_order_meta', $order_id, $data);
        self::maybeEmptyCart($payment_code);
        return $order_id;
    }

    public static function formatOrderDataArrayV1($input, $billingAddress)
    {
        return [
            "terms" => 0,
            "createaccount" => 0,
            "ship_to_different_address" => false,
            "woocommerce_checkout_update_totals" => false,
            "billing_first_name" => Utils::get_prop($input, 'payment_firstname'),
            "billing_last_name" => Utils::get_prop($input, 'payment_lastname'),
            "billing_company" => Utils::get_prop($input, 'payment_company'),
            "billing_country" => Utils::get_prop($input, 'payment_country'),
            "billing_address_1" => Utils::get_prop($input, 'payment_address_1'),
            "billing_address_2" => Utils::get_prop($input, 'payment_address_2'),
            "billing_city" => Utils::get_prop($input, 'payment_city'),
            "billing_state" => Utils::get_prop($input, 'payment_zone'),
            "billing_postcode" => Utils::get_prop($input, 'payment_postcode'),
            "billing_phone" => $billingAddress['telephone'],
            "billing_email" => WC()->customer->get_email(),
            "shipping_first_name" => Utils::get_prop($input, 'shipping_firstname'),
            "shipping_last_name" => Utils::get_prop($input, 'shipping_lastname'),
            "shipping_company" => Utils::get_prop($input, 'shipping_company'),
            "shipping_country" => Utils::get_prop($input, 'shipping_country'),
            "shipping_address_1" => Utils::get_prop($input, 'shipping_address_1'),
            "shipping_address_2" => Utils::get_prop($input, 'shipping_address_2'),
            "shipping_city" => Utils::get_prop($input, 'shipping_city'),
            "shipping_state" => Utils::get_prop($input, 'shipping_zone'),
            "shipping_postcode" => Utils::get_prop($input, 'shipping_postcode'),
          ];
    }

    public static function formatOrderDataArrayV2($billingAddress, $shippingAddress)
    {
        return [
            "terms" => 0,
            "createaccount" => 0,
            "ship_to_different_address" => false,
            "woocommerce_checkout_update_totals" => false,
            "billing_first_name" => $billingAddress['first_name'],
            "billing_last_name" => $billingAddress['last_name'],
            "billing_company" => $billingAddress['company'],
            "billing_country" => $billingAddress['country'],
            "billing_address_1" => $billingAddress['address_1'],
            "billing_address_2" => $billingAddress['address_2'],
            "billing_city" => $billingAddress['city'],
            "billing_state" => $billingAddress['state'],
            "billing_postcode" => $billingAddress['postcode'],
            "billing_phone" => $billingAddress['telephone'],
            "billing_email" => WC()->customer->get_email(),
            "shipping_first_name" => $shippingAddress['first_name'],
            "shipping_last_name" => $shippingAddress['last_name'],
            "shipping_company" => $shippingAddress['company'],
            "shipping_country" => $shippingAddress['country'],
            "shipping_address_1" => $shippingAddress['address_1'],
            "shipping_address_2" => $shippingAddress['address_2'],
            "shipping_city" => $shippingAddress['city'],
            "shipping_state" => $shippingAddress['state'],
            "shipping_postcode" => $shippingAddress['postcode'],
          ];
    }

    public static function getOrderStatus($order, $paymentMethod)
    {
        $status = 'pending';
        if ($paymentMethod === 'cod') {
            $status = 'processing';
        } elseif ($paymentMethod === 'bacs') {
            $status = 'on-hold';
        }

        return $status;
    }

    public static function sendProperEmails($order_id, $status, $sendToAdmin = false)
    {
        if ($sendToAdmin) {
            (new \WC_Email_New_Order())->trigger($order_id);
        }

        switch ($status) {
          case 'processing':
            (new \WC_Email_Customer_Processing_Order())->trigger($order_id);
            break;
            
          case 'on-hold':
            (new \WC_Email_Customer_On_Hold_Order())->trigger($order_id);
            break;
          
          default:
            break;
        }
    }

    private static function maybeEmptyCart($paymentMethod)
    {
        if (in_array($paymentMethod, ['cod', 'bacs'], true)) {
            WC()->cart->empty_cart();
        }
    }

    public static function register_admin_order_shipping_address($order)
    {
        // Custom Order Fields
        $custom_fields = get_post_meta($order->get_id(), 'custom_fields', true);
        $custom_object = json_decode($custom_fields);
        // GMaps Location
        if (isset($custom_object->gps) && isset($custom_object->gps->latLng)) {
            $lat_long = $custom_object->gps->latLng;
            echo "<a target='_blank' href='https://www.google.com/maps/search/?api=1&query=$lat_long'>Location</a>";
        } elseif (isset($custom_object->latLng)) {
            $lat_long = $custom_object->latLng;
            echo "<a target='_blank' href='https://www.google.com/maps/search/?api=1&query=$lat_long'>Location</a>";
        }
        // Shipping Date
        if (isset($custom_object->shipping_date)) {
            echo "<p>Shipping Date: $custom_object->shipping_date</p>";
        }

        // Shipping Address
        $shipping_custom_fields = (array)json_decode(get_post_meta($order->get_id(), 'shipping_custom_fields', true));
        foreach ($shipping_custom_fields as $key => $value) {
            if (is_string($value)) {
                echo "<p>$key: $value</p>";
            }
        }
    }

    public static function register_admin_order_payment_address($order)
    {
        $billing_custom_fields = (array)json_decode(get_post_meta($order->get_id(), 'billing_custom_fields', true));
        foreach ($billing_custom_fields as $key => $value) {
            if (is_string($value)) {
                echo "<p>$key: $value</p>";
            }
        }
    }

    public static function to_schema($order_id = false)
    {
        $order = wc_get_order($order_id);

        if (!$order instanceof \WC_Order) {
            throw new \Exception("Invalid order id ($order_id)");
        }
        $couponCodes = $order->get_coupon_codes();

        return array(
            'order_id' => $order_id,
            'invoice_no' => 0,
            'invoice_prefix' => '',
            'store_name' => '',
            'store_url' => '',
            'customer_id' => $order->get_customer_id(),
            'firstname' => $order->get_billing_first_name(),
            'lastname' => $order->get_billing_last_name(),
            'email' => $order->get_billing_email(),
            'telephone' => $order->get_billing_phone(),
            'fax' => '',
            'custom_field' => '',
            'payment_firstname' => $order->get_billing_first_name(),
            'payment_lastname' => $order->get_billing_last_name(),
            'payment_company' => $order->get_billing_company(),
            'payment_address_1' => $order->get_billing_address_1(),
            'payment_address_2' => $order->get_billing_address_2(),
            'payment_postcode' => $order->get_billing_postcode(),
            'payment_city' => $order->get_billing_city(),
            'payment_custom_field' => $order->get_meta('billing_custom_fields'),
            'payment_method' => $order->get_payment_method_title()? $order->get_payment_method_title(): $order->get_payment_method(),
            'payment_code' => $order->get_payment_method(),
            'shipping_firstname' => $order->get_shipping_first_name(),
            'shipping_lastname' => $order->get_shipping_last_name(),
            'shipping_company' => $order->get_shipping_company(),
            'shipping_address_1' => $order->get_shipping_address_1(),
            'shipping_address_2' => $order->get_shipping_address_2(),
            'shipping_postcode' => $order->get_shipping_postcode(),
            'shipping_city' => $order->get_shipping_city(),
            'shipping_custom_field' => $order->get_meta('shipping_custom_fields'),
            'shipping_method' => $order->get_shipping_method(),
            'shipping_code' => '',
            'comment' => $order->get_customer_note(),
            'order_status_id' => '',
            'order_status' => $order->get_status(),
            'date_added' => $order->get_date_created()->date('Y-m-d'),
            'date_modified' => $order->get_date_modified()->date('Y-m-d'),
            'total' => $order->get_total(),
            'subtotal' => $order->get_subtotal(),
            'tax_total' => $order->get_total_tax(),
            'shipping_total' => $order->get_shipping_total() + $order->get_shipping_tax(),
            'billing_country' => $order->get_billing_country(),
            'shipping_country' => $order->get_shipping_country(),
            'billing_zone' => $order->get_billing_state(),
            'shipping_zone' => $order->get_shipping_state(),
            'fees_total' => $order->get_total_fees(),
            'coupon_code' => (string) reset($couponCodes),
            'coupon_discount' => $order->get_discount_total(),
        );
    }
}
