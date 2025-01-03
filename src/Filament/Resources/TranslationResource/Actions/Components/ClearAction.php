<?php

namespace NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource\Actions\Components;

use Filament\Actions;
use Filament\Notifications\Notification;
use NicolaeSoitu\FilamentTranslations\Models\Translation;

class ClearAction extends Action
{
    public static function make(): Actions\Action
    {
        return Actions\Action::make('clear')
            ->requiresConfirmation()
            ->icon('heroicon-o-trash')
            ->hiddenLabel()
            ->tooltip(trans('filament-translations::translation.clear'))
            ->action(function () {
                Translation::query()->truncate();

                Notification::make()
                    ->title(trans('filament-translations::translation.clear_notifications'))
                    ->success()
                    ->send();
            })
            ->color('danger')
            ->label(trans('filament-translations::translation.clear'));
    }
}
