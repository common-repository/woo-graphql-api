<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Shipping
{
    public static function getShippingMethods($address = null)
    {
        WC()->cart->calculate_shipping();
        $shipping_packages = WC()->shipping()->get_packages();
        if (!$shipping_packages) {
            throw new \Exception('No valid shipping packages');
        }

        $shipping_rates = reset($shipping_packages)['rates'];
        $shipping_zone = self::getZone($address);
        $methods = self::getZoneShippingMethods($shipping_zone);

        return array_map(function ($method) use ($shipping_rates) {
            $tax_display_mode = get_option('woocommerce_tax_display_cart');
            $code = $method->id . ':' . $method->instance_id;
            if ('excl' === $tax_display_mode) {
                $cost = floatval($shipping_rates[$code]->get_cost());
            } else {
                $cost = floatval($shipping_rates[$code]->get_cost() + $shipping_rates[$code]->get_shipping_tax());
            }

            $title = self::getShippingMethodTranslatedTitle($method);

            return array(
                'title' => $title,
                'code' => $code,
                'quote' => array(
                    'code' => $code,
                    'cost' => $cost,
                    'title' => $title,
                    'text' => $method->method_description,
                ),
            );
        }, $methods);
    }

    public static function getZone($address)
    {
        $country = $address['country'];
        $state = $address['state'];
        $postcode = $address['postcode'];
        $package = [
            'destination' => [
                'country' => $country,
                'state' => $state,
                'postcode' => $postcode,
            ],
        ];

        return \WC_Shipping_Zones::get_zone_matching_package($package);
    }

    private static function constructWpmlStringTranslationArgs($method)
    {
        $translationDomain = 'admin_texts_woocommerce_shipping';
        $translationName = $method->id.$method->instance_id.'_shipping_method_title';
        
        return [
            'domain' => $translationDomain,
            'name' => $translationName
        ];
    }

    private static function getShippingMethodTranslatedTitle($method)
    {
        $wmplArgs = self::constructWpmlStringTranslationArgs($method);
        return TranslatorsFactory::get_translator()->translate_string($method->title, $wmplArgs);
    }

    private static function getZoneShippingMethods($shipping_zone)
    {
        $methods = $shipping_zone->get_shipping_methods(true, 'json');
        $filtered_methods = self::validateFreeShipping($methods);
        $free = array();
        foreach ($filtered_methods as $rate_id => $rate) {
            if ('free_shipping' === $rate->id) {
                $free[$rate_id] = $rate;
                break;
            }
        }
        return !empty($free) ? $free : $filtered_methods;
    }

    private static function validateFreeShipping($shipping_methods)
    {
        return array_filter($shipping_methods, function ($method) {
            if ($method->id == 'free_shipping') {
                $freeShippingMethod = new \WC_Shipping_Free_Shipping($method->instance_id);
                return $freeShippingMethod->is_available([]);
            }
            return true;
        });
    }

    public static function setShippingMethod($code)
    {
        WC()->session->set('chosen_shipping_methods', [$code]);

        WC()->cart->calculate_totals();
        if (WC()->session->get('chosen_shipping_methods')[0] !== $code) {
            throw new \Exception("Improper shipping method ($code)");
        }

        return true;
    }

    public static function setShippingAddress($address_id, $type = 'shipping')
    {
        $address = Address::getAddressByID($address_id);
        if (!$address) {
            throw new ClientException("Invalid address ($address_id)");
        }

        return WC()->GQL_User->setUserMeta([
            $type . '_address' => $address_id,
        ]);
    }

    public static function setCurrentAddressAsUserMeta()
    {
        // get cart shipping address
        $address = self::getShippingAddress('shipping');
        // extract address data
        $country = Utils::get_prop($address, 'country');
        $country_id = Utils::get_prop($country, 'country_id');
        $zone = Utils::get_prop($address, 'zone');
        $zone_id = Utils::get_prop($zone, 'zone_id');

        // set customer data
        $metaKeyVals = array(
            'shipping_first_name' => Utils::get_prop($address, 'first_name'),
            'shipping_last_name' => Utils::get_prop($address, 'last_name'),
            'shipping_company' => Utils::get_prop($address, 'company'),
            'shipping_address_1' => Utils::get_prop($address, 'address_1'),
            'shipping_address_2' => Utils::get_prop($address, 'address_2'),
            'shipping_city' => Utils::get_prop($address, 'city'),
            'shipping_state' => $zone_id,
            'shipping_postcode' => Utils::get_prop($address, 'postcode'),
            'shipping_country' => $country_id,
            'shipping_email' => Utils::get_prop($address, 'email'),
            'shipping_phone' => Utils::get_prop($address, 'telephone'),

            'billing_first_name' => Utils::get_prop($address, 'first_name'),
            'billing_last_name' => Utils::get_prop($address, 'last_name'),
            'billing_company' => Utils::get_prop($address, 'company'),
            'billing_address_1' => Utils::get_prop($address, 'address_1'),
            'billing_address_2' => Utils::get_prop($address, 'address_2'),
            'billing_city' => Utils::get_prop($address, 'city'),
            'billing_state' => $zone_id,
            'billing_postcode' => Utils::get_prop($address, 'postcode'),
            'billing_country' => $country_id,
            'billing_email' => Utils::get_prop($address, 'email'),
            'billing_phone' => Utils::get_prop($address, 'telephone'),
        );
        $customer = new \WC_Customer(WC()->GQL_User->get_id());

        foreach ($metaKeyVals as $key => $val) {
            update_user_meta(WC()->GQL_User->get_id(), $key, $val);
        }
        $customer->save();
    }

    public static function getShippingAddress($type = 'shipping', $raw = false)
    {
        $address_id = WC()->GQL_User->getUserMeta("{$type}_address");
        if (!$address_id) {
            return false;
        }

        $address = Address::getAddressByID($address_id);
        if (!$address) {
            throw new \Exception("Invalid address id ($address_id)");
        }

        if ($raw) {
            return $address;
        }

        return Address::to_schema($address);
    }

    public static function getShippingRate()
    {
        $code = WC()->session->get('chosen_shipping_methods');
        if (empty($code)) {
            return false;
        }
        
        WC()->cart->calculate_shipping();
        $shipping_packages = WC()->shipping()->get_packages();
        $shipping_rates = reset($shipping_packages)['rates'];
        return $shipping_rates[reset($code)];
    }
}
