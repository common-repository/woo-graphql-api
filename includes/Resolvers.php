<?php

namespace WCGQL\GQL;
require_once __dir__ . '/Resolvers/ProductType.php';
require_once __dir__ . '/Resolvers/ProductAttributeGroupType.php';
require_once __dir__ . '/Resolvers/ProductOptionType.php';
require_once __dir__ . '/Resolvers/CategoryType.php';
require_once __dir__ . '/Resolvers/ManufacturerType.php';
require_once __dir__ . '/Resolvers/AddressType.php';
require_once __dir__ . '/Resolvers/MethodExtensionType.php';
require_once __dir__ . '/Resolvers/ZoneType.php';
require_once __dir__ . '/Resolvers/OrderType.php';
require_once __dir__ . '/Resolvers/CartItemType.php';
require_once __dir__ . '/Resolvers/CustomerType.php';
require_once __dir__ . '/Resolvers/CartType.php';
require_once __dir__ . '/Resolvers/PostsCategory.php';
require_once __dir__ . '/Resolvers/RootQueryType.php';
require_once __dir__ . '/Resolvers/MutationType.php';

class Resolvers
{
    use ProductTypeResolver;
    use ProductAttributeGroupTypeResolver;
    use ProductOptionTypeResolver;
    use CategoryTypeResolver;
    use ManufacturerTypeResolver;
    use AddressTypeResolver;
    use MethodExtensionTypeResolver;
    use ZoneTypeResolver;
    use OrderTypeResolver;
    use CartItemTypeResolver;
    use CustomerTypeResolver;
    use CartTypeResolver;
    use PostsCategoryTypeResolver;
    use RootQueryTypeResolver;
    use MutationTypeResolver;
}

?>