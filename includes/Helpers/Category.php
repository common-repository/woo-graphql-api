<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Category
{
    private $category;
    private $name;
    private $description;

    public function __construct($category)
    {
        // WPML getting translation
        global $sitepress;
        if (\is_numeric($category) && isset($sitepress)) {
            remove_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1, 1);
            $this->category = \get_term(\intval($category), 'product_cat');
            add_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1, 1);
        } else if (\is_numeric($category)) {
            $this->category = \get_term(\intval($category), 'product_cat');
            if (!$this->category instanceof \WP_Term) {
                throw new ClientException("Category ($category) was not found.");
            }
        } else if ($category instanceof \WP_Term) {
            $this->category = $category;
        } else {
            throw new \Exception('Category expects \WP_TERM or category id as parameter.');
        }
    }

    public function get_slug()
    {
        return $this->category->slug;
    }

    public function to_schema()
    {
        TranslatorsFactory::get_translator()->translate_category($this);

        return array(
            'category_id' => $this->get_id(),
            'name' => $this->get_name(),
            'description' => $this->get_description(),
            'products_count' => Utils::get_prop($this->category, 'count'),
            'image' => $this->get_image()
        );
    }

    public function get_id()
    {
        return $this->category->term_id;
    }

    public function get_name()
    {
        return Utils::either(
            $this->name,
            $this->category->name
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
            $this->category->description
        );
    }

    public function set_description($description)
    {
        if (\is_string($description)) {
            $this->description = $description;
        }
    }

    private function get_image()
    {
        $thumbnail_id = get_term_meta(
            Utils::get_prop($this->category, 'term_id'),
            'thumbnail_id',
            true
        );

        $image = wp_get_attachment_url($thumbnail_id);
        return $image ? $image : '';
    }
}
