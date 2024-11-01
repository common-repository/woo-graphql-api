<?php

namespace WCGQL\Helpers;

class Currency
{

    public static function getCurrency($currency_code)
    {
        $all_currencies = get_woocommerce_currencies();
        $symbol = get_woocommerce_currency_symbol($currency_code);
        $position = get_option('woocommerce_currency_pos');

        $currency = [
            'code' => $currency_code,
            'title' => $all_currencies[$currency_code],
            'currency_position' => $position,
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimal_separator' => wc_get_price_decimal_separator(),
            'number_of_decimals' => wc_get_price_decimals()
        ];

        if (strstr($position, 'left')) {
            $currency['symbol_left'] = html_entity_decode($symbol);
        } else {
            $currency['symbol_right'] = html_entity_decode($symbol);
        }

        return $currency;
    }
}