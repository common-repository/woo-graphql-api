<?php
namespace WCGQL\Translators;

interface ITranslation
{
    public function getTranslationId();

    public function getElementId();

    public function getLanguageCode();

    public function getSourceLanguageCode();

    public function getElementType();

    public function IsOriginal();
}
