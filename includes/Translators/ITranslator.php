<?php

namespace WCGQL\Translators;

/**
 * interface ITranslator
 *
 * the common interface for translator helpers.
 */
interface ITranslator
{
    /**
     * @return bool returns true if the translation method is available.
     */
    public static function is_available();

    /**
     * Sets the language that will be used in each consecutive call.
     *
     * @param string $code - language code.
     * @return bool
     */
    public function set_language($language_id);

    /**
     * Gets the default langauge code.
     *
     * @return array Current language associative array schema-ready
     */
    public function get_default_language();

    /**
     * Gets the current langauge object.
     *
     * @return array Current language associative array schema-ready
     */
    public function get_language();

    /**
     * gets an array of enabled languages
     * @return array - array of languages schema-ready
     */
    public function get_languages();

    /**
     * translates a single string.
     *
     * @param string $string - the string to be translated.
     * @param array $args (optional)
     * @return string the translation
     */
    public function translate_string($string, $args = array());

    /**
     * translates a single term string.
     *
     * @param string $string - the string to be translated.
     * @param string $taxonomy - the term taxonomy.
     * @return string the translation
     */
    public function translate_term($string, $taxonomy = '');

    /**
     * Translate Product Object.
     *
     * @param Product $product
     * @return Product translated product object.
     */
    public function translate_product(&$product);

    /**
     * Translate Category Object.
     *
     * @param Category $category
     * @return Category translated category object.
     */
    public function translate_category(&$category);

    /**
     * Translates a WordPress post object.
     *
     * @param \WCGQL\Helpers\WPPost $post
     * @return \WCGQL\Helpers\WPPost
     */
    public function translate_post(&$post);

    /**
     * Translates a WordPress Category object.
     *
     * @param \WCGQL\Helpers\WPCategory $category
     * @return \WCGQL\Helpers\WPCategory
     */
    public function translate_posts_category(&$category);

    /**
     * Get translations of an element.
     *
     * @param string $elementId
     * @param string $elementType
     * @return ITranslation[] $translations
     */
    public function getTranslations($elementId, $elementType);
}
