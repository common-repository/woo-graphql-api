<?php

namespace WCGQL\GQL;

trait ProductAttributeGroupTypeResolver
{

    public function ProductAttributeGroupType_attribute($root, $args, $ctx)
    {
        return $root['attribute'];
    }

}

?>