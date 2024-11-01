<?php

namespace WCGQL\Helpers;

class Coupon
{
    public static function addCoupon($code)
    {
        if (!isset($code) || !WC()->cart->add_discount($code)) {
            return false;
        }

        return WC()->GQL_User->setUserMeta([
            'coupon' => $code
        ]);
    }

    public static function removeCoupon()
    {
        WC()->cart->remove_coupons();
        WC()->cart->calculate_totals();
        WC()->GQL_User->setUserMeta([
            'coupon' => ''
        ]);
        return true;
    }
}
