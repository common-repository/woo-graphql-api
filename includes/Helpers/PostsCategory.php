<?php

namespace WCGQL\Helpers;

class PostsCategory
{
    /** @var \WP_Term */
    private $category;
    /** @var string */
    private $name;

    /**
     * Creates a new Wordpress Category.
     *
     * @param int|WP_Term $category
     */
    public function __construct($category)
    {
        if (\is_numeric($category)) {
            $this->category = \get_category($category);
        } else if ($category instanceof \WP_Term) {
            $this->category = $category;
        } else {
            throw new \Exception("WPCategory expects category id or WP_Term object as input '$category' found.");
        }
    }

    /** @return array */
    public function schema_get_posts()
    {
        return array_map(function ($post) {
            return (new Post($post))->to_schema();
        }, \get_posts(array('category' => Utils::get_prop($this->category, 'cat_ID'))));
    }

    /** @return array */
    public function schema_get_parent()
    {
        return (new PostsCategory(Utils::get_prop($this->category, 'category_parent')))->to_schema();
    }

    public function to_schema()
    {
        \WCGQL\Translators\TranslatorsFactory::get_translator()->translate_posts_category($this);
        return array(
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'count' => Utils::get_prop($this->category, 'count'),
            'ref' => $this,
        );
    }

    public function get_id()
    {
        return Utils::get_prop($this->category, 'cat_ID');
    }

    /** @return string */
    public function get_name()
    {
        return Utils::either($this->name, Utils::get_prop($this->category, 'cat_name'));
    }

    /** @param string $name */
    public function set_name($name)
    {
        if (\is_string($name)) {
            $this->name = $name;
        }
    }
}
