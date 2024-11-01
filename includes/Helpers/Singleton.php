<?php

namespace WCGQL\Helpers;

class Singleton
{
    private static $instances;

    protected function __construct()
    {
    }

    public static function instance()
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }

        return self::$instances[$class];
    }

    final private function __clone()
    {
    }
}
