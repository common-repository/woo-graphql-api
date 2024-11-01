<?php

namespace WCGQL\Translators;

class DefaultTranslator implements ITranslator
{
    private $current_lang_id = '';

    public function __construct()
    {
    }

    public static function is_available()
    {
        return true;
    }

    public function set_language($language_id)
    {
        $this->current_lang_id = $language_id;
        return true;
    }

    public function get_default_language()
    {
        return array();
    }

    public function get_language()
    {
        return array();
    }

    public function get_languages()
    {
        return array();
    }

    public function translate_string($string, $args = array())
    {
        return $string;
    }

    public function translate_term($string, $taxonomy = '')
    {
        return $string;
    }

    public function translate_product(&$product)
    {
        return $product;
    }

    public function translate_category(&$category)
    {
        return $category;
    }

    public function translate_posts_category(&$category)
    {
        return $category;
    }

    public function translate_post(&$post)
    {
        return $post;
    }

    public function getTranslations($elementId, $elementType){
        // TODO: return array of ITranslation
        return [];
    }
}
