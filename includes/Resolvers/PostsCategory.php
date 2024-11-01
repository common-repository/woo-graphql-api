<?php

namespace WCGQL\GQL;


trait PostsCategoryTypeResolver
{

    public function PostsCategoryType_parent($root, $args, $ctx)
    {
        return $root['ref']->schema_get_parent();
    }

    public function PostsCategoryType_posts($root, $args, $ctx)
    {
        return $root['ref']->schema_get_posts();
    }
}

?>