<?php
namespace WCGQL\GQL;

require_once __DIR__ . '/Resolvers.php';

use GraphQL\Type\Schema;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\CustomScalarType;

class Types
{
    public static $ProductType;
    public static $ProductAttributeType;
    public static $ProductAttributeGroupType;
    public static $ProductOptionType;
    public static $productOptionValueType;
    public static $ProductOptionType2;
    public static $productOptionValueType2;
    public static $ProductImageType;
    public static $ReviewType;
    public static $ReviewInput;
    public static $CategoryType;
    public static $ManufacturerType;
    public static $InformationType;
    public static $SessionType;
    public static $AddressInput;
    public static $AddressType;
    public static $MethodExtensionType;
    public static $MethodQuoteType;
    public static $CustomerGroupType;
    public static $DownloadType;
    public static $LanguageType;
    public static $CountryType;
    public static $StateType;
    public static $CurrencyType;
    public static $ZoneType;
    public static $BannerType;
    public static $OrderInput;
    public static $orderConfirmationType;
    public static $OrderProductInput;
    public static $OrderProductType;
    public static $OrderProductOptionInput;
    public static $TotalsType;
    public static $OrderTotalsInput;
    public static $OrderType;
    public static $CustomFieldType;
    public static $CartItemType;
    public static $CustomerType;
    public static $CustomerInput;
    public static $CartItemInput;
    public static $CartItemOptionInput;
    public static $CartType;
    public static $StoreType;
    public static $CountType;
    public static $CustomerEdit;
    public static $ShippingQuoteType;
    public static $FaqType;
    public static $SiteInfoType;
    public static $SiteConfigType;
    public static $PluginType;
    public static $PriceType;
    public static $ProductVariationType;
    public static $NewsType;
    public static $MenuItemType;
    public static $PostType;
    public static $PostsCategory;
    public static $ResponseType;
    public static $ResponseObjectType;
    public static $OrderStatusEnumType;
    public static $DealsModeEnumType;
    public static $RootQueryType;
    public static $MutationType;
    public static $schema;
    private static $types;
    private static $resolvers;

    private function __construct()
    {
        self::$resolvers = new Resolvers ();

        static::$ProductType = new ObjectType ([
            'name' => 'ProductType',
            'fields'  => function () { return [
                'product_id' => [
                    'type' => Type::id ()
                ],
                'name' => [
                    'type' => Type::string ()
                ],
                'description' => [
                    'type' => Type::string ()
                ],
                'model' => [
                    'type' => Type::string ()
                ],
                'quantity' => [
                    'type' => Type::string ()
                ],
                'image' => [
                    'type' => Type::string ()
                ],
                'price' => [
                    'type' => self::$PriceType
                ],
                'special' => [
                    'type' => self::$PriceType
                ],
                'wishlist' => [
                    'type' => Type::boolean (),
                ],
                'in_stock' => [
                    'type' => Type::boolean ()
                ],
                'manufacturer' => [
                    'type' => self::$ManufacturerType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->ProductType_manufacturer ($root, $args, $ctx);
                    }
                ],
                'attributes' => [
                    'type' => Type::listOf (self::$ProductAttributeGroupType),
                    'args' => [
                        'language_id' => Type::int ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->ProductType_attributes ($root, $args, $ctx);
                    }
                ],
                'options' => [
                    'type' => Type::listOf (self::$ProductOptionType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->ProductType_options ($root, $args, $ctx);
                    }
                ],
                'images' => [
                    'type' => Type::listOf (self::$ProductImageType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->ProductType_images ($root, $args, $ctx);
                    }
                ],
                'rating' => [
                    'type' => Type::float ()
                ],
                'permalink' => [
                    'type' => Type::string ()
                ],
                'tax_mode' => [
                    'type' => Type::string ()
                ],
                'is_managing_stock' => [
                    'type' => Type::boolean ()
                ],
                'is_virtual' => [
                    'type' => Type::boolean ()
                ],
                'is_downloadable' => [
                    'type' => Type::boolean ()
                ],
                'is_featured' => [
                    'type' => Type::boolean ()
                ],
                'is_on_sale' => [
                    'type' => Type::boolean ()
                ],
                'price_min' => [
                    'type' => self::$PriceType
                ],
                'price_max' => [
                    'type' => self::$PriceType
                ],
                'price_sale_min' => [
                    'type' => self::$PriceType
                ],
                'price_sale_max' => [
                    'type' => self::$PriceType
                ],
                'is_variable' => [
                    'type' => Type::boolean ()
                ],
            ]; }
        ]);

        static::$ProductAttributeType = new ObjectType ([
            'name' => 'ProductAttributeType',
            'fields' => function () {
                return [
                    'attribute_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'text' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$ProductAttributeGroupType = new ObjectType ([
            'name' => 'ProductAttributeGroupType',
            'fields' => function () {
                return [
                    'attribute_group_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'attribute' => [
                        'type' => Type::listOf(self::$ProductAttributeType),
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->ProductAttributeGroupType_attribute($root, $args, $ctx);
                        }
                    ]
                ];
            }
        ]);

