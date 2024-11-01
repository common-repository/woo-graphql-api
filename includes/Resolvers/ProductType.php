<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Product;
use WCGQL\Helpers\Variation;
use WCGQL\Helpers\ProductsFactory;
use WCGQL\Helpers\Review;
use WCGQL\Helpers\Utils;
use WCGQL\Translators\TranslatorsFactory;

trait ProductTypeResolver
{
    public function RootQueryType_product($root, $args, $ctx)
    {
        return (new ProductsFactory)
            ->get_product(Utils::get_prop($args, 'id'))
            ->to_schema();
    }

    public function RootQueryType_products($root, $args, $ctx)
    {
        $products = array();
        if (isset($args['filter_name']) && !empty($args['filter_name']) && get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\WPMLTranslator") { 
            global $sitepress;
            $allLanguages = TranslatorsFactory::get_translator()->get_languages();
            foreach ($allLanguages as $language) {
                $sitepress->switch_lang($language['code'], true);
                $_products = (new ProductsFactory)
                    ->limit(Utils::get_prop($args, 'limit'))
                    ->offset(Utils::get_prop($args, 'start'))
                    ->search_term(Utils::get_prop($args, 'filter_name'))
                    ->category(Utils::get_prop($args, 'filter_category_id'))
                    ->order(Utils::get_prop($args, 'order'))
                    ->sort_by(Utils::get_prop($args, 'sort'))
                    ->onlyInStock(Utils::get_prop($args, 'instock'))
                    ->onlyPublished()
                    ->onlyDeals(Utils::get_prop($args, 'dealsMode'))
                    ->get_products();
                $products = array_merge($_products, $products);
            }
        } else {
            $products = (new ProductsFactory)
                ->limit(Utils::get_prop($args, 'limit'))
                ->offset(Utils::get_prop($args, 'start'))
                ->search_term(Utils::get_prop($args, 'filter_name'))
                ->category(Utils::get_prop($args, 'filter_category_id'))
                ->order(Utils::get_prop($args, 'order'))
                ->sort_by(Utils::get_prop($args, 'sort'))
                ->onlyInStock(Utils::get_prop($args, 'instock'))
                ->onlyPublished()
                ->onlyDeals(Utils::get_prop($args, 'dealsMode'))
                ->get_products();
        }

        return array_map(function ($product) {
            return $product->to_schema();
        }, $products);
    }

    public function RootQueryType_relatedProducts($root, $args, $ctx)
    {
        return (new Product($args['id']))->getRelatedProducts();
    }

    public function RootQueryType_productSpecials($root, $args, $ctx)
    {
        return Product::getProductsOnSale();
    }

    public function MutationType_addReview($root, $args, $ctx)
    {
        $product_id = $args['product_id'];
        return Review::addReview($product_id, $args['input']);
    }

    public function RootQueryType_reviews($root, $args, $ctx)
    {
        $product_id = $args['product_id'];
        return Review::getReviews($product_id);
    }

    public function ProductType_wishlist($root, $args, $ctx)
    {
        return (new Product($root['product_id']))->isInWishlist();
    }

    public function ProductType_manufacturer($root, $args, $ctx)
    {
        return null;
    }

    public function ProductType_attributes($root, $args, $ctx)
    {
        return $root['attributes'];
    }

    public function ProductType_options($root, $args, $ctx)
    {
        $product = $root['product']->get_product();
        return Variation::getAvailableOptions($product, []);
    }

    public function ProductType_images($root, $args, $ctx)
    {
        return $root['product']->get_gallery();
    }
}
