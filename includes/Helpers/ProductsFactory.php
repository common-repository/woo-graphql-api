<?php

namespace WCGQL\Helpers;

class ProductsFactory
{
    /** @var array */
    private $args = array();

    /**
     * returns a product.
     *
     * @param int $id
     * @return \WCGQL\Helpers\Product
     */
    public function get_product($id = 0)
    {
        return new Product($id);
    }

    /**
     * Sets maximum number of products to return.
     *
     * @param int $limit
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function limit($limit)
    {
        if (\is_numeric($limit)) {
            $this->args['limit'] = \intval($limit);
        }

        return $this;
    }

    /**
     * Sets the offset for the products batch.
     *
     * @param int $offset
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function offset($offset)
    {
        if (\is_numeric($offset)) {
            $this->args['offset'] = \intval($offset);
        }

        return $this;
    }

    /**
     * Sets the sort key for the products batch.
     *
     * @param string $by
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function sort_by($by)
    {
        $order = isset($this->args['order']) ? $this->args['order'] : 'DESC';
        $query_args = [
            'orderby' => $by,
            'order' => $order,
        ];
        $ordering_args = WC()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
        $this->args['orderby'] = $ordering_args['orderby'];
        $this->args['order'] = $ordering_args['order'];
        if ($ordering_args['meta_key']) {
            $this->args['meta_key'] = $ordering_args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        }

        return $this;
    }

    /**
     * Sets the sort order for the products batch.
     *
     * @param string $order
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function order($order)
    {

        if (\is_string($order)) {
            $this->args['order'] = strval($order);
        }

        return $this;

    }

    /**
     * Sets whether or not to fetch only products on sale.
     *
     * @param bool $on_sale
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function on_sale($on_sale)
    {
        if (\is_bool($on_sale)) {
            $args['on_sale'] = $on_sale;
        }

        return $this;
    }

    /**
     * Sets the search term.
     *
     * @param string $term
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function search_term($term)
    {
        if (\is_string($term)) {
            $this->args['s'] = strval($term);
        }

        return $this;
    }

    /**
     * Sets the parent category for the products.
     *
     * @param int $category
     * @return \WCGQL\Helpers\ProductsFactory
     */
    public function category($category)
    {
        if (\is_numeric($category)) {
            $cat_slug = $this->get_category_slug($category);
            $this->args['category'] = array($cat_slug);
        }
        return $this;
    }

    public function onlyInStock($inStockOnly)
    {
        if($inStockOnly){
            $this->args['meta_key'] = '_stock_status';
            $this->args['meta_value'] = 'instock';
        }

        return $this;
    }

    public function onlyPublished()
    {
        $this->args['status'] = 'publish';
        return $this;
    }

    public function onlyOnSale()
    {
        return wc_get_product_ids_on_sale();
    }

    public function onlyFeatured()
    {
        return wc_get_featured_product_ids();
    }

    public function onlyDeals($dealsMode)
    {
        if(!$dealsMode || $dealsMode === 'none')
            return $this;
        
        $products = array();
        switch($dealsMode){
            case 'onSale':
                $products = array_merge($products, $this->onlyOnSale());
                break;
            case 'featured':
                $products = array_merge($products, $this->onlyFeatured());
                break;
            case 'all':
                $products = array_merge($products, $this->onlyOnSale(), $this->onlyFeatured());
                break;
            default:return $this;
        }
        
        $this->args['include'] = $products;
    
        return $this;
    }

    /**
     * returns a category slug given its id.
     *
     * @param int $id
     * @return string
     */
    private function get_category_slug($id)
    {
        return (new CategoriesFactory)->get_category($id)->get_slug();
    }

    /**
     * return a list of products given an arguments array.
     *
     * @return \WCGQL\Helpers\Product[]
     */
    public function get_products()
    {
        if(isset($this->args['include']) &&
            count($this->args['include']) === 0) return [];

        $products = wc_get_products($this->args);
        return array_map(function ($product) {
            return new Product($product);
        }, $products);
    }
}
