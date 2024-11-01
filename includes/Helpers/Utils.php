<?php

namespace WCGQL\Helpers;

use WCGQL\Translators\TranslatorsFactory;

class Utils
{
    public static function map(&$array, $map, $translate = false)
    {
        foreach ($map as $key1 => $key2) {
            if (is_callable($key2)) {
                $array[$key1] = $key2($array);
            } elseif (is_string($key2)) {
                $array[$key1] = self::get_prop($array, $key2, $translate);
            } else {
                $array[$key1] = null;
            }
        }
        return $array;
    }

    public static function get_prop($arr, $propname = '', $translate = false)
    {
        if (!isset($arr)) {
            return null;
        }
        if (
            (is_array($arr) && isset($arr[$propname])) ||
            (is_object($arr) && isset($arr->{$propname}))
        ) {
            $prop = is_array($arr) ? $arr[$propname] : $arr->{$propname};
            if (!isset($prop)) {
                return null;
            }
            return $translate && is_string($prop)
                ? TranslatorsFactory::get_translator()->translate_string($prop)
                : $prop;
        }
        return null;
    }

    public static function get_text($text, $lang = '')
    {
        if (empty($lang) && !empty($GLOBALS['gq_lang'])) {
            $lang = sanitize_text_field($GLOBALS['gq_lang']);
        }
        return apply_filters('translate_text', $text, $lang);
    }

    public static function array_flatten_sorted(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = strtolower($a);
        });
        sort($return);
        return $return;
    }

    public static function clean_empty(&$array)
    {
        foreach ($array as $key => $val) {
            if ($val == null || empty($val) || !$val) {
                unset($array[$key]);
                continue;
            }

            if (is_array($val)) {
                $array[$key] = clean_empty($array[$key]);
                continue;
            }
        }

        return $array;
    }

    public static function phpinfo_dump()
    {
        ob_start();
        phpinfo();
        $data = ob_get_contents();
        ob_clean();
        return $data;
    }

    public static function either($first, $second)
    {
        return isset($first) ? $first : $second;
    }

    public static function dd($var)
    {
        die(var_dump($var));
    }

    public static function getSitePlugins()
    {
        include_once 'wp-admin/includes/plugin.php';

        $allPlugins = get_plugins();

        $activePlugins = get_option('active_plugins');

        $plugins = [];
        foreach ($allPlugins as $key => $value) {
            $isActive = (in_array($key, $activePlugins)) ? true : false;
            $plugins[] = array(
                'Name' => $value['Name'],
                'PluginURI' => $value['PluginURI'],
                'Version' => $value['Version'],
                'Description' => $value['Description'],
                'Author' => $value['Author'],
                'AuthorURI' => $value['AuthorURI'],
                'TextDomain' => $value['TextDomain'],
                'DomainPath' => $value['DomainPath'],
                'Network' => $value['Network'],
                'Title' => $value['Title'],
                'AuthorName' => $value['AuthorName'],
                'isActive' => $isActive,
            );
        }

        return $plugins;
    }

    public static function remove_key_prefix($prefix, $object)
    {
        $remove_prefix = function ($key) use ($prefix) {
            return self::starts_with($prefix, $key)
                ? str_replace($prefix, '', $key)
                : $key;
        };

        $new_keys = array_map($remove_prefix, array_keys($object));
        return array_combine($new_keys, $object);
    }

    public static function starts_with($needle, $heystack)
    {
        return substr_compare($heystack, $needle, 0);
    }

    public static function add_key_prefix($prefix, $object)
    {
        $add_prefix = function ($key) use ($prefix) {
            return $prefix . $key;
        };

        $new_keys = array_map($add_prefix, array_keys($object));
        return array_combine($new_keys, $object);
    }

    public static function mail_admin($from, $name, $body)
    {
        $to = \get_option('admin_email');
        $body = \sprintf("From: %s <%s> \n\n %s", $name, $from, $body);
        $store_name = get_option('woocommerce_email_from_name');
        $subject = get_option('contactUsSubject');

        if (empty($subject)) {
            $subject = "{$store_name} App - A new enquiry.";
        }

        return \wp_mail(
            $to,
            $subject,
            $body
        );
    }

    public static function string_not_empty($string)
    {
        return \is_string($string) && !empty($string);
    }
}
