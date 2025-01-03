<?php

namespace TomatoPHP\FilamentTranslations\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\TranslationLoader\LanguageLine;

class CustomTranslationExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return LanguageLine::all();
    }

    public function map($row): array
    {
        $exportArray = [
            $row->id,
            $row->group,
            $row->key,
        ];

        $locales = config('filament-translations.locals');
        foreach ($locales as $key => $value) {
            $exportArray[] = $row->text[$key] ?? null;
        }

        return $exportArray;
    }

    public function headings(): array
    {
        $headers = [
            'id',
            'group',
            'key',
        ];

        $locales = config('filament-translations.locals');
        foreach ($locales as $key => $value) {
            $headers[] = $key;
        }

        return $headers;
    }
}
