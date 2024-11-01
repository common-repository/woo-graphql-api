<?php
namespace WCGQL\Translators;

class WPMLTranslation implements ITranslation
{
    public function __construct($translation)
    {
        $this->translation_id = $translation->translation_id;
        $this->element_id = $translation->element_id;
        $this->language_code = $translation->language_code;
        $this->source_language_code = $translation->source_language_code;
        $this->element_type = $translation->element_type;
        $this->original = $translation->original;
    }

    public function getTranslationId()
    {
        return $this->translation_id;
    }

    public function getElementId()
    {
        return $this->element_id;
    }

    public function getLanguageCode()
    {
        return $this->language_code;
    }

    public function getSourceLanguageCode()
    {
        return $this->source_language_code;
    }

    public function getElementType()
    {
        return $this->element_type;
    }

    public function IsOriginal()
    {
        return $this->original;
    }
}
