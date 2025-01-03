<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | add path that will be show to the scaner to catch lanuages tags
    |
    */
    'paths' => [
        app_path(),
        resource_path('views'),
        base_path('vendor'),
        base_path('Modules'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded paths
    |--------------------------------------------------------------------------
    |
    | Put here any folder that you want to exclude that is inside of paths
    |
    */

    'excludedPaths' => [],

    /*
    |--------------------------------------------------------------------------
    | Locals
    |--------------------------------------------------------------------------
    |
    | add the locals that will be show on the languages selector
    |
    */
    'locals' => [
        'en' => [
            'label' => 'English',
            'flag' => 'us',
        ],
        'ro' => [
            'label' => 'Română',
            'flag' => 'ro',
        ],
        'ru' => [
            'label' => 'Русский',
            'flag' => 'ru',
        ],
        // 'pt_BR' => [
        //     'label' => 'Português (Brasil)',
        //     'flag' => 'br',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    |
    | use simple modal resource for the translation resource
    |
    */
    'modal' => true,

    /*
    |--------------------------------------------------------------------------
    |
    | Add groups that should be excluded in translation import from files to database
    |
    */
    'exclude_groups' => [],

    /*
     |--------------------------------------------------------------------------
     |
     | Register the navigation for the translations.
     |
     */
    'register_navigation' => true,

    /*
     |--------------------------------------------------------------------------
     |
     | Use Queue to scan the translations.
     |
     */
    'use_queue_on_scan' => true,

    /*
     |--------------------------------------------------------------------------
     |
     | Custom import command.
     |
     */
    'path_to_custom_import_command' => null,

    /*
     |--------------------------------------------------------------------------
     |
     | Show buttons in Translation resource.
     |
     */
    'scan_enabled' => true,
    'export_enabled' => true,
    'import_enabled' => true,

    /*
     |--------------------------------------------------------------------------
     |
     | Translation resource.
     |
     */
    'translation_resource' => \NicolaeSoitu\FilamentTranslations\Filament\Resources\TranslationResource::class,

    /*
     |--------------------------------------------------------------------------
     |
     | Custom Excel export.
     |
     */
    'path_to_custom_excel_export' => null,

    /*
     |--------------------------------------------------------------------------
     |
     | Custom Excel import.
     |
     */
    'path_to_custom_excel_import' => null,
    /*
     |--------------------------------------------------------------------------
     |
     | Translate only empty words.
     |
     */
    'only_empty_words' => true,

    /*
     |--------------------------------------------------------------------------
     |
     | Default locale for translation.
     |
     */
    'default_locale_for_translation' => 'en',

    /*
     |--------------------------------------------------------------------------
     |
     | Translators.
     |
     */
    'translators' => [
        'google' => [
            'allowed' => true,
            'handler' =>  NicolaeSoitu\FilamentTranslations\Translators\Google\GoogleTranslator::class, 
            'chunk_size' => 100,
        ],
        'openai' => [
            'allowed' => true,
            'handler' =>  \NicolaeSoitu\FilamentTranslations\Translators\Openai\OpenaiTranslator::class,
            'chunk_size' => 50,
            'model' => 'gpt-3.5-turbo',
            'system_prompt' => 'You are a translator. Your job is to translate the following json object to the language specified in the prompt.',
            'user_prompt' => 'Translate the following json object from :from to :language, ensuring you return only the translated content in JSON format without added quotes or any other extraneous details and dont change the keys. Importantly, any word prefixed with the symbol ":" should remain unchanged and should not be translated the key "context" should be used to understand the meaning of the phrase',
        ],
    ],
];
