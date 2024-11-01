<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Wishlist
{
    private $id;
    private $product_ids;
    private $products;

    public function __construct()
    {
        $user = WC()->GQL_User;
        $id = $user->get_id();
        if (!$id) {
            $this->id = false;
            return;
        }

        $this->id = $user->getUserMeta('wc_gql_wish');

        if (!$this->id) {
            $wish_list = uniqid('wish');
            set_transient(
                $wish_list,
                array(),
                3 * MONTH_IN_SECONDS
            );

            if ($user->setUserMeta(
                array('wc_gql_wish' => $wish_list)
            )) {
                $this->id = $wish_list;
            }
        }

        $this->get_product_ids();
    }

    public function get_product_ids()
    {
        if (is_array($this->product_ids)) {
            return $this->product_ids;
        }

        if (!$this->get_id()) {
            $this->product_ids = array();
        } else {
            $res = \get_transient($this->get_id());
            if (is_array($res)) {
                $this->product_ids = $res;
            } else {
                $this->product_ids = array();
            }
        }

        return $this->product_ids;
    }

    public function set_product_ids($ids)
    {
        $ids = array_unique($ids);

        if ($ids === $this->product_ids) {
            return true;
        }

        $this->product_ids = $ids;
        return $this->save();
    }

    public function get_id()
    {
        return $this->id;
    }

    public function save()
    {
        if (!$this->get_id()) {
            return false;
        }

        $res = \set_transient(
            $this->get_id(),
            $this->get_product_ids(),
            3 * MONTH_IN_SECONDS
        );

        return isset($res) && !!$res;
    }

    public function has_product($product_id)
    {
        return in_array($product_id, $this->get_product_ids());
    }

    private function validateProductId($product_id)
    {
        if (!$this->get_id()) {
            return false;
        }
        $this->tryCreatingProductWithId($product_id);

        return $product_id;
    }

    public function tryCreatingProductWithId($product_id)
    {
        new Product($product_id);
    }

    public function wishlistedProductVersion($product_id)
    {
        $products = TranslatorsFactory::get_translator()->getTranslations($product_id, 'product');
     
        if (count($products) === 0) {
            return $this->has_product($product_id)? $product_id: null;
        }

        foreach ($products as $product) {
            if ($this->has_product($product->getElementId())) {
                return $product->getElementId();
            }
        }
    
        return null;
    }

    public function add_product($product_id)
    {
        $this->validateProductId($product_id);
        if (!$product_id) {
            throw new \Exception("Can't add to wishlist, invalid product id ($product_id)");
        }

        $ids = $this->get_product_ids();
        $ids[] = $product_id;
        return $this->set_product_ids($ids);
    }

    public function delete_product($product)
    {
        $product_id = $this->validateProductId($product);
        if (!$product_id) {
            throw new \Exception("Can't delete from wishlist, invalid product id ($product_id)");
        }

        $ids = $this->get_product_ids();
        $wishlistedId = $this->wishlistedProductVersion($product_id);

        if ($wishlistedId) {
            foreach ($ids as $key => $val) {
                if ($val == $wishlistedId) {
                    unset($ids[$key]);
                }
            }
        }

        return $this->set_product_ids($ids);
    }

    public function to_schema()
    {
        $ids = $this->get_product_ids();

        return array_map(function ($id) {
            return (new Product($id))->to_schema();
        }, $ids);
    }
}
