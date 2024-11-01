<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Image;
use WCGQL\Helpers\Product;
use WCGQL\Helpers\Utils;
use WCGQL\Helpers\Variation;
use WCGQL\Translators\TranslatorsFactory;

trait CartTypeResolver
{
    public function CartType_totals($root, $args, $ctx)
    {
        $totals = [];
        foreach (WC()->cart->get_totals() as $key => $value) {
            if (is_array($value)) {
                continue;
            }

            $totals[] = [
            'code' => $key,
            'title' => $key,
            'value' => $value,
            'sort_order' => 0
          ];
        }

        return $totals;
    }

    public function CartType_fees($root, $args, $ctx)
    {
        if (! defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        WC()->cart->calculate_totals();

        $fees = [];
        foreach (WC()->cart->get_fees() as $key => $fee) {
            $fees[] = [
            'code' => urldecode($key),
            'title' => $fee->name,
            'value' => $fee->amount,
            'sort_order' => 0
          ];
        }

        return $fees;
    }

    public function CartType_items($root, $args, $ctx)
    {
        $items = array();
        $tax_display_mode = get_option('woocommerce_tax_display_cart');

        foreach (WC()->cart->get_cart() as $key => $product) {
            $cart_id = $key;
            $product_id = Utils::get_prop($product, 'product_id');
            $productSchema = new Product($product_id);
            TranslatorsFactory::get_translator()->translate_product($productSchema);

            $data = Utils::get_prop($product, 'data');
            $product_image = Image::image_from_id($data->get_image_id(), true);
            $quantity = Utils::get_prop($product, 'quantity');
            $productPrice = 'incl' === $tax_display_mode ? wc_get_price_including_tax($data, [ 'price' => $data->get_price() ]) : wc_get_price_excluding_tax($data, [ 'price' => $data->get_price() ]);
            $regular_price = 'incl' === $tax_display_mode ? wc_get_price_including_tax($data, [ 'price' => $data->get_regular_price() ]) : wc_get_price_excluding_tax($data, [ 'price' => $data->get_regular_price() ]);
            $sale_price = 'incl' === $tax_display_mode ? wc_get_price_including_tax($data, [ 'price' => $data->get_sale_price() ]) : wc_get_price_excluding_tax($data, [ 'price' => $data->get_sale_price() ]);
           
            $productTotal = 'incl' === $tax_display_mode ? wc_get_price_including_tax($data, [ 'price' => $data->get_price(), 'qty' => $quantity ]) : wc_get_price_excluding_tax($data, [ 'price' => $data->get_price(), 'qty' => $quantity ]);

            $items[] = array(
                'cart_id' => $cart_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'total' => $productTotal,
                'name' => $productSchema->get_name(),
                'image' => $product_image,
                'price'     => $productPrice,
                'regular_price' => $regular_price,
                'sale_price' => $sale_price,
                'weight' => (int)$data->get_weight(),
                'height' => (int)$data->get_height(),
                'width' => (int)$data->get_width(),
                'length' => (int)$data->get_length(),
                'stock' => (int)$data->get_stock_quantity(),
                'tax_mode'  => $data->get_tax_status(),
                'in_stock' => $data->is_in_stock(),
                'is_managing_stock' => $data->managing_stock(),
                'wishlist' => WC()->GQL_Wishlist->wishlistedProductVersion($product_id)? true: false,
                'options' => Variation::getProductCartItemOptions($productSchema->get_product(), $data->get_attributes()),
                'line_tax' => $product['line_tax']
            );
        }
        
        return $items;
    }
}
