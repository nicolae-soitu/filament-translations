<?php

namespace NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Actions\Components;

abstract class Action
{
    abstract public static function make(): \Filament\Actions\Action;
}
