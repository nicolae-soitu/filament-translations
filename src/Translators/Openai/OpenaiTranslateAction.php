<?php

namespace TomatoPHP\FilamentTranslations\Translators\Openai;

use TomatoPHP\FilamentTranslations\Translators\Filament\ModalAction;
// use TomatoPHP\FilamentTranslations\Translators\Openai\OpenaiTranslator;

class OpenaiTranslateAction extends ModalAction 
{
    public static $title = 'OpenAI Translate';
    public static $icon = 'heroicon-o-light-bulb';
    public static $tooltip = 'filament-translations::translation.google_scan';
    public static $color = 'success';   
    public static $translator = OpenaiTranslator::class;
    public static $translateButton = 'Translate with OpenAI';
    public static $chunk_size = 50;
}