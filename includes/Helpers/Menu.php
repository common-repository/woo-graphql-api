<?php

namespace WCGQL\Helpers;

class Menu
{
    private $items = array();

    public function __construct($name = '')
    {
        if (!\is_string($name) || empty($name)) {
            $name = get_option('wc_gql_default_menu');
        }

        $items = \wp_get_nav_menu_items($name);

        if (\is_array($items)) $this->items = $items;
        else $this->items = array();
    }

    public function to_schema()
    {
        return array_map(function ($item) {
            return array(
                'item_id' => Utils::get_prop($item, 'ID'),
                'object_id' => Utils::get_prop($item, 'object_id'),
                'object_type' => Utils::get_prop($item, 'object'),
                'url' => Utils::get_prop($item, 'url'),
                'title' => Utils::get_prop($item, 'title'),
                'order' => Utils::get_prop($item, 'menu_order'),
            );
        }, $this->items);
    }
}
