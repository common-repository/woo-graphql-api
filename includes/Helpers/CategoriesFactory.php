<?php

namespace WCGQL\Helpers;

class CategoriesFactory
{
    private $args = array('taxonomy' => 'product_cat');

    /**
     * returns a Category.
     *
     * @param int $id
     * @return WCGQL\Helpers\Category
     */
    public function get_category($id = 0)
    {
        if (\is_numeric($id) && $id > 0) {
            return new Category($id);
        } 
        
        $category = get_term_by( 'slug', $id, 'product_cat' );
        if($category){
            return new Category($category->term_id);
        }

        throw new \Exception("Expected order id found '$id'.");
    }

    /**
     * Sets the parent of the categories.
     *
     * @param int $parent
     * @return \WCGQL\Helpers\CategoriesFactory
     */
    public function parent($parent)
    {
        if (\is_numeric($parent)) {
            $this->args['parent'] = intval($parent);
        }

        return $this;
    }

    /**
     * return a list of categories.
     *
     * @return \WCGQL\Helpers\Category[]
     */
    public function get_categories()
    {
        $cats = \get_terms($this->args);
        return array_map(function ($cat) {
            return new Category($cat);
        }, $cats);
    }
}
