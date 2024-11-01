<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\CategoriesFactory;
use WCGQL\Helpers\ProductsFactory;
use WCGQL\Helpers\Utils;

trait CategoryTypeResolver
{

    public function RootQueryType_categories($root, $args, $ctx)
    {
        $cats = (new CategoriesFactory)
            ->parent(0)
            ->get_categories();

        return array_map(function ($cat) {
            return $cat->to_schema();
        }, $cats);
    }

    public function RootQueryType_category($root, $args, $ctx)
    {
        return (new CategoriesFactory)
            ->get_category(Utils::get_prop($args, 'id'))
            ->to_schema();
    }

    public function CategoryType_parent($root, $args, $ctx)
    {
        $category_id = $root['category_id'];
        $wc_category = \get_term(\intval($category_id), 'product_cat');
        if ($wc_category instanceof \WP_Term) {
            if ($wc_category->parent != 0) {

                $parent_category = new Category($wc_category->parent);
                return $parent_category->to_schema();
            }
        }

        return null;
    }

    public function CategoryType_products($root, $args, $ctx)
    {
        $products = (new ProductsFactory)
                    ->limit(Utils::get_prop($args, 'limit'))
                    ->offset(Utils::get_prop($args, 'start'))
                    ->category(Utils::get_prop($root, 'category_id'))
                    ->order(Utils::get_prop ($args, 'order'))
                    ->sort_by(Utils::get_prop ($args, 'sort'))
                    ->onlyInStock(Utils::get_prop($args, 'instock'))
                    ->onlyPublished()
                    ->onlyDeals(Utils::get_prop($args, 'dealsMode'))
                    ->get_products();

        return array_map(function($product) {
            return $product->to_schema();
        }, $products);
    }

    public function CategoryType_categories($root, $args, $ctx)
    {
        $cats = (new CategoriesFactory)
            ->parent(Utils::get_prop($root, 'category_id'))
            ->get_categories();

        return array_map(function ($cat) {
            return $cat->to_schema();
        }, $cats);
    }

}

?>