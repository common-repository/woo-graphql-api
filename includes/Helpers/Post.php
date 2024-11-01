<?php

namespace WCGQL\Helpers;

class Post
{
    /** @var \WP_Term */
    private $post;
    /** @var string */
    private $title;
    /** @var string */
    private $excerpt;
    /** @var string */
    private $content;

    /**
     * Creates a new Wordpress Post.
     *
     * @param int|WP_Post $post
     */
    public function __construct($post)
    {
        if (\is_numeric($post)) {
            $this->post = \get_post($post);
        } else if ($post instanceof \WP_Post) {
            $this->post = $post;
        } else {
            throw new \Exception("WPPost expects post id or WP_Post object as input '$post' found.");
        }
    }

    public function to_schema()
    {
        \WCGQL\Translators\TranslatorsFactory::get_translator()->translate_post($this);
        return array(
            'id' => $this->get_id(),
            'title' => $this->get_title(),
            'content' => $this->get_content(),
            'excerpt' => $this->get_excerpt(),
            'date' => Utils::get_prop($this->post, 'post_date'),
            'ref' => $this,
        );
    }

    /** @return int */
    public function get_id()
    {
        return Utils::get_prop($this->post, 'ID');
    }

    /** @return string */
    public function get_title()
    {
        return Utils::either($this->title, Utils::get_prop($this->post, 'post_title'));
    }

    /** @param string $title */
    public function set_title($title)
    {
        if (\is_string($title)) {
            $this->title = $title;
        }
    }

    /** @return string */
    public function get_content()
    {
        return Utils::either($this->content, Utils::get_prop($this->post, 'post_content'));
    }

    /** @param string $content */
    public function set_content($content)
    {
        if (\is_string($content)) {
            $this->content = $content;
        }
    }

    /** @return string */
    public function get_excerpt()
    {
        return Utils::either($this->excerpt, Utils::get_prop($this->post, 'post_excerpt'));
    }

    /** @param string $excerpt */
    public function set_excerpt($excerpt)
    {
        if (\is_string($excerpt)) {
            $this->excerpt = $excerpt;
        }
    }
}
