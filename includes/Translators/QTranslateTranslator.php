<?php

namespace WCGQL\Translators;

use WCGQL\Helpers\Utils;

class QTranslateTranslator implements ITranslator
{
    private $current_lang_id = '';
    private $languages = array();

    public function __construct()
    {
        global $q_config;
        if (!isset($q_config) || !isset($q_config['enabled_languages'])) {
            return;
        }

        foreach ($q_config['enabled_languages'] as $lang) {
            $this->languages[$lang] = [
                'language_id' => $lang,
                'code' => $lang,
                'name' => $q_config['language_name'][$lang],
                'locale' => $q_config['locale'][$lang],
                'image' => $q_config['flag'][$lang],
            ];
        }

        $currentLanguageId = WC()->GQL_User->get_language_code();
        if (empty($currentLanguageId) || !in_array($currentLanguageId, array_keys($this->languages))) {
            $currentLanguageId = reset($this->languages)['language_id'];
        }

        $this->current_lang_id = $currentLanguageId;
    }

    public static function is_available()
    {
        return defined('QTRANSLATE_FILE');
    }

    public function set_language($language_id)
    {
        if(!isset($this->languages[$language_id])){
            return false;
        }
        $this->current_lang_id = $language_id;
        return WC()->GQL_User->set_language_code($language_id);
    }

    public function get_default_language()
    {
        global $q_config;    
        return Utils::get_prop($this->languages, $q_config['default_language']);
    }

    public function get_language()
    {
        return Utils::get_prop($this->languages, $this->current_lang_id);
    }

    public function get_languages()
    {
        return $this->languages;
    }

    public function translate_product(&$product)
    {
        $name = $this->translate_string($product->get_name());
        $product->set_name($name);
        $description = $this->translate_string($product->get_description());
        $product->set_description($description);

        return $product;
    }

    public function translate_string($string, $args = array())
    {
        if (is_string($string)) {
            return html_entity_decode(\apply_filters('translate_text', $string, $this->current_lang_id));
        }

        return \apply_filters('translate_text', $string->name, $this->current_lang_id);
    }

    public function translate_category(&$category)
    {
        $name = $this->translate_term($category->get_name());
        $category->set_name($name);
        $description = $this->translate_string($category->get_description());
        $category->set_description($description);

        return $category;
    }

    public function translate_term($term, $taxonomy = '')
    {
        if (is_string($term)) {
            return html_entity_decode(\apply_filters('translate_term', $term, $this->current_lang_id));
        }

        return \apply_filters('translate_term', $term, $this->current_lang_id, $taxonomy);
    }

    public function translate_posts_category(&$category)
    {
        $name = $this->translate_string($category->get_name());
        $category->set_name($name);
        return $category;
    }

    public function translate_post(&$post)
    {
        $title = $this->translate_string($post->get_title());
        $post->set_title($title);
        $excerpt = $this->translate_string($post->get_excerpt());
        $post->set_excerpt($excerpt);
        $content = $this->translate_string($post->get_content());
        $post->set_content($content);
        return $post;
    }

    public function getTranslations($elementId, $elementType){
        // TODO: return array of ITranslation
        return [];
    }
}
