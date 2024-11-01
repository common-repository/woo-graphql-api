<?php

namespace WCGQL\Translators;

use WCGQL\Helpers\CategoriesFactory;
use WCGQL\Helpers\Post;
use WCGQL\Helpers\PostsCategory;
use WCGQL\Helpers\ProductsFactory;
use WCGQL\Helpers\Utils;

class WPMLTranslator implements ITranslator
{
    private $current_language_id = '';
    private $langauges = array();
    private $trid_types = [
        'product' => 'post_post',
        'category' => 'tax_category'
    ];

    private $post_types = [
        'product' => 'post',
        'category' => 'product_cat'
    ];

    public function __construct()
    {
        $languages = apply_filters('wpml_active_languages', null, array());
        foreach ($languages as $code => $language) {
            $this->languages[$code] = array(
                'language_id' => $code,
                'code' => $code,
                'name' => $language['native_name'],
                'locale' => $code,
                'image' => $language['country_flag_url'],
            );
        }

        $currentLanguageId = WC()->GQL_User->get_language_code();
        if (empty($currentLanguageId) || !in_array($currentLanguageId, array_keys($this->languages))) {
            $currentLanguageId = reset($this->languages)['language_id'];
        }

        $this->current_language_id = $currentLanguageId;
    }

    public static function is_available()
    {
        return \defined('ICL_LANGUAGE_CODE');
    }

    public function set_language($language_id)
    {
        if(!isset($this->languages[$language_id])){
            return false;
        }
        $this->current_language_id = $language_id;
        return WC()->GQL_User->set_language_code($language_id);
    }

    public function get_default_language()
    {
        global $sitepress;
        return Utils::get_prop($this->languages, $sitepress->get_default_language());
    }

    public function get_language()
    {
        return Utils::get_prop($this->languages, $this->current_language_id);
    }

    public function get_languages()
    {
        return $this->languages;
    }

    public function translate_string($string, $args = array())
    {
        return apply_filters('wpml_translate_single_string', $string, $args['domain']?? null, $args['name']?? null, $this->current_language_id);
    }
    
    public function translate_term($term, $taxonomy = '')
    {
        $new_id = $this->get_translated_id($term->term_id, $taxonomy);

        if ($new_id === $term->term_id) {
            return $term;
        }

        global $sitepress;
        remove_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1, 1);
        $foundTerm = \get_term(\intval($new_id), $taxonomy);
        add_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1, 1);

        return $foundTerm;
    }

    private function get_translated_id($old_id, $taxonomy = 'post')
    {
        $new_id = apply_filters(
            'wpml_object_id',
            $old_id,
            $taxonomy,
            true,
            $this->current_language_id
        );

        if (!\is_numeric($new_id)) {
            return $old_id;
        }

        return $new_id;
    }

    public function translate_product(&$product)
    {
        $new_id = $this->get_translated_id($product->get_id(), 'post');
        if ($new_id === $product->get_id()) {
            return $product;
        }

        $new_product = (new ProductsFactory)->get_product($new_id);
        $product->set_product($new_id);
        $product->set_name($new_product->get_name());
        $product->set_description($new_product->get_description());
        return $new_product;
    }

    public function translate_category(&$category)
    {
        $new_id = $this->get_translated_id($category->get_id(), 'product_cat');
        if ($new_id === $category->get_id()) {
            return $category;
        }

        $new_cat = (new CategoriesFactory)->get_category($new_id);
        $category->set_name($new_cat->get_name());
        $category->set_description($new_cat->get_description());
        return $category;
    }

    public function translate_posts_category(&$category)
    {
        $new_id = $this->get_translated_id($category->get_id(), 'category');

        if ($new_id === $category->get_id()) {
            return $category;
        }

        $new_cat = new PostsCategory($new_id);
        $category->set_name($new_cat->get_name());
        return $category;
    }

    public function translate_post(&$post)
    {
        $new_id = $this->get_translated_id($post->get_id(), 'post');
        if ($new_id === $post->get_id()) {
            return $post;
        }

        $new_post = new Post($new_id);
        $post->set_title($new_post->get_title());
        $post->set_excerpt($new_post->get_excerpt());
        $post->set_content($new_post->get_content());
        return $post;
    }

    public function getTranslations($elementId, $elementType)
    {
        $trid = apply_filters( 'wpml_element_trid', NULL, $elementId, $this->trid_types[$elementType] );
        $translations = apply_filters( 'wpml_get_element_translations', NULL, $trid, $this->post_types[$elementType] );
        return array_map(function($translation){
            return new WPMLTranslation($translation);
        }, $translations);
    }
}
