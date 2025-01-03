<?php

namespace TomatoPHP\FilamentTranslations\Translators\Google;

use TomatoPHP\FilamentTranslations\Translators\Filament\ModalAction;
use TomatoPHP\FilamentTranslations\Translators\GoogleTranslator;

class GoogleTranslateAction extends ModalAction
{
    public static $title = 'Google Translation';
    public static $icon = 'heroicon-o-language';
    public static $tooltip = 'filament-translations::translation.google_scan';
    public static $color = 'info';  
    public static $translator = GoogleTranslator::class;
    public static $translateButton = 'Translate with Google';
    public static $chunk_size = 25;
}

