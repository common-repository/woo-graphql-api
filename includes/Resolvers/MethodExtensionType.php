<?php

namespace WCGQL\GQL;

trait MethodExtensionTypeResolver
{

    public function MethodExtensionType_quote($root, $args, $ctx)
    {
        return $root['quote'];
    }

}

?>