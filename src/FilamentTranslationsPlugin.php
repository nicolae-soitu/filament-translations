<?php

namespace TomatoPHP\FilamentTranslations;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Actions\Components\ClearAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Actions\Components\CreateAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Actions\Components\ScanAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ManagePageActions;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\HeaderActions\ExportAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\HeaderActions\ImportAction;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationHeaderActions;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationBulkActions;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationFilters;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use TomatoPHP\FilamentTranslations\Models\Translation;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;    
use Filament\Forms\Components\Radio;    
use TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Form\TranslationForm;

class FilamentTranslationsPlugin implements Plugin
{
    public bool $allowClearTranslations = false;

    public bool $allowCreate = false;

    public function getId(): string
    {
        return 'filament-translations';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            config('filament-translations.translation_resource'),
        ]);
    }

    public function allowClearTranslations(bool $allowClearTranslations = true): self
    {
        $this->allowClearTranslations = $allowClearTranslations;

        return $this;
    }

    public function allowCreate(bool $allowCreate = true): self
    {
        $this->allowCreate = $allowCreate;

        return $this;
    }

    public function boot(Panel $panel): void
    {
        
        if(is_array(config('filament-translations.translators'))){
            foreach (config('filament-translations.translators') as $translator) {
                if ($translator['allowed']) {
                    $translator['handler']::make();
                }
            }
        }
        if (config('filament-translations.import_enabled')) {
            TranslationHeaderActions::register(ImportAction::make());
        }

        if (config('filament-translations.export_enabled')) {
            TranslationHeaderActions::register(ExportAction::make());
        }

        if (config('filament-translations.scan_enabled')) {
            ManagePageActions::register(ScanAction::make());
        }

        if (filament('filament-translations')->allowClearTranslations) {
            ManagePageActions::register(ClearAction::make());
        }

        if (filament('filament-translations')->allowCreate) {
            ManagePageActions::register(CreateAction::make());
        }
        TranslationForm::register([
            Textarea::make('context_for_ai')
                ->label(trans('filament-translations::translation.context_for_ai'))
                ->columnSpanFull(),
            Textarea::make('description')
                ->label(trans('filament-translations::translation.description'))
                ->columnSpanFull(),
            Radio::make('is_allowed')
                ->label(trans('filament-translations::translation.is_allowed'))
                ->boolean()
                ->default(true)
                ->inline()
                ->inlineLabel(false),
            Radio::make('allow_automatical_translation')
                ->label(trans('filament-translations::translation.allow_automatical_translation'))
                ->boolean()
                ->default(true)
                ->inline()
                ->inlineLabel(false),
            Radio::make('need_atention')
                ->label(trans('filament-translations::translation.need_atention'))
                ->boolean()
                ->default(false)
                ->inline()
                ->inlineLabel(false),
            TextInput::make('source')
                ->label(trans('filament-translations::translation.source'))
                ->disabled()
                ->default('manual'),
            Textarea::make('found_in')
                ->label(trans('filament-translations::translation.found_in'))
                ->disabled()
                ->columnSpanFull(),
        ]);
        TranslationFilters::register([
            SelectFilter::make('source')
                ->options(Translation::query()->groupBy('source')->pluck('source', 'source')->all()),
            TernaryFilter::make('allow_automatical_translation')
                ->label('Allowed to automatical translation'),
            TernaryFilter::make('is_allowed')
                ->label('Allowed'),
        ]);
    }

    public static function make(): FilamentTranslationsPlugin
    {
        return new FilamentTranslationsPlugin;
    }
}
