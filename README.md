![Screenshot of Login](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/3x1io-tomato-translations.jpg)

# Filament Translations Manager

Manage your translation with DB and cache, you can scan your languages tags like `trans()`, `__()`, and get the string inside and translate them use UI.

this plugin is build in [spatie/laravel-translation-loader](https://github.com/spatie/laravel-translation-loader)

This package is a fork of [tomatophp/filament-translations](https://github.com/tomatophp/filament-translations) with enhanced features:

- Built-in automatic translation using Google Translate and ChatGPT
- Bulk translation capabilities (all phrases, selected phrases, or individual phrases)
- Configurable AI model selection in config file
- Improved prompts for better multi-language support beyond English
- Extensible translator system - ability to add custom translators in config

[![Dependabot Updates](https://github.com/nicolae-soitu/filament-translations/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/nicolae-soitu/filament-translations/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/nicolae-soitu/filament-translations/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/nicolae-soitu/filament-translations/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/nicolae-soitu/filament-translations/actions/workflows/tests.yml/badge.svg)](https://github.com/nicolae-soitu/filament-translations/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/nicolae-soitu/filament-translations/version.svg)](https://packagist.org/packages/nicolae-soitu/filament-translations)
[![License](https://poser.pugx.org/nicolae-soitu/filament-translations/license.svg)](https://packagist.org/packages/nicolae-soitu/filament-translations)
[![Downloads](https://poser.pugx.org/nicolae-soitu/filament-translations/d/total.svg)](https://packagist.org/packages/nicolae-soitu/filament-translations)





## Screenshots

![Screenshot of list](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/translations-list.png)
![Screenshot of create](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/create.png)
![Screenshot of edit](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/edit.png)
![Screenshot of scan](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/scan.png)
![Screenshot of import](https://raw.githubusercontent.com/tomatophp/filament-translations/master/arts/import.png)

## Installation

```bash
composer require nicolae-soitu/filament-translations
```

now run install command

```bash
php artisan filament-translations:install
```

Finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
$panel->plugin(\TomatoPhp\FilamentTranslations\FilamentTranslationsPlugin::make())
```

### Allow Create Button to Create New Language

If you want to allow the user to create a new language, you need to add the following to your panel provider:

```php
$panel->plugin(\TomatoPhp\FilamentTranslations\FilamentTranslationsPlugin::make()->allowCreate())
```

### Allow Clear All Translations Button

If you want to allow the user to clear all translations, you need to add the following to your panel provider:

```php
$panel->plugin(\TomatoPhp\FilamentTranslations\FilamentTranslationsPlugin::make()->allowClearTranslations())
```

## Use Language Switcher

we move language switcher to another package you can check it [Filament Language Switcher](https://github.com/tomatophp/filament-language-switcher/)


## Scan Using Command Line

You can scan your project to get all the languages tags and save them to the database

```bash
php artisan filament-translations:import
```

## Configuring Translators

To use automatic translation, make sure you have the following settings in your `config/filament-translations.php` configuration file:

```php
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
            'handler' =>  TomatoPhp\FilamentTranslations\Translators\Google\GoogleTranslator::class, 
            'chunk_size' => 100,
        ],
        'openai' => [
            'allowed' => true,
            'handler' =>  \TomatoPhp\FilamentTranslations\Translators\Openai\OpenaiTranslator::class,
            'chunk_size' => 50,
            'model' => 'gpt-3.5-turbo',
            'system_prompt' => 'You are a translator. Your job is to translate the following json object to the language specified in the prompt.',
            'user_prompt' => 'Translate the following json object from :from to :language, ensuring you return only the translated content in JSON format without added quotes or any other extraneous details and dont change the keys. Importantly, any word prefixed with the symbol ":" should remain unchanged and should not be translated the key "context" should be used to understand the meaning of the phrase',
        ],
    ],
```

## Change Scan to work on Queue

In your config file just change the `use_queue_on_scan` to `true`

```php

'use_queue_on_scan' => true,

```

## Custom Import Command

You can create your own command to import the translations, add your custom import class to the config file like this:

```php
'path_to_custom_import_command' => ImportTranslations::class,
```

This command will automatically run when you click on the "Scan For New Languages" button in the UI.

## Custom Excel Import

You can create your own Excel import to import the translations, add your custom import class to the config file like this:

```php
'path_to_custom_excel_import' => CustomTranslationImport::class,
```

The import class is based on the Laravel Excel package.
You can check the documentation [here](https://docs.laravel-excel.com/3.1/imports/).
This import will automatically run when you click on the "Import" button in the UI.

## Custom Excel Export

You can create your own Excel export to export the translations in your own format, add your custom export class to the config file like this:

```php
'path_to_custom_excel_export' => CustomTranslationExport::class,
```

The export class is based on the Laravel Excel package.
You can check the documentation [here](https://docs.laravel-excel.com/3.1/imports/).
This import will automatically run when you click on the "Export" button in the UI.

## Show or hide buttons in the UI

You can show or hide the buttons in the UI by changing the config file. By default, all buttons are shown.

```php
'show_import_button' => true,
'show_export_button' => false,
'show_scan_button' => false ,
```

## Custom Resource

You can create your own resource to show the translations in the UI, add your custom resource class to the config file like this:

```php
'translation_resource' => CustomResource::class,
```

This is especially useful when you want to have complete control over the UI but still want to use the translations package. Think about implementing a check on user roles when using `shouldRegisterNavigation` in your custom resource.

## Translation Translations Resource Hooks

we have add a lot of hooks to make it easy to attach actions, columns, filters, etc

### Table Columns

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationTable;

public function boot()
{
    TranslationTable::register([
        \Filament\Tables\Columns\TextColumn::make('something')
    ]);
}
```

### Table Actions

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationActions;

public function boot()
{
    TranslationActions::register([
        \Filament\Tables\Actions\ReplicateAction::make()
    ]);
}
```

### Table Filters

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationFilters;

public function boot()
{
    TranslationFilters::register([
        \Filament\Tables\Filters\SelectFilter::make('something')
    ]);
}
```

### Table Bulk Actions

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Table\TranslationBulkActions;

public function boot()
{
    TranslationBulkActions::register([
        \Filament\Tables\BulkActions\DeleteAction::make()
    ]);
}
```

### From Components

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Form\TranslationForm;

public function boot()
{
    TranslationForm::register([
        \Filament\Forms\Components\TextInput::make('something')
    ]);
}
```

### Page Actions

```php
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ManagePageActions;
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Actions\EditPageActions;
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Actions\ViewPageActions;
use TomatoPhp\FilamentTranslations\Filament\Resources\TranslationResource\Actions\CreatePageActions;

public function boot()
{
    ManagePageActions::register([
        Filament\Actions\Action::make('action')
    ]);
    
    EditPageActions::register([
        Filament\Actions\Action::make('action')
    ]);
    
    ViewPageActions::register([
        Filament\Actions\Action::make('action')
    ]);
    
    CreatePageActions::register([
        Filament\Actions\Action::make('action')
    ]);
}
```

## Publish Assets

You can publish config file by use this command:

```bash
php artisan vendor:publish --tag="filament-translations-config"
```

You can publish views file by use this command:

```bash
php artisan vendor:publish --tag="filament-translations-views"
```

You can publish languages file by use this command:

```bash
php artisan vendor:publish --tag="filament-translations-lang"
```

You can publish migrations file by use this command:

```bash
php artisan vendor:publish --tag="filament-translations-migrations"
```


## Testing

if you like to run `PEST` testing just use this command

```bash
composer test
```

## Code Style

if you like to fix the code style just use this command

```bash
composer format
```

## PHPStan

if you like to check the code by `PHPStan` just use this command

```bash
composer analyse
```

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
