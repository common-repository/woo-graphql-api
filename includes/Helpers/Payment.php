<?php

namespace WCGQL\Helpers;

class Payment
{
    public static function getPaymentGateways()
    {
        if (! defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        
        if (! defined('WOOCOMMERCE_CHECKOUT')) {
            define('WOOCOMMERCE_CHECKOUT', true);
        }

        $gateways = \WC_Payment_Gateways::instance()->get_available_payment_gateways();

        return array_map(function ($gateway) {
            $title = $gateway->get_title();
            $error = '';

            $code = Utils::get_prop($gateway, 'id');
            $text = $gateway->get_description();
            $details = json_encode(
                Utils::either(Utils::get_prop($gateway, 'account_details'), '')
            );

            // TODO: make this less dependent on specific payment method data 
            $cost = 0;
            if($gateway->id === 'cod' && isset($gateway->settings['extra_fee'])
            ){
                $current = self::getPaymentMethod();
                self::setPaymentMethod('cod');
                
                WC()->cart->calculate_totals();
                $cartFees = WC()->cart->get_fees();
                $feesKeys = array_keys($cartFees);
                $codKey = reset($feesKeys); 
                if(isset($cartFees[$codKey])){
                    $cost = $cartFees[$codKey]->amount;
                }

                self::setPaymentMethod($current);
            }

            $quote = compact('code', 'title', 'cost', 'text', 'details');
            return compact('title', 'error', 'quote');
        }, $gateways);
    }

    public static function setPaymentMethod($code)
    {
        WC()->session->set( 'chosen_payment_method', $code);
        // TODO: add stricter that the value is properly set and not accepting invalid values 
        return WC()->session->get('chosen_payment_method') == $code;
    }

    public static function getPaymentMethod()
    {
        return WC()->session->get('chosen_payment_method');
    }
}
