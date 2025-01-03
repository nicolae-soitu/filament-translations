<?php

namespace NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\InfoList\Entries;

abstract class Entry
{
    abstract public static function make(): \Filament\Infolists\Components\Entry;
}
