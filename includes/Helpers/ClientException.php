<?php

namespace WCGQL\Helpers;

use GraphQL\Error\ClientAware;

class ClientException extends \Exception implements ClientAware
{
    function isClientSafe()
    {
        return true;
    }

    function getCategory()
    {
        return "Validation";
    }
}