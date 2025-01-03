<?php

namespace TomatoPHP\FilamentTranslations\Translators\Filament;

use Filament\Actions\Action;
use TomatoPHP\FilamentTranslations\Translators\Openai\OpenaiTranslator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;          
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationBulkActions;
use Filament\Tables\Actions\Action as TableAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationActions;
use TomatoPHP\FilamentTranslations\Models\Translation;
class ModalAction     
{
    public static $title = '';
    public static $icon = 'heroicon-o-light-bulb';
    public static $tooltip = '';
    public static $color = 'danger';
    public static $translateButton = 'Translate';
    public static $translator;
    public static $chunk_size = 50;

    public static function make(): Action
    {   
        $locals = [];
        foreach(config('filament-translations.locals') as $key => $local){
            $locals[$key] = $local['label'];
        }

        TranslationBulkActions::register([
            BulkAction::make(class_basename(static::class).'_translate')
                ->label(static::$translateButton)
                ->requiresConfirmation()
                ->icon(static::$icon)
                ->color(static::$color)
                ->form([
                    Select::make('from')
                        ->searchable()
                        ->options($locals)
                        ->default(config('filament-translations.default_locale_for_translation','en'))
                        ->label(trans('filament-translations::translation.from_language'))
                        ->required(),
                    Select::make('to')
                        ->options($locals)
                        ->label(trans('filament-translations::translation.to_language'))
                        ->required()
                ])
                ->action(function (Collection $records, array $data) use ($locals) {
                    $data['witch_words'] = 'all';
                    $data['translate_key'] = 'yes';
                    // dd($records,static::$translator, $data);
                    // static::$translator::dispatch($records, $data);
                    foreach($records->chunk(static::$chunk_size) as $chunk){
                        $translation = (new static::$translator)->translateBulk($chunk, $data);
                    }

                    Notification::make()
                        ->title(trans('filament-translations::translation.notification.done.bulk', ['from' => $locals[$data['from']], 'to' => $locals[$data['to']]]))
                        ->success()
                        ->send();
                })
        ]);

        TranslationActions::register([
            TableAction::make(class_basename(static::class).'_translate_record')
                ->label('')
                ->tooltip(static::$translateButton)
                ->requiresConfirmation()
                ->icon(static::$icon)
                ->color(static::$color)
                ->form([
                    Select::make('from')
                        ->searchable()
                        ->options($locals)
                        ->default(config('filament-translations.default_locale_for_translation','en'))
                        ->label(trans('filament-translations::translation.from_language'))
                        ->required(),
                    Select::make('to')
                        ->options($locals)
                        ->label(trans('filament-translations::translation.to_language'))
                        ->required()
                ])
                ->action(function (Translation $records, array $data) use ($locals) {
                    $data['witch_words'] = 'all';
                    $data['translate_key'] = 'yes';
                    $translation = (new static::$translator)->translateBulk([$records], $data);
                    Notification::make()
                        ->title(trans('filament-translations::translation.notification.done.one', ['from' => $locals[$data['from']], 'to' => $locals[$data['to']]]))
                        ->success()
                        ->send();
                })
        ]);

        
        return Action::make(class_basename(static::class))
            ->requiresConfirmation()
            ->icon(static::$icon)
            ->hiddenLabel()
            ->tooltip(trans(static::$tooltip))
            ->color(static::$color)
            ->form([
                Select::make('from')
                    ->searchable()
                    ->options($locals)
                    ->default(config('filament-translations.default_locale_for_translation','en'))
                    ->label(trans('filament-translations::translation.from_language'))
                    ->required(),
                Select::make('to')
                    ->options($locals)
                    ->label(trans('filament-translations::translation.to_language'))
                    ->required(),
                Radio::make('witch_words')
                    ->options([
                        'all' => 'All phrases',
                        'only_missing' => 'Only missing phrases',
                    ])
                    ->default('only_missing')
                    ->descriptions([
                        'all' => 'All words in this language, doesn\'t matter if they are translated or not',
                        'only_missing' => 'Only phrases that are not translated in this language',
                    ])
                    ->inline(),
                Radio::make('translate_key')    
                    ->options([
                        'yes' => __('Yes'),
                        'no' => __('No'),
                    ])
                    ->descriptions([
                        'yes' => 'If the word is missing in the selected language, the key phrase will be translated from the key',
                        'no' => 'The phrase will not be translated from key if the translation base is missing',
                    ])
                    ->default('no')
                    ->label('Translate key')
                    ->inline()
            ])
            ->action(function (array $data) {
                // dd($data);
                static::$translator::dispatchAll($data);

                // Notification::make()
                //     ->title(trans(self::$languagePrefix.'.gpt_scan_notification_start'))
                //     ->success()
                //     ->send();
            });
    }
}