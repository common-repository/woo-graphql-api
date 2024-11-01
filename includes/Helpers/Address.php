<?php

namespace WCGQL\Helpers;

class Address
{
    public static function addAddress($address)
    {
        self::validate_address($address);
        $addresses = self::getAddresses();

        self::checkAddressAlreadyAdded($addresses, $address);

        $address_id = uniqid();
        $address['address_id'] = $address_id;

        $addresses[] = $address;
        self::save($addresses);
        return $address_id;
    }

    public static function validate_address($address)
    {
        if (empty($address['postcode']) || strlen($address['postcode']) != 5) {
            throw new ClientException('Wrong Postal Code');
        }

        // if (!is_numeric($address['state']) || !\WC_Shipping_Zones::get_zone(absint($address['state']))) throw new ClientException('Wrong zone_id');

        if (!isset($address['default'])) {
            $address['default'] = false;
        }
        return true;
    }

    public static function getAddresses()
    {
        $user_id = WC()->GQL_User->get_id();
        $addresses = get_user_meta($user_id, 'wc_multiple_shipping_addresses', true);

        if (!is_array($addresses)) {
            $addresses = [];
        }
        return $addresses;
    }

    public static function checkAddressAlreadyAdded($addressesStack, $addressNeedle)
    {
        foreach ($addressesStack as $address) {
            unset($address['address_id']);
            if (count(\array_diff_assoc($address, $addressNeedle)) == 0) {
                throw new ClientException('Duplicate Address');
            }
        }
        return false;
    }

    public static function save($addresses)
    {
        $user_id = WC()->GQL_User->get_id();
        return \update_user_meta($user_id, 'wc_multiple_shipping_addresses', $addresses);
    }

    public static function editAddress($oldAddressId, $newAddress)
    {
        $addresses = array_values(self::getAddresses());
        $address_index = self::searchForIndex($addresses, $oldAddressId);
        $newAddress['address_id'] = $oldAddressId;
        $addresses[$address_index] = $newAddress;
        return self::save($addresses);
    }

    public static function delete($address_id)
    {
        $addresses = array_values(self::getAddresses());
        $address_index = self::searchForIndex($addresses, $address_id);
        unset($addresses[$address_index]);
        return self::save($addresses);
    }

    public static function revokeDeletedAddress($deletedAddressId)
    {
        $currentAddressId = WC()->GQL_User->getUserMeta('shipping_address');

        if ($deletedAddressId === $currentAddressId) {
            WC()->GQL_User->setUserMeta([
                'shipping_address' => null,
            ]);
            WC()->GQL_User->setUserMeta([
                'billing_address' => null,
            ]);

            return true;
        }

        return false;
    }

    public static function searchForIndex($addresses, $address_id)
    {
        $addressIdsColumn = array_column($addresses, 'address_id');

        $address_index = array_search(strval($address_id), $addressIdsColumn, true);

        if ($address_index === false && is_numeric($address_id)) {
            $address_index = array_search(intval($address_id), $addressIdsColumn, true);
        }

        if ($address_index === false) {
            throw new \Exception("Invalid address id ($address_id)");
        }
        
        return $address_index;
    }
    
    public static function getAddressByID($address_id)
    {
        $addresses = array_values(self::getAddresses());
        $address_index = self::searchForIndex($addresses, $address_id);
        return $addresses[$address_index];
    }

    public static function from_input($input, $prefix = '')
    {
        $address = array(
            'first_name' => Utils::get_prop($input, $prefix . 'firstname'),
            'last_name' => Utils::get_prop($input, $prefix . 'lastname'),
            'country' => Utils::get_prop($input, $prefix . 'country_id'),
            'state' => Utils::get_prop($input, $prefix . 'zone_id'),
            'city' => Utils::get_prop($input, $prefix . 'city'),
            'company' => Utils::get_prop($input, $prefix . 'company'),
            'postcode' => Utils::get_prop($input, $prefix . 'postcode'),
            'address_1' => Utils::get_prop($input, $prefix . 'address_1'),
            'address_2' => Utils::get_prop($input, $prefix . 'address_2'),
            'telephone' => Utils::get_prop($input, $prefix . 'telephone'),
            'custom_field' => Utils::get_prop($input, $prefix . 'custom_field'),
            'default' => Utils::get_prop($input, $prefix . 'default'),
        );

        return $address;
    }

    public static function to_schema($addresses = false, $single = true)
    {
        if ($single) {
            return self::convert($addresses);
        }

        $addresses = array_map(function ($address) {
            return self::convert($address);
        }, $addresses);

        return $addresses;
    }

    private static function convert($address)
    {
        return Utils::map($address, array(
            'firstname' => 'first_name',
            'lastname' => 'last_name',
            'zone' => function ($_address) {
                $zoneId = Utils::get_prop($_address, 'state');
                $zoneName = $zoneId;
                if ($zoneId) {
                    $countryId = Utils::get_prop($_address, 'country');
                    $zoneName = (
                        isset(WC()->countries->states[$countryId]) &&
                        isset(WC()->countries->states[$countryId][$zoneId])
                    )?
                    WC()->countries->states[$countryId][$zoneId]
                    :$zoneId;
                }

                return array(
                    'zone_id' => $zoneId,
                    'name' => $zoneName,
                    'code' => $zoneId,
                );
            },
            'country' => function ($_address) {
                $countryId = Utils::get_prop($_address, 'country');
                $countryName = WC()->countries->countries[$countryId]?: null;

                return array(
                    'country_id' => $countryId,
                    'name' => $countryName,
                );
            },
        ));
    }
}
