<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Address;
use WCGQL\Helpers\ClientException;
use WCGQL\Helpers\Country;
use WCGQL\Helpers\Utils;

trait AddressTypeResolver
{
    public function MutationType_addAddress($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new ClientException('Not authorized');
        }

        $address = Address::from_input($args['input']);
        return Address::addAddress($address);
    }

    public function MutationType_editAddress($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new ClientException('Not authorized');
        }

        $address = Address::from_input($args['input']);
        return Address::editAddress($args['address_id'], $address);
    }

    public function MutationType_deleteAddress($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new ClientException('Not authorized');
        }

        $isDeleted = Address::delete($args['address_id']);

        if ($isDeleted) {
            Address::revokeDeletedAddress($args['address_id']);
        }

        return $isDeleted;
    }

    public function RootQueryType_address($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new ClientException('Not authorized');
        }

        $addressId = $args['id'];
        $address = Address::getAddressByID($addressId);
        return Address::to_schema($address);
    }

    public function RootQueryType_addresses($root, $args, $ctx)
    {
        if (!\is_user_logged_in()) {
            throw new ClientException('Not authorized');
        }

        $addresses = Address::getAddresses();
        return Address::to_schema($addresses, false);
    }

    public function RootQueryType_zones($root, $args, $ctx)
    {
        $zones = \WC_Shipping_Zones::get_zones();
        $res = [];
        if (!is_array($zones) || empty($zones)) {
            return $res;
        }

        foreach ($zones as $zone) {
            $res[] = [
                'zone_id' => Utils::get_prop($zone, 'zone_id'),
                'name' => Utils::get_prop($zone, 'zone_name'),
                'code' => Utils::get_prop($zone, 'id'),
            ];
        }
        return $res;
    }

    public function RootQueryType_zone($root, $args, $ctx)
    {
        $zoneId = $args['id'];
        $zone = \WC_Shipping_Zones::get_zone($args['id']);
        if (!isset($zone) || empty($zone)) {
            throw new \Exception("Invalid zone id ($zoneId)");
        }

        return [
            'zone_id' => $zone->get_id(),
            'name' => $zone->get_zone_name(),
            'code' => $zone->get_id(),
        ];
    }

    public function RootQueryType_country($root, $args, $ctx)
    {
        $country_id = $args['id'];
        return Country::getCountryByID($country_id);
    }

    public function RootQueryType_countries($root, $args, $ctx)
    {
        $countries = WC()->countries->get_allowed_countries();
        $res = [];
        foreach ($countries as $key => $val) {
            $res[] = Country::getCountryByID($key);
        }
        return $res;
    }

    public function RootQueryType_states($root, $args, $ctx)
    {
        $country_id = $args['country_id']?? $root['country_id'];
        $states = array();
        if (!isset(WC()->countries->states[$country_id])) {
            return $states;
        }
        
        foreach (WC()->countries->states[$country_id] as $state_code => $state_name) {
            $states[] = [
                'state_id' => $state_code,
                'name' => $state_name,
            ];
        }
        return $states;
    }

    public function AddressType_zone($root, $args, $ctx)
    {
        return isset($root['zone'])? $root['zone']: null;
    }

    public function AddressType_country($root, $args, $ctx)
    {
        return isset($root['country'])? $root['country']: null;
    }
}
