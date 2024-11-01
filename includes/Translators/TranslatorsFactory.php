<?php

namespace WCGQL\Translators;

class TranslatorsFactory
{
    /** @var string[] */
    private static $translators = array(
        'WPMLTranslator',
        'QTranslateTranslator',
        'DefaultTranslator',
    );

    /** @return ITranslator */
    public static function get_translator()
    {
        foreach (self::$translators as $translator) {
            $class = __NAMESPACE__ . '\\' . $translator;
            if (call_user_func(array($class, 'is_available'))) {
                return new $class;
            }
        }
    }
}
