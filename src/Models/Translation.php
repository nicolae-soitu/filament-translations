<?php

namespace TomatoPHP\FilamentTranslations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\TranslationLoader\LanguageLine;

class Translation extends LanguageLine
{
    use HasFactory;
    use SoftDeletes;

    public $translatable = ['text'];

    /** @var array */
    public $guarded = ['id'];

    /** @var array */
    protected $casts = [
        'text' => 'array',
        'is_allowed' => 'boolean',
        'need_atention' => 'boolean',
        'found_in' => 'array',
        'source' => 'string',
        'allow_automatical_translation' => 'boolean',
        'context_for_ai' => 'string',
        'description' => 'string',
        'need_atention' => 'boolean',
    ];

    protected $table = 'language_lines';

    protected $fillable = [
        'group',
        'key',
        'text',
        'namespace',
        'is_allowed',
        'found_in',
        'source',
        'allow_automatical_translation',
        'context_for_ai',
        'description',
        'need_atention',
    ];

    public static function getTranslatableLocales(): array
    {
        return config('filament-translations.locals');
    }

    public function getTextByLocale(string $locale){
        return $this->text[$locale]??'';
    }

    public function getTranslation(string $locale, ?string $group = null): string
    {
        if ($group === '*' && ! isset($this->text[$locale])) {
            $fallback = config('app.fallback_locale');

            return $this->text[$fallback] ?? $this->key;
        }

        return $this->text[$locale] ?? '';
    }

    public function setTranslation(string $locale, string $value): self
    {
        $this->text = array_merge($this->text ?? [], [$locale => $value]);

        return $this;
    }

    protected function getTranslatedLocales(): array
    {
        return array_keys($this->text);
    }
}
