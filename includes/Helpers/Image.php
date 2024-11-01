<?php

namespace WCGQL\Helpers;

class Image
{
    public static function getMainImage($productId = 0, $image_id = 0, $url_only = false)
    {
        if (self::checkFIBUActive()) {
            global $knawatfibu;
            $image = $knawatfibu->admin->knawatfibu_get_image_meta($productId);
            if ($image && isset($image['img_url']) && !empty($image['img_url'])) {
                return $image['img_url'];
            }
        }

        return self::image_from_id(
            $image_id,
            $url_only
        );
    }

    public static function checkFIBUActive()
    {
        $blog_plugins = get_option('active_plugins', array());
        $site_plugins = is_multisite() ? (array) maybe_unserialize(get_site_option('active_sitewide_plugins')) : array();

        if (in_array('featured-image-by-url/featured-image-by-url.php', $blog_plugins) || isset($site_plugins['featured-image-by-url/featured-image-by-url.php'])) {
            return true;
        }
        return false;
    }

    public static function image_from_id($id, $url_only = false)
    {
        if (!$id) {
            return null;
        }
            
        $post = (array)get_post($id);
    
        if ($url_only) {
            $image =  wp_get_attachment_image_src($id, 'full');
            if (!$image) {
                return "";
            }

            return $image[0];
        };
    
        return map($post, [
            'image_id'  => 'ID',
            'title'     => 'post_title',
            'name'      => 'post_name',
            'image'     => 'guid'
        ]);
    }
}
