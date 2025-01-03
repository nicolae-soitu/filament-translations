<?php

namespace TomatoPHP\FilamentTranslations\Filament\Resources\TranslationResource\Table;

use Filament\Tables\Columns\Column;
use Filament\Tables\Table;


class TranslationTable
{
    protected static array $columns = [];

    public static function make(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->bulkActions(TranslationBulkActions::make())
            ->actions(TranslationActions::make())
            ->filters(TranslationFilters::make())
            ->headerActions(TranslationHeaderActions::make())
            ->deferLoading()
            ->defaultSort('key')
            ->columns(self::getColumns());
    }

    public static function getDefaultColumns(): array
    {
        return [
            \Filament\Tables\Columns\IconColumn::make('need_atention')
                ->boolean()    
                ->sortable()
                ->label('!')
                ->tooltip(trans('filament-translations::translation.need_atention_description'))
                ->alignment('center'),
            Columns\Key::make(),
            Columns\Text::make(),
            \Filament\Tables\Columns\TextColumn::make('source')
                ->sortable()
                ->label(trans('filament-translations::translation.field.source'))
                ->toggleable(),
            \Filament\Tables\Columns\TextColumn::make('description')
                ->sortable()
                ->label(trans('filament-translations::translation.field.description'))
                ->toggleable(isToggledHiddenByDefault: true),
            \Filament\Tables\Columns\TextColumn::make('context_for_ai')
                ->sortable()
                ->label(trans('filament-translations::translation.field.context_for_ai'))
                ->tooltip(trans('filament-translations::translation.field.context_for_ai_description'))
                ->toggleable(),
            \Filament\Tables\Columns\ToggleColumn::make('allow_automatical_translation')
                ->sortable()
                ->label(trans('filament-translations::translation.field.allow_automatical_translation'))
                ->tooltip(trans('filament-translations::translation.field.allow_automatical_translation_description'))
                ->toggleable(),
            \Filament\Tables\Columns\ToggleColumn::make('is_allowed')
                ->sortable()
                ->label(trans('filament-translations::translation.field.is_allowed'))
                ->tooltip(trans('filament-translations::translation.field.is_allowed_description'))
                ->toggleable(isToggledHiddenByDefault: true),
            \Filament\Tables\Columns\IconColumn::make('is_imported')
                ->boolean()    
                ->sortable()
                ->label(trans('filament-translations::translation.field.is_imported'))
                ->tooltip(trans('filament-translations::translation.field.is_imported_description'))
                ->alignment('center')
                ->toggleable(isToggledHiddenByDefault: true),
            Columns\CreatedAt::make(),
            Columns\UpdatedAt::make(),
        ];
    }

    private static function getColumns(): array
    {
        return array_merge(self::getDefaultColumns(), self::$columns);
    }

    public static function register(Column | array $column): void
    {
        if (is_array($column)) {
            foreach ($column as $item) {
                if ($item instanceof Column) {
                    self::$columns[] = $item;
                }
            }
        } else {
            self::$columns[] = $column;
        }
    }
}
