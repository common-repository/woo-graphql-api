<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Product
{
    private $product;
    private $name;
    private $description;

    public function __construct($id)
    {
        $this->set_product($id);
    }

    public function getProductFromIdentifier($identifier)
    {
        $handlers = ['isProduct','getById', 'getBySlug'];

        foreach ($handlers as $handler) {
            if (method_exists($this, $handler)) {
                $product = call_user_func(array($this, $handler), $identifier);
                if ($this->isProduct($product)) {
                    return $product;
                }
            }
        }

        return false;
    }

    private function getById($id)
    {
        $product =  wc_get_product($id);
        return ($product instanceof \WC_Product)? $product: null;
    }

    private function getBySlug($slug)
    {
        $_product = get_page_by_path($slug, OBJECT, 'product');
        $product = ($_product)? wc_get_product($_product->ID): null;
        return $this->isProduct($product)? $product: null;
    }

    public function isProduct($product)
    {
        return ($product instanceof \WC_Product)? $product: false;
    }

    public function get_id()
    {
        return $this->product->get_id();
    }

    public function get_name()
    {
        return Utils::either(
            $this->name,
            $this->product->get_name()
        );
    }

    public function set_name($name)
    {
        if (\is_string($name)) {
            $this->name = $name;
        }
    }

    public function get_description()
    {
        return Utils::either(
            $this->description,
            $this->product->get_description()
        );
    }

    public function set_description($description)
    {
        if (\is_string($description)) {
            $this->description = $description;
        }
    }

    public function get_product()
    {
        return $this->product;
    }

    public function set_product($product_id)
    {
        $product = $this->getProductFromIdentifier($product_id);
        if (!$product) {
            throw new \Exception("Invalid product identifier ($product_id)");
        }

        $this->product = $product;
    }

    public static function getProductsOnSale()
    {
        $unique_ids = \wc_get_product_ids_on_sale();
        if (get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\WPMLTranslator") {
            global $sitepress;
            $ids = array_map(function ($id) use ($sitepress) {
                return icl_object_id($id, 'post', true, $sitepress->get_default_language());
            }, \wc_get_product_ids_on_sale());
            $unique_ids = array_unique($ids);
        }
        return array_map(function ($id) {
            return (new ProductsFactory)->get_product($id)->to_schema();
        }, $unique_ids);
    }

    public function get_gallery()
    {
        if (Image::checkFIBUActive()) {
            global $knawatfibu;
            $gallery_images = $knawatfibu->common->knawatfibu_get_wcgallary_meta($this->get_id());
            if (is_array($gallery_images) && count($gallery_images) > 0) {
                $images = [];
                foreach ($gallery_images as $image) {
                    $images[] = [
                        'product_image_id' => $image['url'],
                        'image' => $image['url']
                    ];
                }
                return $images;
            }
        }

        $ids = \array_filter(
            $this->product->get_gallery_image_ids(),
            function ($id) {
                return \is_numeric($id) && \intval($id) > 0;
            }
        );

        return \array_map(function ($id) {
            $image = $product_image_id = Image::image_from_id($id, true);

            return \compact('image', 'product_image_id');
        }, $ids);
    }

    public function isAnyVariationInStock($variableProduct)
    {
        foreach ($variableProduct->get_available_variations() as $variation) {
            if ($variation->is_in_stock()) {
                return true;
            }
        }

        return false;
    }

    public function getRelatedProducts()
    {
        $product_id = $this->get_id();
        $res = \wc_get_related_products($product_id, 10, array($product_id));

        return \array_map(function ($related_product) {
            return (new Product($related_product))->to_schema();
        }, $res);
    }

    public function get_regular_price($min_or_max = 'min')
    {
        $tax_display_mode = get_option('woocommerce_tax_display_shop');
        if ($this->product->is_type('variable')) {
            return 'incl' === $tax_display_mode ? wc_get_price_including_tax($this->product, [ 'price' => $this->product->get_variation_regular_price($min_or_max) ]) : wc_get_price_excluding_tax($this->product, [ 'price' => $this->product->get_variation_regular_price($min_or_max) ]);
        }

        return 'incl' === $tax_display_mode ? wc_get_price_including_tax($this->product, [ 'price' => $this->product->get_regular_price() ]) : wc_get_price_excluding_tax($this->product, [ 'price' => $this->product->get_regular_price() ]);
    }

    public function get_sale_price($min_or_max = 'min')
    {
        $tax_display_mode = get_option('woocommerce_tax_display_shop');
        if ($this->product->is_type('variable')) {
            return 'incl' === $tax_display_mode ? wc_get_price_including_tax($this->product, [ 'price' => $this->product->get_variation_sale_price($min_or_max) ]) : wc_get_price_excluding_tax($this->product, [ 'price' => $this->product->get_variation_sale_price($min_or_max) ]);
        }
        
        return 'incl' === $tax_display_mode ? wc_get_price_including_tax($this->product, [ 'price' => $this->product->get_sale_price() ]) : wc_get_price_excluding_tax($this->product, [ 'price' => $this->product->get_sale_price() ]);
    }
    
    public function to_schema()
    {
        TranslatorsFactory::get_translator()->translate_product($this);
        return array(
            'product' => $this,
            'product_id' => $this->get_id(),
            'name' => $this->get_name(),
            'description' => $this->get_description(),
            'model' => '',
            'quantity' => $this->product->get_stock_quantity(),
            'image' => Image::getMainImage($this->get_id(), $this->product->get_image_id(), true),
            'price' => $this->get_regular_price(),
            'special' => $this->get_sale_price(),
            'in_stock' => $this->product->is_in_stock(),
            'attributes' => [],
            'rating' => is_numeric($this->product->get_average_rating())? $this->product->get_average_rating(): 0,
            'permalink' => get_permalink($this->get_id()),
            'price_suffix' => $this->product->get_price_suffix(),
            'tax_mode' => $this->product->get_tax_status(),
            'is_managing_stock' => $this->product->managing_stock(),
            'is_virtual' => $this->product->is_virtual(),
            'is_downloadable' => $this->product->is_downloadable(),
            'is_featured' => $this->product->is_featured(),
            'is_on_sale' => $this->product->is_on_sale(),
            'is_variable' => $this->product->is_type('variable'),
            'price_min' => $this->get_regular_price('min'),
            'price_max' => $this->get_regular_price('max'),
            'price_sale_min' => $this->get_sale_price('min'),
            'price_sale_max' => $this->get_sale_price('max'),
            'wishlist' => WC()->GQL_Wishlist->wishlistedProductVersion($this->get_id())? true: false
        );
    }
}
