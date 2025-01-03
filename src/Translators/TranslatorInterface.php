<?php

namespace NicolaeSoitu\FilamentTranslations\Translators;

interface TranslatorInterface
{
    public static function make();
    public static function dispatchAll($data);
    public function translateBulk($translations, $language);
    
}
