<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Variation
{
    public static function getAvailableOptions($product, $selectedOptions = array())
    {
        $_product = new Product($product);
        TranslatorsFactory::get_translator()->translate_product($_product);
        $product = $_product->get_product();

        if (!$product->is_type('variable')) {
            return [];
        }

        $currentlySelected = self::createOptionsEncodingCases($selectedOptions);

        $selectedOptionsCases = self::createKeyValuePairsCoveringEncodingCases($currentlySelected);

        $res_prep = self::baseAvailableOptionsStructure($product, $currentlySelected);

        $variations = $product->get_available_variations();

        $variations = array_filter($variations, function ($variation) {
            return is_array($variation['attributes']) && $variation['is_in_stock'];
        });

        foreach ($variations as $variation) {
            if (!self::checkIfVariationValidForCurrentSelection($variation, $selectedOptionsCases, count($currentlySelected))) {
                continue;
            }

            self::setVariationAttributeValueInStockStatus($res_prep, $variation);
        }

        return self::prepareAvailableOptionsResponse($res_prep);
    }

    private static function createOptionsEncodingCases($options)
    {
        $currentlySelected = array();
        foreach ($options as $option) {
            $currentlySelected[] = [
                'names' => array_unique(array($option['product_option_id'], strtolower(urlencode($option['product_option_id'])))),
                'values' => array_unique(array($option['value'], strtolower(urlencode($option['value']))))
            ];
        }
        return $currentlySelected;
    }

    private static function createKeyValuePairsCoveringEncodingCases($currentlySelected)
    {
        $selectedOptionsCases = array();
        foreach ($currentlySelected as $attribute) {
            foreach ($attribute['names'] as $k1 => $v1) {
                $selectedOptionsCases['attribute_' . $v1] = $attribute['values'];
            }
        }
        return $selectedOptionsCases;
    }

    private static function baseAvailableOptionsStructure($product, $currentlySelected)
    {
        // Acquire all product attributes and their values
        $variation_attributes = $product->get_variation_attributes();

        // Remove selected attributes so that they are not sent back
        self::removeSelectedAttibutes($variation_attributes, $currentlySelected);

        $res_prep = array();
        foreach ($variation_attributes as $attribute_name => $attribute_values) {
            $values = array();
            $names = self::textEncodingCases($attribute_name);

            foreach ($attribute_values as $value) {
                $_value = self::textEncodingCases($value);
                $_value['in_stock'] = false;
                $values[$value] = $_value;
            }

            $res_prep[] = [
                'names' => $names,
                'values' => $values,
            ];
        }

        return $res_prep;
    }

    private static function removeSelectedAttibutes(&$allAttributes, $currentlySelected)
    {
        if(count($currentlySelected) == 0 ) return;

        $productAttributes = array_keys($allAttributes);
        $selectedAttributes = array_merge(...array_column($currentlySelected, 'names')); // check for backward compatibility (Spread Operator)
        foreach($productAttributes as $key){
            if(in_array($key, $selectedAttributes)){
                unset($allAttributes[$key]);
            }
        }
    }

    private static function textEncodingCases($text, $case = null)
    {
        $textCases = [
            'name' => $text,
            'original' => $text,
            'decoded' => urldecode($text),
            'encoded' => strtolower(urlencode($text)),
        ];
        if (!$case) {
            return $textCases;
        }
        return $textCases[$case];
    }

    private static function checkIfVariationValidForCurrentSelection($variation, $selectedOptionsCases, $selectedAttributesCount)
    {
        $commonAttributes = array_intersect_key($selectedOptionsCases, $variation['attributes']);
        if (count($commonAttributes) != $selectedAttributesCount) {
            return false;
        }
        foreach ($commonAttributes as $attribute_name => $valueTextCases) {
            if (!in_array($variation['attributes'][$attribute_name], $valueTextCases)) {
                return false;
            }
        }

        return true;
    }

    private static function setVariationAttributeValueInStockStatus(&$attributeStore, $variation)
    {
        foreach ($variation['attributes'] as $key => $value) {
            $search_result = self::search($attributeStore, $value);

            if ($search_result) {
                $path_to_value = $search_result['path'];

                $attributeStore[$path_to_value[0]][$path_to_value[1]][$path_to_value[2]]['in_stock'] = true;
            }
        }
    }

    private static function search($array, $searchKey = '')
    {
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($array),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $key => $value) {
            if (strcmp($key, $searchKey) == 0) {
                $keys = array($key);
                for ($i = $iter->getDepth() - 1; $i >= 0; $i--) {
                    array_unshift($keys, $iter->getSubIterator($i)->key());
                }
                return array('path' => $keys, 'value' => $value);
            }
        }
        return false;
    }

    private static function prepareAvailableOptionsResponse($res_prep)
    {
        $res = array();
        foreach ($res_prep as $index => $attribute) {
            $vals = array();
            foreach ($attribute['values'] as $value) {
                $translated_term = self::translateAttributeValue($attribute['names']['original'], $value['name']);

                $vals[] = [
                    // slug value of attribute value
                    'product_option_value_id' => $value['decoded'],
                    // attribute value in designated language
                    'name' => $translated_term,
                    'in_stock' => $value['in_stock'],
                ];
            }

            $taxonomy = str_replace('attribute_', '', $attribute['names']['original']);
            $name = wc_attribute_label($taxonomy);
            $res[] = [
                'product_option_id' => $attribute['names']['decoded'],
                'name' => $name,
                'product_option_value' => $vals,
                'type' => 'radio',
                'value' => '',
                'required' => true,
            ];
        }
        return $res;
    }

    private static function translateAttributeValue($attributeName, $value)
    {
        $defaultName = $value;
        $term_name = \get_term_by('slug', $value, $attributeName);

        if(!$term_name){
            return $defaultName;
        }

        if (get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\QTranslateTranslator") {
            if(is_object($term_name)){
                $term_name = $term_name->name;
            }

            if(!stristr($term_name, '[:]')){
                return TranslatorsFactory::get_translator()->translate_term($term_name);
            }

            return TranslatorsFactory::get_translator()->translate_string($term_name);
        } elseif (get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\WPMLTranslator"
            || get_class(TranslatorsFactory::get_translator()) == "WCGQL\\Translators\\DefaultTranslator") {
            return TranslatorsFactory::get_translator()->translate_term($term_name, $term_name->taxonomy)->name;
        }
    }

    public static function getVariationPrice($product, $options)
    {
        $variation = self::get_variation($product, $options);
        if (empty($variation)) {
            if (empty($product) || !$product) {
                return null;
            }

            return array('price' => $product->get_price());
        }
        return array('price' => Utils::get_prop($variation, 'display_price'));
    }

    public static function get_variation($product, $options)
    {
        if (!$product->is_type('variable')) {
            return [];
        }

        $selectedAttributesValues = self::getAllPossibleOptionsValues($options);
        $variations = $product->get_available_variations();

        foreach ($variations as $variation) {
            if (!is_array($variation['attributes'])) {
                continue;
            }

            if (self::checkIfVariationMatchSelection($variation, $selectedAttributesValues)) {
                return $variation;
            }
        }

        return [];
    }

    private static function getAllPossibleOptionsValues($options)
    {
        $possibleOptionValues = array();
        foreach ($options as $option) {
            $possibleOptionValues[] = self::textEncodingCases($option['value'], 'original');
            $possibleOptionValues[] = self::textEncodingCases($option['value'], 'decoded');
            $possibleOptionValues[] = self::textEncodingCases($option['value'], 'encoded');
        }
        return $possibleOptionValues;
    }

    private static function checkIfVariationMatchSelection($variation, $selction)
    {
        $variationAttributeValues = array_values($variation['attributes']);
        $matchingAttributeValuePairs = array_intersect($variationAttributeValues, $selction);

        return count($matchingAttributeValuePairs) == count($variation['attributes']);
    }

    public static function productVariationData($product, $options)
    {
        $_product = new Product($product);
        TranslatorsFactory::get_translator()->translate_product($_product);
        $product = $_product->get_product();

        $variation = self::get_variation($product, $options);
        if (empty($variation)) {
            return null;
        }

        $variation_id = Utils::get_prop($variation, 'variation_id');
        $_variration = wc_get_product($variation_id);

        return array(
            'variation_id' => $variation_id,
            'description' => Utils::get_prop($variation, 'variation_description'),
            'price' => Utils::get_prop($variation, 'display_price'),
            'sale_price' => Utils::get_prop($variation, 'display_price'),
            'image' => Image::getMainImage($product->get_id(), Utils::get_prop($variation, 'image_id'), true),
            'weight' => floatval(Utils::get_prop($variation, 'weight')),
            'quantity' => $_variration->get_stock_quantity(),
            'in_stock' => Utils::get_prop($variation, 'is_in_stock'),
        );
    }

    private function setInStockAsTrue()
    {
        foreach ($variation['attributes'] as $key => $value) {
            $search_result = self::search($res_prep, $value);

            if ($search_result) {
                $path_to_value = explode('.', $search_result['path']);

                $res_prep[$path_to_value[0]][$path_to_value[1]][$path_to_value[2]]['in_stock'] = true;
            }
        }
    }

    public static function getProductCartItemOptions($product, $attributes){
        $options = array(); 
        if($product->is_type('variable')){
            foreach ($attributes as $attribute_name => $attribute_value) {
                if($attribute_value instanceof \WC_Product_Attribute){
                    $attribute_value = urldecode($attribute_value->get_data()['value']);
                }

                $attribute_name = urldecode($attribute_name);
                $name = wc_attribute_label($attribute_name);
                $translated_term = self::translateAttributeValue($attribute_name, $attribute_value);

                $options[] = [
                    'product_option_id' => $attribute_name,
                    'name' => $name,
                    'product_option_value' => [],
                    'type' => 'radio',
                    'value' => $translated_term,
                    'required' => true,
                ];
            }
        }

        return $options;
    }
}