        static::$ProductOptionType = new ObjectType ([
            'name' => 'ProductOptionType',
            'fields' => function () {
                return [
                    'product_option_id' => [
                        'type' => Type::id()
                    ],
                    'product_option_value' => [
                        'type' => Type::listOf(self::$productOptionValueType),
                    ],
                    'option_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'type' => [
                        'type' => Type::string()
                    ],
                    'value' => [
                        'type' => Type::string()
                    ],
                    'required' => [
                        'type' => Type::int()
                    ],
                    'in_stock' => [
                        'type' => Type::boolean()
                    ]
                ];
            }
        ]);

        static::$productOptionValueType = new ObjectType ([
            'name' => 'productOptionValueType',
            'fields' => function () {
                return [
                    'product_option_value_id' => [
                        'type' => Type::id()
                    ],
                    'option_value_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ],
                    'quantity' => [
                        'type' => Type::int()
                    ],
                    'subtract' => [
                        'type' => Type::int()
                    ],
                    'price' => [
                        'type' => self::$PriceType
                    ],
                    'price_prefix' => [
                        'type' => Type::string()
                    ],
                    'weight' => [
                        'type' => Type::float()
                    ],
                    'weight_prefix' => [
                        'type' => Type::string()
                    ],
                    'in_stock' => [
                        'type' => Type::boolean()
                    ]
                ];
            }
        ]);

        static::$ProductOptionType2 = new ObjectType ([
            'name' => 'ProductOptionType2',
            'fields' => function () {
                return [
                    'product_option_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'values' => [
                        'type' => Type::listOf(self::$productOptionValueType2)
                    ],
                    'type' => [
                        'type' => Type::string()
                    ],
                    'required' => [
                        'type' => Type::int()
                    ]
                ];
            }
        ]);

        static::$productOptionValueType2 = new ObjectType ([
            'name' => 'productOptionValueType2',
            'fields' => function () {
                return [
                    'name' => [
                        'type' => Type::string()
                    ],
                    'enabled' => [
                        'type' => Type::boolean()
                    ]
                ];
            }
        ]);

        static::$ProductImageType = new ObjectType ([
            'name' => 'ProductImageType',
            'fields' => function () {
                return [
                    'product_image_id' => [
                        'type' => Type::id()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$ReviewType = new ObjectType ([
            'name' => 'ReviewType',
            'fields' => function () {
                return [
                    'review_id' => [
                        'type' => Type::id()
                    ],
                    'author' => [
                        'type' => Type::string()
                    ],
                    'email' => [
                        'type' => Type::string()
                    ],
                    'rating' => [
                        'type' => Type::string()
                    ],
                    'text' => [
                        'type' => Type::string()
                    ],
                    'purchase_verified' => [
                        'type' => Type::boolean()
                    ],
                    'createdAt' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$ReviewInput = new InputObjectType ([
            'name' => 'ReviewInput',
            'fields' => function () {
                return [
                    'name' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'email' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'rating' => [
                        'type' => Type::nonNull(Type::int())
                    ],
                    'text' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'user_id' => [
                        'type' => Type::nonNull(Type::id())
                    ]
                ];
            }
        ]);

        static::$CategoryType = new ObjectType ([
            'name' => 'CategoryType',
            'fields'  => function () { return [
                'category_id' => [
                    'type' => Type::id ()
                ],
                'name' => [
                    'type' => Type::string ()
                ],
                'image' => [
                    'type' => Type::string ()
                ],
                'description' => [
                    'type' => Type::string ()
                ],
                'parent' => [
                    'type' => self::$CategoryType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CategoryType_parent ($root, $args, $ctx);
                    }
                ],
                'products_count' => [
                    'type' => Type::int ()
                ],
                'products' => [
                    'type' => Type::listOf (self::$ProductType),
                    'args' => [
                        'limit' => Type::int (),
                        'start' => Type::int (),
                        'sort' => Type::string (),
                        'order' => Type::string (),
                        'instock' => Type::boolean ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CategoryType_products ($root, $args, $ctx);
                    }
                ],
                'categories' => [
                    'type' => Type::listOf (self::$CategoryType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CategoryType_categories ($root, $args, $ctx);
                    }
                ]
            ]; }
        ]);

        static::$ManufacturerType = new ObjectType ([
            'name' => 'ManufacturerType',
            'fields' => function () {
                return [
                    'manufacturer_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ],
                    'products' => [
                        'type' => Type::listOf(self::$ProductType),
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->ManufacturerType_products($root, $args, $ctx);
                        }
                    ]
                ];
            }
        ]);

        static::$InformationType = new ObjectType ([
            'name' => 'InformationType',
            'fields' => function () {
                return [
                    'information_id' => [
                        'type' => Type::id()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'description' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$SessionType = new ObjectType ([
            'name' => 'SessionType',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::id())
                    ]
                ];
            }
        ]);

        static::$AddressInput = new InputObjectType ([
            'name' => 'AddressInput',
            'fields' => function () {
                return [
                    'firstname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'lastname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'company' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'address_1' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'address_2' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'custom_field' => [
                        'type' => Type::string()
                    ],
                    'postcode' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'city' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'zone_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'country_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'telephone' => [
                        'type' => Type::string()
                    ],
                    'custom_field' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'default' => [
                        'type' => Type::boolean()
                    ]
                ];
            }
        ]);

        static::$AddressType = new ObjectType ([
            'name' => 'AddressType',
            'fields' => function () {
                return [
                    'address_id' => [
                        'type' => Type::id()
                    ],
                    'firstname' => [
                        'type' => Type::string()
                    ],
                    'lastname' => [
                        'type' => Type::string()
                    ],
                    'company' => [
                        'type' => Type::string()
                    ],
                    'address_1' => [
                        'type' => Type::string()
                    ],
                    'address_2' => [
                        'type' => Type::string()
                    ],
                    'custom_field' => [
                        'type' => Type::string()
                    ],
                    'postcode' => [
                        'type' => Type::string()
                    ],
                    'city' => [
                        'type' => Type::string()
                    ],
                    'telephone' => [
                        'type' => Type::string()
                    ],
                    'zone' => [
                        'type' => self::$ZoneType,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->AddressType_zone($root, $args, $ctx);
                        }
                    ],
                    'country' => [
                        'type' => self::$CountryType,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->AddressType_country($root, $args, $ctx);
                        }
                    ]
                ];
            }
        ]);

        static::$MethodExtensionType = new ObjectType ([
            'name' => 'MethodExtensionType',
            'fields' => function () {
                return [
                    'title' => [
                        'type' => Type::string()
                    ],
                    'quote' => [
                        'type' => self::$MethodQuoteType,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->MethodExtensionType_quote($root, $args, $ctx);
                        }
                    ],
                    'error' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$MethodQuoteType = new ObjectType ([
            'name' => 'MethodQuoteType',
            'fields' => function () {
                return [
                    'code' => [
                        'type' => Type::string()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'cost' => [
                        'type' => Type::float()
                    ],
                    'text' => [
                        'type' => Type::string()
                    ],
                    'details' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$CustomerGroupType = new ObjectType ([
            'name' => 'CustomerGroupType',
            'fields' => function () {
                return [
                    'customer_group_id' => [
                        'type' => Type::id()
                    ],
                    'approval' => [
                        'type' => Type::int()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'description' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$DownloadType = new ObjectType ([
            'name' => 'DownloadType',
            'fields' => function () {
                return [
                    'download_id' => [
                        'type' => Type::id()
                    ],
                    'order_id' => [
                        'type' => Type::id()
                    ],
                    'date_added' => [
                        'type' => Type::string()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'filename' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'mask' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$LanguageType = new ObjectType ([
            'name' => 'LanguageType',
            'fields' => function () {
                return [
                    'language_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'code' => [
                        'type' => Type::string()
                    ],
                    'locale' => [
                        'type' => Type::string()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$CountryType = new ObjectType ([
            'name' => 'CountryType',
            'fields'  => function () { return [
                'country_id' => [
                    'type' => Type::id ()
                ],
                'name' => [
                    'type' => Type::string ()
                ],
                'states' => [
                    'type' => Type::listOf (self::$StateType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_states ($root, $args, $ctx);
                    }
                ],
                'iso_code_2' => [
                    'type' => Type::string ()
                ],
                'iso_code_3' => [
                    'type' => Type::string ()
                ],
                'calling_code' => [
                    'type' => Type::string ()
                ]
            ]; }
        ]);

        static::$StateType = new ObjectType ([
            'name' => 'StateType',
            'fields' => function () {
                return [
                    'state_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$CurrencyType = new ObjectType ([
            'name' => 'CurrencyType',
            'fields'  => function () { return [
                'currency_id' => [
                    'type' => Type::id ()
                ],
                'title' => [
                    'type' => Type::string ()
                ],
                'code' => [
                    'type' => Type::string ()
                ],
                'symbol_left' => [
                    'type' => Type::string ()
                ],
                'symbol_right' => [
                    'type' => Type::string ()
                ],
                'value' => [
                    'type' => Type::float ()
                ],
                'currency_position' => [
                    'type' => Type::string ()
                ],
                'thousand_separator' => [
                    'type' => Type::string ()
                ],
                'decimal_separator' => [
                    'type' => Type::string ()
                ],
                'number_of_decimals' =>  Type::int ()
            ]; }
        ]);

        static::$ZoneType = new ObjectType ([
            'name' => 'ZoneType',
            'fields' => function () {
                return [
                    'zone_id' => [
                        'type' => Type::id()
                    ],
                    'country' => [
                        'type' => self::$CountryType,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->ZoneType_country($root, $args, $ctx);
                        }
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'code' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$BannerType = new ObjectType ([
            'name' => 'BannerType',
            'fields' => function () {
                return [
                    'banner_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'banner_image_id' => [
                        'type' => Type::id()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'link' => [
                        'type' => Type::string()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$OrderInput = new InputObjectType ([
            'name' => 'OrderInput',
            'fields'  => function () { return [
                'invoice_prefix' => [
                    'type' => Type::string ()
                ],
                'store_id' => [
                    'type' => Type::id ()
                ],
                'store_name' => [
                    'type' => Type::string ()
                ],
                'store_url' => [
                    'type' => Type::string ()
                ],
                'customer_id' => [
                    'type' => Type::id ()
                ],
                'customer_group_id' => [
                    'type' => Type::id ()
                ],
                'firstname' => [
                    'type' => Type::string ()
                ],
                'lastname' => [
                    'type' => Type::string ()
                ],
                'email' => [
                    'type' => Type::string ()
                ],
                'telephone' => [
                    'type' => Type::string ()
                ],
                'fax' => [
                    'type' => Type::string ()
                ],
                'custom_field' => [
                    'type' => Type::string ()
                ],
                'payment_firstname' => [
                    'type' => Type::string ()
                ],
                'payment_lastname' => [
                    'type' => Type::string ()
                ],
                'payment_company' => [
                    'type' => Type::string ()
                ],
                'payment_address_1' => [
                    'type' => Type::string ()
                ],
                'payment_address_2' => [
                    'type' => Type::string ()
                ],
                'payment_city' => [
                    'type' => Type::string ()
                ],
                'payment_postcode' => [
                    'type' => Type::string ()
                ],
                'payment_country' => [
                    'type' => Type::string ()
                ],
                'payment_country_id' => [
                    'type' => Type::id ()
                ],
                'payment_zone' => [
                    'type' => Type::string ()
                ],
                'payment_zone_id' => [
                    'type' => Type::id ()
                ],
                'payment_address_format' => [
                    'type' => Type::string ()
                ],
                'payment_custom_field' => [
                    'type' => Type::string ()
                ],
                'payment_method' => [
                    'type' => Type::string ()
                ],
                'payment_code' => [
                    'type' => Type::string ()
                ],
                'shipping_firstname' => [
                    'type' => Type::string ()
                ],
                'shipping_lastname' => [
                    'type' => Type::string ()
                ],
                'shipping_company' => [
                    'type' => Type::string ()
                ],
                'shipping_address_1' => [
                    'type' => Type::string ()
                ],
                'shipping_address_2' => [
                    'type' => Type::string ()
                ],
                'shipping_city' => [
                    'type' => Type::string ()
                ],
                'shipping_postcode' => [
                    'type' => Type::string ()
                ],
                'shipping_country' => [
                    'type' => Type::string ()
                ],
                'shipping_country_id' => [
                    'type' => Type::id ()
                ],
                'shipping_zone' => [
                    'type' => Type::string ()
                ],
                'shipping_zone_id' => [
                    'type' => Type::id ()
                ],
                'shipping_address_format' => [
                    'type' => Type::string ()
                ],
                'shipping_custom_field' => [
                    'type' => Type::string ()
                ],
                'shipping_method' => [
                    'type' => Type::string ()
                ],
                'shipping_code' => [
                    'type' => Type::string ()
                ],
                'comment' => [
                    'type' => Type::string ()
                ],
                'total' => [
                    'type' => Type::float ()
                ],
                'affiliate_id' => [
                    'type' => Type::id ()
                ],
                'commission' => [
                    'type' => Type::float ()
                ],
                'marketing_id' => [
                    'type' => Type::id ()
                ],
                'tracking' => [
                    'type' => Type::string ()
                ],
                'language_id' => [
                    'type' => Type::id ()
                ],
                'currency_id' => [
                    'type' => Type::id ()
                ],
                'currency_code' => [
                    'type' => Type::float ()
                ],
                'currency_value' => [
                    'type' => Type::string ()
                ],
                'ip' => [
                    'type' => Type::string ()
                ],
                'forwarded_ip' => [
                    'type' => Type::string ()
                ],
                'user_agent' => [
                    'type' => Type::string ()
                ],
                'accept_language' => [
                    'type' => Type::string ()
                ],
                'products' => [
                    'type' => Type::listOf (self::$OrderProductInput)
                ],
                'sendEmail' => [
                    'type' => Type::boolean ()
                ]
            ]; }
        ]);

        static::$orderConfirmationType = new InputObjectType ([
            'name' => 'orderConfirmationType',
            'fields' => function () {
                return [
                    'text' => [
                        'type' => Type::string()
                    ],
                    'attachment' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$OrderProductInput = new InputObjectType ([
            'name' => 'OrderProductInput',
            'fields' => function () {
                return [
                    'product_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'model' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'quantity' => [
                        'type' => Type::nonNull(Type::int())
                    ],
                    'price' => [
                        'type' => Type::nonNull(Type::float())
                    ],
                    'total' => [
                        'type' => Type::nonNull(Type::float())
                    ],
                    'tax' => [
                        'type' => Type::nonNull(Type::float())
                    ],
                    'reward' => [
                        'type' => Type::int()
                    ],
                    'option' => [
                        'type' => Type::listOf(self::$OrderProductOptionInput)
                    ]
                ];
            }
        ]);

        static::$OrderProductType = new ObjectType ([
            'name' => 'OrderProductType',
            'fields'  => function () { return [
                'order_product_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'order_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'product_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'name' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'model' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'quantity' => [
                    'type' => Type::nonNull (Type::int ())
                ],
                'price' => [
                    'type' => Type::nonNull (self::$PriceType)
                ],
                'total' => [
                    'type' => Type::nonNull (self::$PriceType)
                ],
                'tax' => [
                    'type' => Type::nonNull (self::$PriceType)
                ],
                'reward' => [
                    'type' => Type::nonNull (Type::int ())
                ],
                'tax_mode' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'subtotal' => [
                    'type' => Type::nonNull (self::$PriceType)
                ],
                'image' => [
                    'type' => Type::string ()
                ]
            ]; }
        ]);

        static::$OrderProductOptionInput = new InputObjectType ([
            'name' => 'OrderProductOptionInput',
            'fields' => function () {
                return [
                    'product_option_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'product_option_value_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'value' => [
                        'type' => Type::string()
                    ],
                    'type' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$TotalsType = new ObjectType ([
            'name' => 'TotalsType',
            'fields' => function () {
                return [
                    'code' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'title' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'value' => [
                        'type' => Type::nonNull(self::$PriceType)
                    ],
                    'sort_order' => [
                        'type' => Type::nonNull(Type::int())
                    ]
                ];
            }
        ]);

        static::$OrderTotalsInput = new InputObjectType ([
            'name' => 'OrderTotalsInput',
            'fields' => function () {
                return [
                    'code' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'title' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'value' => [
                        'type' => Type::nonNull(Type::float())
                    ],
                    'sort_order' => [
                        'type' => Type::nonNull(Type::int())
                    ]
                ];
            }
        ]);

        static::$OrderType = new ObjectType ([
            'name' => 'OrderType',
            'fields'  => function () { return [
                'order_id' => [
                    'type' => Type::id ()
                ],
                'invoice_no' => [
                    'type' => Type::int ()
                ],
                'invoice_prefix' => [
                    'type' => Type::string ()
                ],
                'store' => [
                    'type' => self::$StoreType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_store ($root, $args, $ctx);
                    }
                ],
                'products' => [
                    'type' => Type::listOf (self::$OrderProductType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_products ($root, $args, $ctx);
                    }
                ],
                'store_name' => [
                    'type' => Type::string ()
                ],
                'store_url' => [
                    'type' => Type::string ()
                ],
                'customer_id' => [
                    'type' => Type::id ()
                ],
                'firstname' => [
                    'type' => Type::string ()
                ],
                'lastname' => [
                    'type' => Type::string ()
                ],
                'email' => [
                    'type' => Type::string ()
                ],
                'telephone' => [
                    'type' => Type::string ()
                ],
                'fax' => [
                    'type' => Type::string ()
                ],
                'custom_field' => [
                    'type' => Type::string ()
                ],
                'payment_firstname' => [
                    'type' => Type::string ()
                ],
                'payment_lastname' => [
                    'type' => Type::string ()
                ],
                'payment_company' => [
                    'type' => Type::string ()
                ],
                'payment_address_1' => [
                    'type' => Type::string ()
                ],
                'payment_address_2' => [
                    'type' => Type::string ()
                ],
                'payment_postcode' => [
                    'type' => Type::string ()
                ],
                'payment_city' => [
                    'type' => Type::string ()
                ],
                'paymentZone' => [
                    'type' => self::$ZoneType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_paymentZone ($root, $args, $ctx);
                    }
                ],
                'paymentCountry' => [
                    'type' => self::$CountryType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_paymentCountry ($root, $args, $ctx);
                    }
                ],
                'payment_custom_field' => [
                    'type' => Type::string ()
                ],
                'payment_method' => [
                    'type' => Type::string ()
                ],
                'payment_code' => [
                    'type' => Type::string ()
                ],
                'shipping_firstname' => [
                    'type' => Type::string ()
                ],
                'shipping_lastname' => [
                    'type' => Type::string ()
                ],
                'shipping_company' => [
                    'type' => Type::string ()
                ],
                'shipping_address_1' => [
                    'type' => Type::string ()
                ],
                'shipping_address_2' => [
                    'type' => Type::string ()
                ],
                'shipping_postcode' => [
                    'type' => Type::string ()
                ],
                'shipping_city' => [
                    'type' => Type::string ()
                ],
                'shippingZone' => [
                    'type' => self::$ZoneType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_shippingZone ($root, $args, $ctx);
                    }
                ],
                'shippingCountry' => [
                    'type' => self::$CountryType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_shippingCountry ($root, $args, $ctx);
                    }
                ],
                'shipping_custom_field' => [
                    'type' => Type::string ()
                ],
                'shipping_method' => [
                    'type' => Type::string ()
                ],
                'shipping_code' => [
                    'type' => Type::string ()
                ],
                'comment' => [
                    'type' => Type::string ()
                ],
                'total' => [
                    'type' => self::$PriceType
                ],
                'order_status_id' => [
                    'type' => Type::string ()
                ],
                'order_status' => [
                    'type' => Type::string ()
                ],
                'affiliate_id' => [
                    'type' => Type::id ()
                ],
                'commission' => [
                    'type' => Type::string ()
                ],
                'language' => [
                    'type' => self::$LanguageType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_language ($root, $args, $ctx);
                    }
                ],
                'currency' => [
                    'type' => self::$CurrencyType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->OrderType_currency ($root, $args, $ctx);
                    }
                ],
                'ip' => [
                    'type' => Type::string ()
                ],
                'forwarded_ip' => [
                    'type' => Type::string ()
                ],
                'user_agent' => [
                    'type' => Type::string ()
                ],
                'accept_language' => [
                    'type' => Type::string ()
                ],
                'date_added' => [
                    'type' => Type::string ()
                ],
                'date_modified' => [
                    'type' => Type::string ()
                ],
                'tax_total' => [
                    'type' => self::$PriceType
                ],
                'shipping_total' => [
                    'type' => self::$PriceType
                ],
                'subtotal' => [
                    'type' => self::$PriceType
                ],
                'fees_total' => [
                    'type' => self::$PriceType
                ],
                'coupon_discount' => [
                    'type' => self::$PriceType
                ],
                'coupon_code' => [
                    'type' => Type::id ()
                ],
            ]; }
        ]);

        static::$CustomFieldType = new ObjectType ([
            'name' => 'CustomFieldType',
            'fields' => function () {
                return [
                    'custom_field_id' => [
                        'type' => Type::id()
                    ],
                    'custom_field_value' => [
                        'type' => Type::string()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'type' => [
                        'type' => Type::string()
                    ],
                    'value' => [
                        'type' => Type::string()
                    ],
                    'validation' => [
                        'type' => Type::string()
                    ],
                    'location' => [
                        'type' => Type::string()
                    ],
                    'required' => [
                        'type' => Type::boolean()
                    ],
                    'sort_order' => [
                        'type' => Type::int()
                    ]
                ];
            }
        ]);

        static::$CartItemType = new ObjectType ([
            'name' => 'CartItemType',
            'fields'  => function () { return [
                'cart_id' => [
                    'type' => Type::id ()
                ],
                'product_id' => [
                    'type' => Type::id ()
                ],
                'name' => [
                    'type' => Type::string ()
                ],
                'model' => [
                    'type' => Type::string ()
                ],
                'shipping' => [
                    'type' => Type::string ()
                ],
                'image' => [
                    'type' => Type::string ()
                ],
                'option' => [
                    'type' => Type::listOf (self::$ProductOptionType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CartItemType_option ($root, $args, $ctx);
                    }
                ],
                'options' => [
                    'type' => Type::listOf (self::$ProductOptionType)
                ],
                'download' => [
                    'type' => Type::listOf (self::$DownloadType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CartItemType_download ($root, $args, $ctx);
                    }
                ],
                'quantity' => [
                    'type' => Type::int ()
                ],
                'minimum' => [
                    'type' => Type::int ()
                ],
                'subtract' => [
                    'type' => Type::int ()
                ],
                'stock' => [
                    'type' => Type::int ()
                ],
                'price' => [
                    'type' => self::$PriceType
                ],
                'regular_price' => [
                    'type' => self::$PriceType
                ],
                'sale_price' => [
                    'type' => self::$PriceType
                ],
                'total' => [
                    'type' => self::$PriceType
                ],
                'line_tax' => [
                    'type' => self::$PriceType
                ],
                'reward' => [
                    'type' => Type::int ()
                ],
                'points' => [
                    'type' => Type::int ()
                ],
                'tax_class_id' => [
                    'type' => Type::id ()
                ],
                'weight' => [
                    'type' => Type::float ()
                ],
                'weight_class_id' => [
                    'type' => Type::id ()
                ],
                'length' => [
                    'type' => Type::float ()
                ],
                'width' => [
                    'type' => Type::float ()
                ],
                'height' => [
                    'type' => Type::float ()
                ],
                'length_class_id' => [
                    'type' => Type::id ()
                ],
                'tax_mode' => [
                    'type' => Type::string ()
                ],
                'in_stock' => [
                    'type' => Type::boolean ()
                ],
                'is_managing_stock' => [
                    'type' => Type::boolean ()
                ],
                'wishlist' => [
                    'type' => Type::boolean ()
                ],
            ]; }
        ]);

        static::$CustomerType = new ObjectType ([
            'name' => 'CustomerType',
            'fields'  => function () { return [
                'customer_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'customer_group' => [
                    'type' => self::$CustomerGroupType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CustomerType_customer_group ($root, $args, $ctx);
                    }
                ],
                'firstname' => [
                    'type' => Type::string ()
                ],
                'lastname' => [
                    'type' => Type::string ()
                ],
                'email' => [
                    'type' => Type::string ()
                ],
                'telephone' => [
                    'type' => Type::string ()
                ],
                'fax' => [
                    'type' => Type::string ()
                ]
            ]; }
        ]);

        static::$CustomerInput = new InputObjectType ([
            'name' => 'CustomerInput',
            'fields'  => function () { return [
                'customer_group_id' => [
                    'type' => Type::nonNull (Type::int ())
                ],
                'firstname' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'lastname' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'email' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'telephone' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'address_1' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'address_2' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'city' => [
                    'type' => Type::nonNull (Type::string ())
                ],
                'country_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'zone_id' => [
                    'type' => Type::nonNull (Type::id ())
                ],
                'width' => [
                    'type' => Type::float ()
                ],
                'height' => [
                    'type' => Type::float ()
                ],
                'length_class_id' => [
                    'type' => Type::id ()
                ],
                'tax_mode' => [
                    'type' => Type::string ()
                ],
                'in_stock' => [
                    'type' => Type::boolean ()
                ],
                'is_managing_stock' => [
                    'type' => Type::boolean ()
                ]
            ]; }
        ]);

        static::$CustomerType = new ObjectType ([
            'name' => 'CustomerType',
            'fields' => function () {
                return [
                    'customer_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'customer_group' => [
                        'type' => self::$CustomerGroupType,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->CustomerType_customer_group($root, $args, $ctx);
                        }
                    ],
                    'firstname' => [
                        'type' => Type::string()
                    ],
                    'lastname' => [
                        'type' => Type::string()
                    ],
                    'email' => [
                        'type' => Type::string()
                    ],
                    'telephone' => [
                        'type' => Type::string()
                    ],
                    'fax' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$CustomerInput = new InputObjectType ([
            'name' => 'CustomerInput',
            'fields' => function () {
                return [
                    'customer_group_id' => [
                        'type' => Type::nonNull(Type::int())
                    ],
                    'firstname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'lastname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'email' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'telephone' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'address_1' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'address_2' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'city' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'country_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'zone_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'password' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'confirm' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'agree' => [
                        'type' => Type::nonNull(Type::boolean())
                    ],
                    'fax' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'company' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'postcode' => [
                        'type' => Type::nonNull(Type::string())
                    ]
                ];
            }
        ]);

        static::$CartItemInput = new InputObjectType ([
            'name' => 'CartItemInput',
            'fields' => function () {
                return [
                    'product_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'quantity' => [
                        'type' => Type::int()
                    ],
                    'options' => [
                        'type' => Type::listOf(self::$CartItemOptionInput)
                    ],
                    'recurring_id' => [
                        'type' => Type::id()
                    ]
                ];
            }
        ]);

        static::$CartItemOptionInput = new InputObjectType ([
            'name' => 'CartItemOptionInput',
            'fields' => function () {
                return [
                    'option_id' => [
                        'type' => Type::nonNull(Type::id())
                    ],
                    'value' => [
                        'type' => Type::nonNull(Type::string())
                    ]
                ];
            }
        ]);

        static::$CartType = new ObjectType ([
            'name' => 'CartType',
            'fields'  => function () { return [
                'weight' => [
                    'type' => Type::float ()
                ],
                'tax' => [
                    'type' => self::$PriceType
                ],
                'total' => [
                    'type' => self::$PriceType
                ],
                'subtotal' => [
                    'type' => self::$PriceType
                ],
                'shipping_total' => [
                    'type' => self::$PriceType
                ],
                'shipping_tax' => [
                    'type' => self::$PriceType
                ],
                'coupon_discount' => [
                    'type' => self::$PriceType
                ],
                'coupon_code' => [
                    'type' => Type::id ()
                ],
                'has_stock' => [
                    'type' => Type::boolean ()
                ],
                'has_shipping' => [
                    'type' => Type::boolean ()
                ],
                'has_download' => [
                    'type' => Type::boolean ()
                ],
                'shipping_method_code' => [
                    'type' => Type::string()
                ],
                'payment_method_code' => [
                    'type' => Type::string()
                ],
                'totals' => [
                    'type' => Type::listOf (self::$TotalsType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CartType_totals ($root, $args, $ctx);
                    }
                ],
                'fees' => [
                    'type' => Type::listOf (self::$TotalsType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CartType_fees ($root, $args, $ctx);
                    }
                ],
                'items' => [
                    'type' => Type::listOf (self::$CartItemType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->CartType_items ($root, $args, $ctx);
                    }
                ]
            ]; }
        ]);

        static::$StoreType = new ObjectType ([
            'name' => 'StoreType',
            'fields' => function () {
                return [
                    'store_id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'url' => [
                        'type' => Type::string()
                    ],
                    'ssl' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$CountType = new ObjectType ([
            'name' => 'CountType',
            'fields' => function () {
                return [
                    'count' => [
                        'type' => Type::int()
                    ]
                ];
            }
        ]);

        static::$CustomerEdit = new InputObjectType ([
            'name' => 'CustomerEdit',
            'fields' => function () {
                return [
                    'firstname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'lastname' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'email' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'telephone' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'fax' => [
                        'type' => Type::nonNull(Type::string())
                    ]
                ];
            }
        ]);

        static::$ShippingQuoteType = new ObjectType ([
            'name' => 'ShippingQuoteType',
            'fields' => function () {
                return [
                    'title' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'code' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'cost' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'tax_class_id' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'text' => [
                        'type' => Type::nonNull(Type::string())
                    ]
                ];
            }
        ]);

        static::$FaqType = new ObjectType ([
            'name' => 'FaqType',
            'fields' => function () {
                return [
                    'faq_id' => [
                        'type' => Type::id()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'description' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$PriceType = new CustomScalarType([
            'name' => 'Price',
            'serialize' => function($value) {
                return floatVal(wc_format_decimal($value, wc_get_price_decimals()));
            },
            'parseValue' => function($value) {
                return floatVal(wc_format_decimal($value, wc_get_price_decimals()));
            },
            'parseLiteral' => function($valueNode, array $variables = null) {
                return $valueNode->value;
            },
        ]);        

        static::$ProductVariationType = new ObjectType ([
            'name' => 'ProductVariationType',
            'fields' => function () {
                return [
                    'variation_id' => [
                        'type' => Type::id()
                    ],
                    'description' => [
                        'type' => Type::string()
                    ],
                    'price' => [
                        'type' => self::$PriceType
                    ],
                    'sale_price' => [
                        'type' => self::$PriceType
                    ],
                    'image' => [
                        'type' => Type::string()
                    ],
                    'weight' => [
                        'type' => Type::float()
                    ],
                    'quantity' => [
                        'type' => Type::string()
                    ],
                    'in_stock' => [
                        'type' => Type::boolean()
                    ]
                ];
            }
        ]);

        static::$NewsType = new ObjectType ([
            'name' => 'NewsType',
            'fields' => function () {
                return [
                    'news_id' => [
                        'type' => Type::id()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'description' => [
                        'type' => Type::string()
                    ],
                    'short_description' => [
                        'type' => Type::string()
                    ],
                    'image' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$PostType = new ObjectType ([
            'name' => 'PostType',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'content' => [
                        'type' => Type::string()
                    ],
                    'excerpt' => [
                        'type' => Type::string()
                    ],
                    'date' => [
                        'type' => Type::string()
                    ],
                ];
            }
        ]);

        static::$PostsCategory = new ObjectType ([
            'name' => 'PostsCategory',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::id()
                    ],
                    'name' => [
                        'type' => Type::string()
                    ],
                    'count' => [
                        'type' => Type::int()
                    ],
                    'parent' => [
                        'type' => self::$PostsCategory,
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->PostsCategoryType_parent($root, $args, $ctx);
                        }
                    ],
                    'posts' => [
                        'type' => Type::listOf(self::$PostType),
                        'resolve' => function ($root, $args, $ctx) {
                            return self::$resolvers->PostsCategoryType_posts($root, $args, $ctx);
                        }
                    ],
                ];
            }
        ]);

        static::$MenuItemType = new ObjectType ([
            'name' => 'MenuItemType',
            'fields' => function () {
                return [
                    'item_id' => [
                        'type' => Type::id()
                    ],
                    'object_id' => [
                        'type' => Type::id()
                    ],
                    'object_type' => [
                        'type' => Type::string()
                    ],
                    'url' => [
                        'type' => Type::string()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'order' => [
                        'type' => Type::int()
                    ],
                ];
            }
        ]);

        static::$SiteInfoType = new ObjectType ([
            'name' => 'SiteInfoType',
            'fields'  => function () { return [
                'phpversion' => [
                    'type' => Type::string ()
                ],
                'mysqlversion' => [
                    'type' => Type::string ()
                ],
                'phpinfo' => [
                    'type' => Type::string ()
                ],
                'pluginversion' => [
                    'type' => Type::string ()
                ],
                'plugins' => [
                    'type' => Type::listOf ( self::$PluginType )
                ]
            ]; }
        ]);

        static::$SiteConfigType = new ObjectType ([
            'name' => 'SiteConfigType',
            'fields'  => function () { return [
                'tax_display_shop' => [
                    'type' => Type::string ()
                ],
                'tax_display_cart' => [
                    'type' => Type::string ()
                ],
                'hide_out_of_stock_items' => [
                    'type' => Type::boolean ()
                ],
                'price_suffix' => [
                    'type' => Type::string ()
                ],
            ]; }
        ]);

        static::$PluginType = new ObjectType ([
            'name' => 'PluginType',
            'fields'  => function () { return [
                'Name' => [
                    'type' => Type::string ()
                ],
                'PluginURI' => [
                    'type' => Type::string ()
                ],
                'Version' => [
                    'type' => Type::string ()
                ],
                'Description' => [
                    'type' => Type::string ()
                ],
                'Author' => [
                    'type' => Type::string ()
                ],
                'AuthorURI' => [
                    'type' => Type::string ()
                ],
                'TextDomain' => [
                    'type' => Type::string ()
                ],
                'DomainPath' => [
                    'type' => Type::string ()
                ],
                'Network' => [
                    'type' => Type::string ()
                ],
                'Title' => [
                    'type' => Type::string ()
                ],
                'AuthorName' => [
                    'type' => Type::string ()
                ],
                'isActive' => [
                    'type' => Type::boolean ()
                ]
            ]; }
        ]);

        static::$ResponseObjectType = new ObjectType ([
            'name' => 'ResponseObjectType',
            'fields' => function () {
                return [
                    'title' => [
                        'type' => Type::string()
                    ],
                    'code' => [
                        'type' => Type::string()
                    ],
                    'content' => [
                        'type' => Type::string()
                    ]
                ];
            }
        ]);

        static::$ResponseType = new ObjectType ([
            'name' => 'ResponseType',
            'fields' => function () {
                return [
                    'data' => [
                        'type' => Type::listOf(self::$ResponseObjectType)
                    ],
                    'errors' => [
                        'type' => Type::listOf(self::$ResponseObjectType)
                    ]
                ];
            }
        ]);

        static::$OrderStatusEnumType = new EnumType ([
            'name' => 'OrderStatusEnumType',
            'description' => 'Various order statuses',
            'values' => ['pending', 'processing', 'shipped', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed']
        ]);

        static::$DealsModeEnumType = new EnumType ([
            'name' => 'DealsModeEnumType',
            'description' => 'Deals Modes',
            'values' => ['onSale', 'featured', 'all', 'none']
        ]);

        static::$RootQueryType = new ObjectType ([
            'name' => 'RootQueryType',
            'fields'  => function () { return [
                'product' => [
                    'type' => self::$ProductType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_product ($root, $args, $ctx);
                    }
                ],
                'products' => [
                    'type' => Type::listOf (self::$ProductType),
                    'args' => [
                        'filter_category_id' => Type::string (),
                        'filter_sub_category' => Type::int (),
                        'filter_name' => Type::string (),
                        'sort' => Type::string (),
                        'order' => Type::string (),
                        'start' => Type::int (),
                        'limit' => Type::int (),
                        'instock' => Type::boolean (),
                        'dealsMode' => self::$DealsModeEnumType,
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_products ($root, $args, $ctx);
                    }
                ],
                'relatedProducts' => [
                    'type' => Type::listOf (self::$ProductType),
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_relatedProducts ($root, $args, $ctx);
                    }
                ],
                'productSpecials' => [
                    'type' => Type::listOf (self::$ProductType),
                    'args' => [
                        'sort' => Type::string (),
                        'order' => Type::string (),
                        'start' => Type::int (),
                        'limit' => Type::int ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_productSpecials ($root, $args, $ctx);
                    }
                ],
                'reviews' => [
                    'type' => Type::listOf (self::$ReviewType),
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ()),
                        'start' => Type::int (),
                        'limit' => Type::int ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_reviews ($root, $args, $ctx);
                    }
                ],
                'categories' => [
                    'type' => Type::listOf (self::$CategoryType),
                    'args' => [
                        'parent' => Type::int ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_categories ($root, $args, $ctx);
                    }
                ],
                'category' => [
                    'type' => self::$CategoryType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_category ($root, $args, $ctx);
                    }
                ],
                'manufacturers' => [
                    'type' => Type::listOf (self::$ManufacturerType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_manufacturers ($root, $args, $ctx);
                    }
                ],
                'manufacturer' => [
                    'type' => self::$ManufacturerType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_manufacturer ($root, $args, $ctx);
                    }
                ],
                'informations' => [
                    'type' => Type::listOf (self::$InformationType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_informations ($root, $args, $ctx);
                    }
                ],
                'information' => [
                    'type' => self::$InformationType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_information ($root, $args, $ctx);
                    }
                ],
                'session' => [
                    'type' => self::$SessionType,
                    'args' => [
                        'id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_session ($root, $args, $ctx);
                    }
                ],
                'cart' => [
                    'type' => self::$CartType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_cart ($root, $args, $ctx);
                    }
                ],
                'address' => [
                    'type' => self::$AddressType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_address ($root, $args, $ctx);
                    }
                ],
                'addresses' => [
                    'type' => Type::listOf (self::$AddressType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_addresses ($root, $args, $ctx);
                    }
                ],
                'customerGroup' => [
                    'type' => self::$CustomerGroupType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_customerGroup ($root, $args, $ctx);
                    }
                ],
                'customerGroups' => [
                    'type' => Type::listOf (self::$CustomerGroupType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_customerGroups ($root, $args, $ctx);
                    }
                ],
                'language' => [
                    'type' => self::$LanguageType,
                    'args' => [
                        'id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_language ($root, $args, $ctx);
                    }
                ],
                'languages' => [
                    'type' => Type::listOf (self::$LanguageType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_languages ($root, $args, $ctx);
                    }
                ],
                'zones' => [
                    'type' => Type::listOf (self::$ZoneType),
                    'args' => [
                        'country_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_zones ($root, $args, $ctx);
                    }
                ],
                'zone' => [
                    'type' => self::$ZoneType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_zone ($root, $args, $ctx);
                    }
                ],
                'country' => [
                    'type' => self::$CountryType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_country ($root, $args, $ctx);
                    }
                ],
                'countries' => [
                    'type' => Type::listOf (self::$CountryType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_countries ($root, $args, $ctx);
                    }
                ],
                'currency' => [
                    'type' => self::$CurrencyType,
                    'args' => [
                        'code' => Type::string ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_currency ($root, $args, $ctx);
                    }
                ],
                'currencies' => [
                    'type' => Type::listOf (self::$CurrencyType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_currencies ($root, $args, $ctx);
                    }
                ],
                'banners' => [
                    'type' => Type::listOf (self::$BannerType),
                    'args' => [
                        'layout' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_banners ($root, $args, $ctx);
                    }
                ],
                'wishlist' => [
                    'type' => Type::listOf (self::$ProductType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_wishlist ($root, $args, $ctx);
                    }
                ],
                'order' => [
                    'type' => self::$OrderType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_order ($root, $args, $ctx);
                    }
                ],
                'orders' => [
                    'type' => Type::listOf (self::$OrderType),
                    'args' => [
                        'start' => Type::int (),
                        'limit' => Type::int ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_orders ($root, $args, $ctx);
                    }
                ],
                'paymentAddress' => [
                    'type' => self::$AddressType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_paymentAddress ($root, $args, $ctx);
                    }
                ],
                'shippingAddress' => [
                    'type' => self::$AddressType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_shippingAddress ($root, $args, $ctx);
                    }
                ],
                'paymentMethods' => [
                    'type' => Type::listOf (self::$MethodExtensionType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_paymentMethods ($root, $args, $ctx);
                    }
                ],
                'shippingMethods' => [
                    'type' => Type::listOf (self::$MethodExtensionType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_shippingMethods ($root, $args, $ctx);
                    }
                ],
                'loggedIn' => [
                    'type' => self::$CustomerType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_loggedIn ($root, $args, $ctx);
                    }
                ],
                'faqs' => [
                    'type' => Type::listOf (self::$FaqType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_faqs ($root, $args, $ctx);
                    }
                ],
                'news' => [
                    'type' => self::$NewsType,
                    'args' => [
                        'id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_news ($root, $args, $ctx);
                    }
                ],
                'allnews' => [
                    'type' => Type::listOf (self::$NewsType),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_allnews ($root, $args, $ctx);
                    }
                ],
                'posts_category' => [
                    'type' => self::$PostsCategory,
                    'args' => [
                        'id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_posts_category ($root, $args, $ctx);
                    }
                ],
                'post' => [
                    'type' => self::$PostType,
                    'args' => [
                        'id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_post ($root, $args, $ctx);
                    }
                ],
                'menu' => [
                    'type' => Type::listOf (self::$MenuItemType),
                    'args' => [
                        'name' => Type::string ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_menu ($root, $args, $ctx);
                    }
                ],
                'productVariationPrice' => [
                    'type' => self::$PriceType,
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ()),
                        'options' => Type::nonNull (Type::listOf (self::$OrderProductOptionInput))
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_productVariationPrice ($root, $args, $ctx);
                    }
                ],
                'productVariationData' => [
                    'type' => self::$ProductVariationType,
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ()),
                        'options' => Type::nonNull (Type::listOf (self::$OrderProductOptionInput))
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_productVariationData ($root, $args, $ctx);
                    }
                ],
                'siteInfo' => [
                    'type' => self::$SiteInfoType,
                    'args' => [
                        'key' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_siteInfo ($root, $args, $ctx);
                    }
                ],
                'siteConfig' => [
                    'type' => self::$SiteConfigType,
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_siteConfig ($root, $args, $ctx);
                    }
                ],
                'availableOptions' => [
                    'type' => Type::listOf( self::$ProductOptionType ),
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ()),
                        'options' => Type::nonNull (Type::listOf (self::$OrderProductOptionInput))
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_availableOptions ($root, $args, $ctx);
                    }
                ],
                'getCookie' => [
                    'type' => Type::string (),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_getCookie ($root, $args, $ctx);
                    }
                ],
                'states' => [
                    'type' => Type::listOf (self::$StateType),
                    'args' => [
                        'country_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_states ($root, $args, $ctx);
                    }
                ],
                'getProductIDFromSlug' => [
                    'type' => Type::id (),
                    'args' => [
                        'slug' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_getProductIDFromSlug ($root, $args, $ctx);
                    }
                ],
                'getCategoryIDFromSlug' => [
                    'type' => Type::id (),
                    'args' => [
                        'slug' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->RootQueryType_getCategoryIDFromSlug ($root, $args, $ctx);
                    }
                ]
            ]; }
        ]);

        static::$MutationType = new ObjectType ([
            'name' => 'MutationType',
            'fields'  => function () { return [
                'addReview' => [
                    'type' => Type::id (),
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ()),
                        'input' => Type::nonNull (self::$ReviewInput)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addReview ($root, $args, $ctx);
                    }
                ],
                'addAddress' => [
                    'type' => Type::id (),
                    'args' => [
                        'input' => self::$AddressInput
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addAddress ($root, $args, $ctx);
                    }
                ],
                'editAddress' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'address_id' => Type::id (),
                        'input' => self::$AddressInput
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_editAddress ($root, $args, $ctx);
                    }
                ],
                'deleteAddress' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'address_id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_deleteAddress ($root, $args, $ctx);
                    }
                ],
                'addOrder' => [
                    'type' => Type::id (),
                    'args' => [
                        'input' => Type::nonNull (self::$OrderInput)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addOrder ($root, $args, $ctx);
                    }
                ],
                'editOrder' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'order_id' => Type::nonNull (Type::id ()),
                        'input' => Type::nonNull (self::$OrderInput)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_editOrder ($root, $args, $ctx);
                    }
                ],
                'deleteOrder' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'order_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_deleteOrder ($root, $args, $ctx);
                    }
                ],
                'confirmOrder' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'order_id' => Type::nonNull (Type::id()),
                        'confirmation' => Type::nonNull (self::$orderConfirmationType)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_confirmOrder ($root, $args, $ctx);
                    }
                ],
                'addItemToCart' => [
                    'type' => self::$CartType,
                    'args' => [
                        'input' => Type::nonNull (self::$CartItemInput)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addItemToCart ($root, $args, $ctx);
                    }
                ],
                'addItemsToCart' => [
                    'type' => self::$CartType,
                    'args' => [
                        'input' => Type::nonNull (Type::listOf (self::$CartItemInput))
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addItemsToCart ($root, $args, $ctx);
                    }
                ],
                'updateCartItem' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'cart_id' => Type::nonNull (Type::id ()),
                        'quantity' => Type::nonNull (Type::int ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_updateCartItem ($root, $args, $ctx);
                    }
                ],
                'removeCartItems' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'ids' => Type::nonNull (Type::listOf (Type::id ())),
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_removeCartItems ($root, $args, $ctx);
                    }
                ],
                'addCoupon' => [
                    'type' => self::$CartType,
                    'args' => [
                        'code' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addCoupon ($root, $args, $ctx);
                    }
                ],
                'removeCoupon' => [
                    'type' => Type::boolean (),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_removeCoupon ($root, $args, $ctx);
                    }
                ],
                'setPaymentAddress' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'input' => Type::nonNull (self::$AddressInput)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setPaymentAddress ($root, $args, $ctx);
                    }
                ],
                'setPaymentAddressById' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'address_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setPaymentAddressById ($root, $args, $ctx);
                    }
                ],
                'setPaymentMethod' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'code' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setPaymentMethod ($root, $args, $ctx);
                    }
                ],
                'setShippingAddress' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'input' => self::$AddressInput
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setShippingAddress ($root, $args, $ctx);
                    }
                ],
                'setShippingAddressById' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'address_id' => Type::id ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setShippingAddressById ($root, $args, $ctx);
                    }
                ],
                'setShippingMethod' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'code' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setShippingMethod ($root, $args, $ctx);
                    }
                ],
                'addWishlist' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addWishlist ($root, $args, $ctx);
                    }
                ],
                'addWishlistItems' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'product_ids' => Type::nonNull (Type::listOf (Type::id ()))
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_addWishlistItems ($root, $args, $ctx);
                    }
                ],
                'deleteWishlist' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'product_id' => Type::nonNull (Type::id ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_deleteWishlist ($root, $args, $ctx);
                    }
                ],
                'editCustomer' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'input' => Type::nonNull (self::$CustomerEdit)
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_editCustomer ($root, $args, $ctx);
                    }
                ],
                'editPassword' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'oldPassword' => Type::nonNull (Type::string ()),
                        'password' => Type::nonNull (Type::string ()),
                        'confirm' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_editPassword ($root, $args, $ctx);
                    }
                ],
                'register' => [
                    'type' => Type::id (),
                    'args' => [
                        'input' => self::$CustomerInput
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_register ($root, $args, $ctx);
                    }
                ],
                'login' => [
                    'type' => Type::id (),
                    'args' => [
                        'email' => Type::nonNull (Type::string ()),
                        'password' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_login ($root, $args, $ctx);
                    }
                ],
                'loginByMobileNumber' => [
                    'type' => Type::id (),
                    'args' => [
                        'mobile' => Type::nonNull (Type::string ()),
                        'password' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_loginByMobileNumber ($root, $args, $ctx);
                    }
                ],
                'logout' => [
                  'type' => Type::boolean (),
                  'resolve' => function ($root, $args, $ctx) {
                    return self::$resolvers->MutationType_logout();
                  }
                ],
                'forgotten' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'email' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_forgotten ($root, $args, $ctx);
                    }
                ],
                'contactus' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'name' => Type::nonNull (Type::string ()),
                        'email' => Type::nonNull (Type::string ()),
                        'enquiry' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_contactus ($root, $args, $ctx);
                    }
                ],
                'setLanguage' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'code' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setLanguage ($root, $args, $ctx);
                    }
                ],
                'setCurrency' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'code' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_setCurrency ($root, $args, $ctx);
                    }
                ],
                'emptyCart' => [
                    'type' => Type::boolean (),
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_emptyCart ($root, $args, $ctx);
                    }
                ],
                'sendOTP' => [
                    'type' => self::$ResponseType,
                    'args' => [
                        'country_code' => Type::nonNull (Type::string ()),
                        'phone_number' => Type::nonNull (Type::string ()),
                        'purpose' =>  Type::string (),
                        'via' =>  Type::string ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_sendOTP ($root, $args, $ctx);
                    }
                ],
                'verifyOTP' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'country_code' => Type::nonNull (Type::string ()),
                        'phone_number' => Type::nonNull (Type::string ()),
                        'token' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_verifyOTP ($root, $args, $ctx);
                    }
                ],
                'sendForgetPasswordSMS' => [
                    'type' => self::$ResponseType,
                    'args' => [
                        'country_code' => Type::nonNull (Type::string ()),
                        'phone_number' => Type::nonNull (Type::string ()),
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_sendForgetPassword ($root, $args, $ctx);
                    }
                ],
                'loginByMobileNumberOTP' => [
                    'type' => self::$ResponseType,
                    'args' => [
                        'country_code' => Type::nonNull (Type::string ()),
                        'phone_number' => Type::nonNull (Type::string ()),
                        'token' => Type::nonNull (Type::string ())
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_loginByMobileNumberOTP ($root, $args, $ctx);
                    }
                ],
                'changeOrderStatus' => [
                    'type' => Type::boolean (),
                    'args' => [
                        'order_id' => Type::nonNull (Type::id ()),
                        'status' => Type::nonNull (self::$OrderStatusEnumType),
                        'note' => Type::nonNull (Type::string ()),
                        'transactionID' => Type::nonNull (Type::string ()),
                        'sendEmail' => Type::boolean ()
                    ],
                    'resolve' => function ($root, $args, $ctx) {
                        return self::$resolvers->MutationType_changeOrderStatus ($root, $args, $ctx);
                    }
                ]
            ]; }
        ]);

        self::$schema = new Schema ([
            'query' => self::$RootQueryType,
            'mutation' => self::$MutationType
        ]);

    }

    public static function ResolversInstance()
    {
        self::Instance();
        return self::$resolvers;
    }

    public static function Instance()
    {
        if (!isset (static::$types)) static::$types = new Types ();
        return static::$types;
    }

    private function __clone()
    {
    }
}

?>
